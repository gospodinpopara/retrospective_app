<?php

declare(strict_types=1);

namespace App\Message;

class EmailMessage
{
    private string $toEmail;
    private string $subject;
    private string $templateName;
    private array $templateData;
    private string $fromName;
    private string $from;

    public function __construct(
        string $toEmail,
        string $subject,
        string $templateName,
        array $templateData = [],
        string $from = 'retrospectiveapp@retrospective.com',
        string $fromName = 'Retrospective App'
    ) {
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->templateName = $templateName;
        $this->templateData = $templateData;
        $this->from = $from;
        $this->fromName = $fromName;
    }

    public function getToEmail(): string
    {
        return $this->toEmail;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }
}
