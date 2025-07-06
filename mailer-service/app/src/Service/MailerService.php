<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EmailLog;
use Doctrine\ORM\EntityManagerInterface;
use NotFloran\MjmlBundle\Renderer\RendererInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly RendererInterface $mjmlRenderer,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param string $to
     * @param string $subject
     * @param string $templateName
     * @param array  $templateData
     * @param string $from
     * @param string $fromName
     *
     * @return void
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $templateName,
        array $templateData,
        string $from,
        string $fromName
    ): void {
        $mjmlContent = $this->twig->render($templateName.'.mjml.twig', $templateData);
        $htmlContent = $this->mjmlRenderer->render($mjmlContent);

        $email = new Email()
            ->from(new Address($from, $fromName))
            ->to($to)
            ->subject($subject)
            ->html($htmlContent);

        $emailLog = new EmailLog();
        $emailLog->setRecipient($to)
            ->setSender($from)
            ->setSenderName($fromName)
            ->setSubject($subject)
            ->setTemplate($templateName)
            ->setTemplateData($templateData)
            ->setStatus(EmailLog::STATUS_QUEUED);

        try {
            $this->mailer->send($email);
            $emailLog->setStatus(EmailLog::STATUS_SENT);
        } catch (TransportExceptionInterface $e) {
            $emailLog->setStatus(EmailLog::STATUS_FAILED)
                ->setErrorMessage($e->getMessage());
        }

        $this->entityManager->persist($emailLog);
        $this->entityManager->flush();
    }
}
