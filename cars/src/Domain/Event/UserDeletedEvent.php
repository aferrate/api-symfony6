<?php

namespace App\Domain\Event;

class UserDeletedEvent
{
    private $userId;
    private $email;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
