<?php

namespace App\Controller;

use App\Domain\Validator\Request\User\ValidatorUserRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Command\CreateUser\CreateUserCommand;
use App\Application\Command\UpdateUser\UpdateUserCommand;
use App\Application\Command\DeleteUser\DeleteUserCommand;
use App\Application\Query\GetAllUsers\GetAllUsersQuery;
use App\Application\Query\GetUserFromEmail\GetUserFromEmailQuery;
use App\Message\SendEmail;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Exception;

class UserController
{
    private $validator;

    public function __construct(ValidatorUserRequestInterface $validator)
    {
        $this->validator = $validator;
    }

    public function addUser(Request $request, MessageBusInterface $bus): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$this->validator->validateAddUserRequest($data)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $user = User::createUser();
            $user = $user->buildUserFromArray($user, $data);

            $result = $bus->dispatch(new CreateUserCommand($user))->last(HandledStamp::class)->getResult();
            $status = ($result['error']) ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED;

            if(!$result['error']) {
                $msg = 'User created with id ' . $result['id'];
                $subject = 'User created';

                $bus->dispatch(new SendEmail($msg, $subject));
            }

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateUser(string $email, Request $request, MessageBusInterface $bus): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$this->validator->validateUpdateUserRequest($data)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $bus->dispatch(new UpdateUserCommand($email, $data))
                ->last(HandledStamp::class)->getResult();
            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            if(!$result['error']) {
                $msg = 'User updated with id '.$result['id'];
                $subject = 'User updated';

                $bus->dispatch(new SendEmail($msg, $subject));
            }

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteUser(string $email, MessageBusInterface $bus): JsonResponse
    {
        try {
            if (!$this->validator->validateDeleteUserRequest(['email' => $email])) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $bus->dispatch(new DeleteUserCommand($email))->last(HandledStamp::class)->getResult();
            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            if(!$result['error']) {
                $msg = 'User deleted with id ' . $result['id'];
                $subject = 'User deleted';

                $bus->dispatch(new SendEmail($msg, $subject));
            }

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllUsers(int $page, MessageBusInterface $queryBus): JsonResponse
    {
        try {
            if (!$this->validator->validateGetAllUsersRequest(['page' => $page])) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $queryBus->dispatch(new GetAllUsersQuery($page))->last(HandledStamp::class)->getResult();

            return new JsonResponse($result, Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getUserFromEmail(string $email, MessageBusInterface $queryBus): JsonResponse
    {
        try {
            if (!$this->validator->validateGetUserFromEmailRequest($email)) {
                return new JsonResponse(['error'=> 'bad params'], Response::HTTP_BAD_REQUEST);
            }

            $result = $queryBus->dispatch(new GetUserFromEmailQuery($email))
                ->last(HandledStamp::class)->getResult();

            $status = ($result['error']) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

            return new JsonResponse($result, $status);
        } catch (Exception $e) {
            return new JsonResponse(['error'=> $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
