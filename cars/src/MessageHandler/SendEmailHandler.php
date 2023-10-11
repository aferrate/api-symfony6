<?php

namespace App\MessageHandler;

use App\Message\SendEmail;
use App\Domain\Factory\UserRepoFactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Domain\Mail\SendMailInterface;

class SendEmailHandler implements MessageHandlerInterface
{
    private $userReadRepo;
    private $mailer;

    public function __construct(UserRepoFactoryInterface $userRepoFactory, SendMailInterface $mailer)
    {
        $this->userReadRepo = $userRepoFactory->getUserReadRepo();
        $this->mailer = $mailer;
    }

    public function __invoke(SendEmail $sendEmail)
    {
        $users = $this->userReadRepo->getEmailUsers();

        foreach ($users as $user) {
            $this->mailer->sendMail([
                'email_from' => $_ENV['EMAIL_SENDER'],
                'email_to' => $user,
                'subject' => $sendEmail->getSubject(),
                'text' => $sendEmail->getMsg()
            ]);
        }
    }
}
