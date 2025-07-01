<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Enum\SiteNotificationType;
use App\Repository\SiteNotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SiteNotificationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Delete(),
        new Patch(),
    ],
    normalizationContext: ['groups' => ['notification']],
)]
class SiteNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification'])]
    private ?int $id = null;

    #[Groups(['notification'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['notification'])]
    #[ORM\Column(length: 512)]
    private ?string $body = null;

    #[Groups(['notification'])]
    #[ORM\Column(length: 512)]
    private ?string $link = null;

    #[Groups(['notification'])]
    #[ORM\Column(length: 255, enumType: SiteNotificationType::class)]
    private ?SiteNotificationType $type = null;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?\DateTime $dateFrom = null;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?\DateTime $dateTo = null;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?bool $generic = false;

    #[Groups(['notification'])]
    #[ORM\Column]
    private ?\DateTime $eolDate = null;

    #[Groups(['notification'])]
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[Groups(['notification'])]
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getType(): ?SiteNotificationType
    {
        return $this->type;
    }

    public function setType(?SiteNotificationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDateFrom(): ?\DateTime
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTime $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTime
    {
        return $this->dateTo;
    }

    public function setDateTo(\DateTime $dateTo): static
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function isGeneric(): ?bool
    {
        return $this->generic;
    }

    public function setGeneric(bool $generic): static
    {
        $this->generic = $generic;

        return $this;
    }

    public function getEolDate(): ?\DateTime
    {
        return $this->eolDate;
    }

    public function setEolDate(\DateTime $eolDate): static
    {
        $this->eolDate = $eolDate;

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
