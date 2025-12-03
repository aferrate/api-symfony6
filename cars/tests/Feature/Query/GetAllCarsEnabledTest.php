<?php

namespace App\Tests\Feature\Query;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetAllCarsEnabledTest extends WebTestCase
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

    public function testGetAllCarsEnabled(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET','/api/v1/cars/enabled/page/0');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }
}
