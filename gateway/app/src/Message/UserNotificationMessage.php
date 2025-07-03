<?php

declare(strict_types=1);

namespace App\Message;

use App\Model\UserNotification;

class UserNotificationMessage
{
    private int $userId;
    private string $title;
    private string $body;
    private string $link;
    private string $type;
    private \DateTimeInterface $dateFrom;
    private \DateTimeInterface $dateTo;
    private ?\DateTimeInterface $eolDate = null;
    private string $messageType = 'user_notification_message';

    public function __construct(
        int $userId,
        string $title,
        string $body,
        string $link,
        string $type,
        \DateTimeInterface $dateFrom,
        \DateTimeInterface $dateTo,
        ?\DateTimeInterface $eolDate = null
    ) {
        if (!\in_array($type, UserNotification::NOTIFICATION_TYPES)) {
            throw new \InvalidArgumentException(message: "Invalid notification type: {$type}. Allowed types are: ".implode(', ', UserNotification::NOTIFICATION_TYPES));
        }

        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->link = $link;
        $this->type = $type;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->eolDate = $eolDate;
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function getEolDate(): ?\DateTimeInterface
    {
        return $this->eolDate;
    }

    public function getDateTo(): \DateTimeInterface
    {
        return $this->dateTo;
    }

    public function getDateFrom(): \DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
