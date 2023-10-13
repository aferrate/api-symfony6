<?php

namespace App\Controller;

use App\Entity\Car;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Command\CreateCar\CreateCarCommand;
use App\Application\Command\UpdateCar\UpdateCarCommand;
use App\Application\Command\DeleteCar\DeleteCarCommand;
use App\Application\Query\GetAllCars\GetAllCarsQuery;
use App\Application\Query\GetAllCarsEnabled\GetAllCarsEnabledQuery;
use App\Application\Query\GetCarFromId\GetCarFromIdQuery;
use App\Message\SendEmail;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Exception;
use App\Domain\Validator\Request\Car\ValidatorCarRequestInterface;

class CarController
{
    private $validator;

    public function __construct(ValidatorCarRequestInterface $validator)
    {
        $this->validator = $validator;
    }

    public function addCar(Request $request, MessageBusInterface $bus): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$this->validator->validateAddCarRequest($data)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $car = Car::createCar();
            $data['id'] = $car->getId();
            $car = $car->buildCarFromArray($car, $data);

            $result = $bus->dispatch(new CreateCarCommand($car))->last(HandledStamp::class)->getResult();
            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            if(!$result['error']) {
                $msg = 'Car created with id '.$result['id'];
                $subject = 'Car created';

                $bus->dispatch(new SendEmail($msg, $subject));
            }

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateCar(string $id, Request $request, MessageBusInterface $bus): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$this->validator->validateUpdateCarRequest($data)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $bus->dispatch(new UpdateCarCommand($id, $data))
                ->last(HandledStamp::class)->getResult();
            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            if(!$result['error']) {
                $msg = 'Car updated with id ' . $result['id'];
                $subject = 'Car updated';

                $bus->dispatch(new SendEmail($msg, $subject));
            }

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteCar(string $id, MessageBusInterface $bus): JsonResponse
    {
        try {
            if (!$this->validator->validateDeleteCarRequest($id)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $bus->dispatch(new DeleteCarCommand($id))->last(HandledStamp::class)->getResult();
            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            if(!$result['error']) {
                $msg = 'Car deleted with id ' . $result['id'];
                $subject = 'Car deleted';

                $bus->dispatch(new SendEmail($msg, $subject));
            }

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllCars(int $page, MessageBusInterface $queryBus): JsonResponse
    {
        try {
            if (!$this->validator->validateGetAllCarsRequest($page)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $queryBus->dispatch(new GetAllCarsQuery($page))->last(HandledStamp::class)->getResult();

            return new JsonResponse($result, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllCarsEnabled(int $page, MessageBusInterface $queryBus): JsonResponse
    {
        try {
            if (!$this->validator->validateGetAllCarsEnabledRequest($page)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $queryBus->dispatch(new GetAllCarsEnabledQuery($page))->last(HandledStamp::class)->getResult();

            return new JsonResponse($result, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getCarFromId(string $id, MessageBusInterface $queryBus): JsonResponse
    {
        try {
            if (!$this->validator->validateGetCarFromIdRequest($id)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $queryBus->dispatch(new GetCarFromIdQuery($id))->last(HandledStamp::class)->getResult();

            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
