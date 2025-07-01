<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\LatestUserNotificationsDto;
use App\Repository\UserNotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

// Required for OpenAPI content definition

#[ORM\Entity(repositoryClass: UserNotificationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Delete(),
        new Patch(),
        new Get(
            uriTemplate: '/user_notifications/not_visited_count/{userId}',
            controller: 'App\\Controller\\UserNotificationController::getNotVisitedCount',
            read: false,
            name: 'get_user_not_visited_count',
        ),
        new Get(
            uriTemplate: '/user_notifications/latest_site_notifications/{userId}',
            controller: 'App\\Controller\\UserNotificationController::getLatestSiteNotifications',
            output: LatestUserNotificationsDto::class,
            read: false,
            name: 'get_user_latest_site_notifications',
        ),
    ],
    normalizationContext: ['groups' => ['notification']],
)]
class UserNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification'])]
    private ?int $id = null;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?int $userId = null;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?bool $ack = false;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?bool $visited = false;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?bool $served = false;

    #[Groups(['notification'])]
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTime $createdAt = null;

    #[Groups(['notification'])]
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTime $updatedAt = null;

    #[Groups(['notification'])]
    #[ORM\ManyToOne(cascade: ['remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?SiteNotification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function isAck(): ?bool
    {
        return $this->ack;
    }

    public function setAck(bool $ack): static
    {
        $this->ack = $ack;

        return $this;
    }

    public function isVisited(): ?bool
    {
        return $this->visited;
    }

    public function setVisited(bool $visited): static
    {
        $this->visited = $visited;

        return $this;
    }

    public function isServed(): ?bool
    {
        return $this->served;
    }

    public function setServed(bool $served): static
    {
        $this->served = $served;

        return $this;
    }

    public function getNotification(): ?SiteNotification
    {
        return $this->notification;
    }

    public function setNotification(?SiteNotification $notification): static
    {
        $this->notification = $notification;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
