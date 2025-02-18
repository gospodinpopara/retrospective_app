<?php

namespace App\Model;

class SiteMessageModel
{
    public const string MESSAGE_SUCCESS = 'success';
    public const string MESSAGE_DANGER = 'danger';
    public const string MESSAGE_WARNING = 'warning';
    public const string MESSAGE_INFO = 'info';

    private bool $success {
        get {
            return $this->success;
        }
    }
    private string $message {
        get {
            return $this->message;
        }
    }
    private string $messageType {
        get {
            return $this->messageType;
        }
    }

    public function __construct(bool $success, string $message, string $messageType)
    {
        $this->success = $success;
        $this->message = $message;
        $this->messageType = $messageType;
    }

}