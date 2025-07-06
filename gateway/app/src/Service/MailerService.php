<?php

declare(strict_types=1);

namespace App\Service;

use App\Message\EmailMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class MailerService
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function publishMail(EmailMessage $emailMessage): void
    {
        $this->messageBus->dispatch(
            $emailMessage,
            [new AmqpStamp('mailer.send')],
        );
    }
}
