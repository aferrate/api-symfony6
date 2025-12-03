<?php

namespace App\Message;

class SendEmail
{
    private $msg;
    private $subject;

    public function __construct(string $msg, string $subject)
    {
        $this->msg = $msg;
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
}
