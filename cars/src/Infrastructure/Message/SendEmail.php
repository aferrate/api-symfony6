<?php

namespace App\Infrastructure\Message;

use App\Domain\Message\EmailMessageInterface;

class SendEmail implements EmailMessageInterface
{
    private $msg;
    private $subject;

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
}
