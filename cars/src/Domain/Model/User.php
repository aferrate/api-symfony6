<?php

namespace App\Domain\Model;

class User
{
    protected $id;
    protected $email;
    protected $password;

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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param User $user
     * @param array $arrayUser
     * @return User
     */
    public function buildUserFromArray(User $user, array $arrayUser): User
    {
        $user->setEmail($arrayUser['email']);
        $user->setPassword($arrayUser['password']);

        return $user;
    }

    public function toArray(): array
    {
        $userArray = [];
        $userArray['id'] = $this->getId();
        $userArray['email'] = $this->getEmail();
        $userArray['password'] = $this->getPassword();

        return $userArray;
    }
}
