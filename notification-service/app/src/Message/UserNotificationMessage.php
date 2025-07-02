<?php

declare(strict_types=1);

namespace App\Message;

class UserNotificationMessage
{
    private int $userId;
    private string $title;
    private string $body;
    private string $link;
    private string $type;
    private \DateTime $dateFrom;
    private \DateTime $dateTo;
    private ?\DateTime $eolDate = null;

    public function __construct(
        int $userId,
        string $title,
        string $body,
        string $link,
        string $type,
        \DateTime $dateFrom,
        \DateTime $dateTo,
        ?\DateTime $eolDate = null
    ) {
        $this->userId = $userId;
        $this->title = $title;
        $this->body = $body;
        $this->link = $link;
        $this->type = $type;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->eolDate = $eolDate;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDateFrom(): \DateTime
    {
        return $this->dateFrom;
    }

    public function getDateTo(): \DateTime
    {
        return $this->dateTo;
    }

    public function getEolDate(): ?\DateTime
    {
        return $this->eolDate;
    }
}
