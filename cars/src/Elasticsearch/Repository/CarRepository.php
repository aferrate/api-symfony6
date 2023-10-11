<?php

namespace App\Elasticsearch\Repository;

use App\Domain\Model\Car;
use App\Domain\Repository\CarRepositoryInterface;
use Elasticsearch\ClientBuilder;
use DateTime;

class CarRepository implements CarRepositoryInterface
{
    private $elasticClient;

    public function __construct()
    {
        $this->elasticClient = ClientBuilder::create()->setHosts([
            'host' => $_ENV['ELASTICSEARCH_DSN']
        ])->build();;
    }

    public function save(Car $domainCar): Car
    {
        $params = [
            'index' => 'cars',
            'id' => $domainCar->getId(),
            'body' => [
                'id' => $domainCar->getId(),
                'mark' => $domainCar->getMark(),
                'model' => $domainCar->getModel(),
                'year' => $domainCar->getYear(),
                'description' => $domainCar->getDescription(),
                'enabled' => $domainCar->getEnabled(),
                'createdAt' => $domainCar->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $domainCar->getUpdatedAt()->format('Y-m-d H:i:s'),
                'country' => $domainCar->getCountry(),
                'city' => $domainCar->getCity(),
                'imageFilename' => $domainCar->getImageFilename()
            ]
        ];

        $this->elasticClient->index($params);

        return $domainCar;
    }

    public function update(Car $domainCar): Car
    {
        $params = [
            'index' => 'cars',
            'id'    => $domainCar->getId(),
            'body'  => [
                'doc' => [
                    'mark' => $domainCar->getMark(),
                    'model' => $domainCar->getModel(),
                    'year' => $domainCar->getYear(),
                    'description' => $domainCar->getDescription(),
                    'enabled' => $domainCar->getEnabled(),
                    'updatedAt' => $domainCar->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'country' => $domainCar->getCountry(),
                    'city' => $domainCar->getCity(),
                    'imageFilename' => $domainCar->getImageFilename()
                ]
            ]
        ];

        $this->elasticClient->update($params);

        return $domainCar;
    }

    public function delete(Car $car): void
    {
        $params = [
            'index' => 'cars',
            'id' => $car->getId()
        ];

        $this->elasticClient->delete($params);
    }

    public function findAllCarsEnabled(int $page): array
    {
        $firstResult = ($page <= 0) ? 0 : $page * $_ENV['RESULTS_PER_PAGE'];
        $cars = [];

        $params = [
            'index' => 'cars',
            'from' => $firstResult,
            'size' => $_ENV['RESULTS_PER_PAGE'],
            'body' => [
                'query' => [
                    'match' => [
                        'enabled' => true
                    ]
                ],
                'sort' => [
                    'updatedAt' => [
                        'order' => 'desc'
                    ]
                ]
            ]
        ];

        $carsElastic = $this->elasticClient->search($params);

        foreach ($carsElastic['hits']['hits'] as $car) {
            $carDomain = new Car();
            $carDomain = $carDomain->buildCarFromArray($carDomain, $car['_source']);
            $carDomain->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $car['_source']['createdAt']));
            $carDomain->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $car['_source']['updatedAt']));
            $cars[] = $carDomain;
        }

        return $cars;
    }

    public function findAllCars(int $page): array
    {
        $firstResult = ($page <= 0) ? 0 : $page * $_ENV['RESULTS_PER_PAGE'];
        $cars = [];

        $params = [
            'index' => 'cars',
            'from' => $firstResult,
            'size' => $_ENV['RESULTS_PER_PAGE'],
            'body' => [
                'sort' => [
                    'updatedAt' => [
                        'order' => 'desc'
                    ]
                ]
            ]
        ];

        $carsElastic = $this->elasticClient->search($params);

        foreach ($carsElastic['hits']['hits'] as $car) {
            $carDomain = new Car();
            $carDomain = $carDomain->buildCarFromArray($carDomain, $car['_source']);
            $carDomain->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $car['_source']['createdAt']));
            $carDomain->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $car['_source']['updatedAt']));
            $cars[] = $carDomain;
        }

        return $cars;
    }

    public function findOneCarById(string $id): ?Car
    {
        $params = [
            'index' => 'cars',
            'body' => [
                'query' => [
                    'match_phrase_prefix' => [
                        'id' => $id
                    ]
                ]
            ]
        ];

        $carElastic = $this->elasticClient->search($params)['hits']['hits'];

        if (empty($carElastic)) {
            return null;
        }

        $car = new Car();
        $car = $car->buildCarFromArray($car, $carElastic[0]['_source']);
        $car->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $carElastic[0]['_source']['createdAt']));
        $car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $carElastic[0]['_source']['updatedAt']));

        return $car;
    }
}
