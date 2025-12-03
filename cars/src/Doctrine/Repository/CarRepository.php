<?php

namespace App\Doctrine\Repository;

use App\Entity\Car;
use App\Domain\Model\Car as DomainCar;
use App\Domain\Repository\CarRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CarRepository extends ServiceEntityRepository implements CarRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    public function save(DomainCar $domainCar): DomainCar
    {
        $this->_em->persist($domainCar);
        $this->_em->flush();
        $this->_em->clear();

        return $domainCar;
    }

    public function update(DomainCar $domainCar): DomainCar
    {
        $car = $this->_em->getRepository(Car::class)->findOneBy(['id' => $domainCar->getId()]);
        $car->setMark($domainCar->getMark());
        $car->setModel($domainCar->getModel());
        $car->setDescription($domainCar->getDescription());
        $car->setCountry($domainCar->getCountry());
        $car->setCity($domainCar->getCity());
        $car->setImageFilename($domainCar->getImageFilename());
        $car->setYear($domainCar->getYear());
        $car->setEnabled($domainCar->getEnabled());
        $car->setUpdatedAt($domainCar->getUpdatedAt());

        return $this->save($car);
    }

    public function delete(DomainCar $car): void
    {
        $this->_em->remove($this->_em->getRepository(Car::class)->findOneBy(['id' => $car->getId()]));
        $this->_em->flush();
        $this->_em->clear();
    }

    public function findAllCarsEnabled(int $page): array
    {
        $firstResult = ($page <= 0) ? 0 : $page * $_ENV['RESULTS_PER_PAGE'];

        $qb = $this->_em->createQueryBuilder();

        $cars = $qb->select('c')
            ->from('App:Car', 'c')
            ->where('c.enabled = TRUE')
            ->setFirstResult($firstResult)
            ->setMaxResults($_ENV['RESULTS_PER_PAGE'])
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $cars;
    }

    public function findAllCars(int $page): array
    {
        $firstResult = ($page <= 0) ? 0 : $page * $_ENV['RESULTS_PER_PAGE'];

        $qb = $this->_em->createQueryBuilder();

        $cars = $qb->select('c')
            ->from('App:Car', 'c')
            ->setFirstResult($firstResult)
            ->setMaxResults($_ENV['RESULTS_PER_PAGE'])
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $cars;
    }

    public function findOneCarById(string $id): ?DomainCar
    {
        return $this->_em->getRepository(Car::class)->findOneBy(['id' => $id]);
    }
}
