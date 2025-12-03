<?php

namespace App\Domain\Mail;

interface SendMailInterface
{
    public function sendMail(array $params): void;
}
