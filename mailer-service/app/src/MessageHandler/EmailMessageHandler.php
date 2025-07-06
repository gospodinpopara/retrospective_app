<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\EmailMessage;
use App\Service\MailerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsMessageHandler]
class EmailMessageHandler
{
    public function __construct(
        private readonly MailerService $mailerService
    ) {
    }

    /**
     * @param EmailMessage $emailMessage
     *
     * @return void
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(EmailMessage $emailMessage): void
    {
        $this->mailerService->sendEmail(
            to: $emailMessage->getToEmail(),
            subject: $emailMessage->getSubject(),
            templateName: $emailMessage->getTemplateName(),
            templateData: $emailMessage->getTemplateData(),
            from: $emailMessage->getFrom(),
            fromName: $emailMessage->getFromName(),
        );
    }
}
