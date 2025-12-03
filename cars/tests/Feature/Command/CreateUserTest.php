<?php

namespace App\Tests\Feature\Command;

use App\Factory\UserRepoFactory;
use App\Services\CacheRedis;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateUserTest extends WebTestCase
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

    public function testCreateUser(): void
    {
        $client = $this->createAuthenticatedClient();
        $userReadRepo = $this::getContainer()->get(UserRepoFactory::class)->getUserReadRepo();
        $cache = $this::getContainer()->get(CacheRedis::class);

        $email = 'test2@test.com';

        $crawler = $client->request(
            'POST',
            '/api/v1/user/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
    '{
                "email" : "test2@test.com",
                "password" : "test2"
            }'
        );

        $response = $client->getResponse();

        sleep(10);

        $user = $userReadRepo->findOneByEmail($email);
        $userReadRepo->delete($user);
        $cache->deleteIndex('user_'.$user->getEmail());

        $this->assertSame(201, $response->getStatusCode());
    }
}
