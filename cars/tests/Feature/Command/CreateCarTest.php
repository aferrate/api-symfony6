<?php

namespace App\Tests\Feature\Command;

use App\Factory\CarRepoFactory;
use App\Services\CacheRedis;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateCarTest extends WebTestCase
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

    public function testCreateCar(): void
    {
        $client = $this->createAuthenticatedClient();
        $carReadRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarReadRepo();
        $cache = $this::getContainer()->get(CacheRedis::class);

        $crawler = $client->request(
        'POST',
        '/api/v1/car/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
    '{
                "mark" : "testApplication",
                "model" : "testApplication",
                "description" : "testApplication",
                "country" : "testApplication",
                "city" : "testApplication",
                "year" : 2002,
                "enabled" : true,
                "imageFilename": "testApplication.jpg"
            }'
        );

        $response = $client->getResponse();

        sleep(10);

        $id = json_decode($response->getContent(), true)['id'];
        $car = $carReadRepo->findOneCarById($id);
        $carReadRepo->delete($car);
        $cache->deleteIndex('car_'.$car->getId());

        $this->assertSame(200, $response->getStatusCode());
    }
}
