<?php

declare(strict_types=1);

namespace App\Model;

class SiteMessageModel
{
    public const string MESSAGE_SUCCESS = 'success';
    public const string MESSAGE_DANGER = 'danger';
    public const string MESSAGE_WARNING = 'warning';
    public const string MESSAGE_INFO = 'info';

    private bool $success;
    private string $message;
    private string $messageType;

    public function __construct(bool $success, string $message, string $messageType)
    {
        $this->success = $success;
        $this->message = $message;
        $this->messageType = $messageType;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function setMessageType(string $messageType): void
    {
        $this->messageType = $messageType;
    }
}
