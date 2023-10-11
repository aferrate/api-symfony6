<?php

namespace App\DataFixtures;

use App\Domain\Repository\CarRepositoryInterface;
use App\Entity\Car;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use DateTime;

class CarFixtures extends Fixture
{
    private $carRepoBackup;

    public function __construct(CarRepositoryInterface $carRepoBackup)
    {
        $this->carRepoBackup = $carRepoBackup;
    }

    public function load(ObjectManager $manager)
    {
        $car = new Car(Uuid::uuid4());
        $car->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        $car->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
        $car->setYear(2000);
        $car->setCity('test0');
        $car->setCountry('test0');
        $car->setDescription('test0');
        $car->setEnabled(true);
        $car->setImageFilename('test0.jpg');
        $car->setMark('test0');
        $car->setModel('test0');

        $manager->persist($car);
        $manager->flush();

        $this->carRepoBackup->save($car);
    }
}
