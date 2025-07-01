<?php

declare(strict_types=1);

namespace App\Model;

class UserNotification
{
    private ?int $siteNotificationId = null;
    private ?int $userNotificationId = null;
    private ?string $title = null;
    private ?string $body = null;
    private ?string $link = null;
    private ?string $type = null;
    private ?\DateTimeInterface $dateFrom = null;
    private ?\DateTimeInterface $dateTo = null;
    private ?\DateTimeInterface $createdAt = null;
    private ?bool $visited = null;
    private ?bool $served = null;
    private ?bool $generic = null;

    public function getSiteNotificationId(): ?int
    {
        return $this->siteNotificationId;
    }

    public function setSiteNotificationId(?int $siteNotificationId): self
    {
        $this->siteNotificationId = $siteNotificationId;

        return $this;
    }

    public function getUserNotificationId(): ?int
    {
        return $this->userNotificationId;
    }

    public function setUserNotificationId(?int $userNotificationId): self
    {
        $this->userNotificationId = $userNotificationId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTimeInterface $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeInterface $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVisited(): ?bool
    {
        return $this->visited;
    }

    public function setVisited(?bool $visited): self
    {
        $this->visited = $visited;

        return $this;
    }

    public function getServed(): ?bool
    {
        return $this->served;
    }

    public function setServed(?bool $served): self
    {
        $this->served = $served;

        return $this;
    }

    public function getGeneric(): ?bool
    {
        return $this->generic;
    }

    public function setGeneric(?bool $generic): self
    {
        $this->generic = $generic;

        return $this;
    }
}
