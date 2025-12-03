<?php

namespace App\Domain\Model;

use DateTime;

class Car
{
    protected $id;
    protected $mark;
    protected $model;
    protected $description;
    protected $country;
    protected $city;
    protected $imageFilename;
    protected $year;
    protected $enabled;
    protected $createdAt;
    protected $updatedAt;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMark(): string
    {
        return $this->mark;
    }

    /**
     * @param string $mark
     */
    public function setMark(string $mark): void
    {
        $this->mark = $mark;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getImageFilename(): string
    {
        return $this->imageFilename;
    }

    /**
     * @param string $imageFilename
     */
    public function setImageFilename(string $imageFilename): void
    {
        $this->imageFilename = $imageFilename;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param Car $car
     * @param array $arrayCar
     * @return Car
     */
    public function buildCarFromArray(Car $car, array $arrayCar): Car
    {
        $car->setId($arrayCar['id']);
        $car->setMark($arrayCar['mark']);
        $car->setModel($arrayCar['model']);
        $car->setDescription($arrayCar['description']);
        $car->setCountry($arrayCar['country']);
        $car->setCity($arrayCar['city']);
        $car->setImageFilename($arrayCar['imageFilename']);
        $car->setYear($arrayCar['year']);
        $car->setEnabled($arrayCar['enabled']);

        return $car;
    }

    public function toArray(): array
    {
        $carArray = [];
        $carArray['id'] = $this->getId();
        $carArray['mark'] = $this->getMark();
        $carArray['model'] = $this->getModel();
        $carArray['country'] = $this->getCountry();
        $carArray['city'] = $this->getCity();
        $carArray['imageFilename'] = $this->getImageFilename();
        $carArray['year'] = $this->getYear();
        $carArray['enabled'] = $this->getEnabled();
        $carArray['createdAt'] = $this->getCreatedAt()->format('Y-m-d H:i:s');
        $carArray['updatedAt'] = $this->getUpdatedAt()->format('Y-m-d H:i:s');

        return $carArray;
    }
}
