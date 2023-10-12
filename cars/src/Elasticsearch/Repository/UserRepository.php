<?php

namespace App\Elasticsearch\Repository;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use Elasticsearch\ClientBuilder;

class UserRepository implements UserRepositoryInterface
{
    private $elasticClient;

    public function __construct()
    {
        $this->elasticClient = ClientBuilder::create()->setHosts([
            'host' => $_ENV['ELASTICSEARCH_DSN']
        ])->build();;
    }

    public function findOneByEmail(string $email): ?User
    {
        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'match_phrase_prefix' => [
                        'email' => $email
                    ]
                ]
            ]
        ];

        $userElastic = $this->elasticClient->search($params);

        if(empty($userElastic['hits']['hits'])) {
            return null;
        }

        $user = new User();
        $user = $user->buildUserFromArray($user, $userElastic['hits']['hits'][0]['_source']);
        $user->setId($userElastic['hits']['hits'][0]['_source']['id']);

        return $user;
    }

    public function checkEmailRepeated(string $email, string $id): ?User
    {
        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'bool' => [
                        'must_not' => [
                            'term' => [
                                'id' => $id
                            ]
                        ],
                        'must' => [
                            'match_phrase_prefix' => [
                                'email' => $email
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $userElastic = $this->elasticClient->search($params);

        if (empty($userElastic['hits']['hits'][0]['_source'])) {
            return null;
        }

        $user = new User();
        $user = $user->buildUserFromArray($user, $userElastic['hits']['hits'][0]['_source']);

        return $user;
    }

    public function save(User $user): string
    {
        $params = [
            'index' => 'users',
            'id' => $user->getId(),
            'body' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword()
            ]
        ];

        $this->elasticClient->index($params);

        return $user->getId();
    }

    public function update(User $domainUser): User
    {
        $params = [
            'index' => 'users',
            'id'    => $domainUser->getId(),
            'body'  => [
                'doc' => [
                    'email' => $domainUser->getEmail(),
                    'password' => $domainUser->getPassword()
                ]
            ]
        ];

        $this->elasticClient->update($params);

        return $domainUser;
    }

    public function delete(User $user): void
    {
        $params = [
            'index' => 'users',
            'id' => $user->getId()
        ];

        $this->elasticClient->delete($params);
    }

    public function findAllUsers(int $page): array
    {
        $firstResult = ($page <= 0) ? 0 : $page * $_ENV['RESULTS_PER_PAGE'];
        $users = [];

        $params = [
            'index' => 'users',
            'from' => $firstResult,
            'size' => $_ENV['RESULTS_PER_PAGE'],
        ];

        $usersElastic = $this->elasticClient->search($params);

        foreach ($usersElastic['hits']['hits'] as $userElastic) {
            $user = new User();
            $user->setId($userElastic['_source']['id']);
            $user->buildUserFromArray($user, $userElastic['_source']);
            $users[] = $user;
        }

        return $users;
    }

    public function getEmailUsers(): array
    {
        $users = [];

        $params = [
            'index' => 'users',
            'body' => [
                '_source' => ['email']
            ]
        ];

        $usersElastic = $this->elasticClient->search($params);

        if(empty($usersElastic['hits']['hits'][0]['_source'])) {
            return $users;
        }

        foreach ($usersElastic['hits']['hits'][0]['_source'] as $user) {
            $users[] = $user;
        }

        return $users;
    }
}
