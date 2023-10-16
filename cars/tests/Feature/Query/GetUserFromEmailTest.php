<?php

namespace App\Tests\Feature\Query;

use App\Entity\User;
use App\Factory\UserRepoFactory;
use App\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetUserFromEmailTest extends WebTestCase
{
    protected function createAuthenticatedClient($username = 'test@test.com', $password = 'test')
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['data']['token']));

        return $client;
    }

    public function testGetUserFromEmail(): void
    {
        $userReadRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserReadRepo();
        $userWriteRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserWriteRepo();
        $cache = $this::getContainer()->get(CacheRedis::class);

        $user = new User(Uuid::uuid4());
        $user->setEmail('testIntegration@test.com');
        $user->setPassword('testIntegration');

        $userWriteRepo->save($user);
        $userReadRepo->save($user);
        $cache->putIndex($user->toArray(), 'user_'.$user->getEmail());

        sleep(10);

        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET','/api/v1/user/email/'.$user->getEmail());

        $response = $client->getResponse();

        $userReadRepo->delete($user);
        $cache->deleteIndex('user_'.$user->getEmail());

        $this->assertSame(200, $response->getStatusCode());
    }
}