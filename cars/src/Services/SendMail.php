<?php

namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Domain\Mail\SendMailInterface;

class SendMail implements SendMailInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(array $params): void
    {
        $email = (new Email())
            ->from($params['email_from'])
            ->to($params['email_to'])
            ->subject($params['subject'])
            ->text($params['text']);

        $this->mailer->send($email);
    }
}
