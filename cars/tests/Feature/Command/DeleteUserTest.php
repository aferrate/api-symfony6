<?php

namespace App\Tests\Feature\Command;

use App\Entity\User;
use App\Factory\UserRepoFactory;
use App\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteUserTest extends WebTestCase
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

    public function testDeleteUser(): void
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
        $crawler = $client->request('DELETE','/api/v1/user/delete/'.$user->getEmail());

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }
}
