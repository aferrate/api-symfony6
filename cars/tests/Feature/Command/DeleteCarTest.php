<?php

namespace App\Tests\Feature\Command;

use App\Entity\Car;
use App\Factory\CarRepoFactory;
use App\Services\CacheRedis;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DateTime;

class DeleteCarTest extends WebTestCase
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

    public function testDeleteCar(): void
    {
        $carReadRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarReadRepo();
        $carWriteRepo = $this::getContainer()->get(CarRepoFactory::class)->getCarWriteRepo();
        $cache = $this::getContainer()->get(CacheRedis::class);

        $car = new Car(Uuid::uuid4());
        $car->setYear(2000);
        $car->setCity('testIntegration');
        $car->setCountry('testIntegration');
        $car->setDescription('testIntegration');
        $car->setEnabled(true);
        $car->setImageFilename('testIntegration.jpg');
        $car->setMark('testIntegration');
        $car->setModel('testIntegration');
        $car->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        $car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

        $carWriteRepo->save($car);
        $carReadRepo->save($car);
        $cache->putIndex($car->toArray(), 'car_'.$car->getId());

        sleep(10);

        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('DELETE','/api/v1/car/delete/'.$car->getId());

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
    }
}
