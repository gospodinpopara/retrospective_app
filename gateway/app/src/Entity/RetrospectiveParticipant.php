<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RetrospectiveParticipantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: RetrospectiveParticipantRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_user_retrospective', columns: ['user_id', 'retrospective_id'])]
class RetrospectiveParticipant
{
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_ACCEPTED = 'accepted';
    public const string STATUS_DECLINED = 'declined';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'retrospectiveParticipants')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'retrospectiveParticipants')]
    #[ORM\JoinColumn(name: 'retrospective_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Retrospective $retrospective = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!\in_array($status, [self::STATUS_DECLINED, self::STATUS_ACCEPTED, self::STATUS_PENDING])) {
            throw new \InvalidArgumentException('Invalid retrospective participant status provided');
        }

        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRetrospective(): ?Retrospective
    {
        return $this->retrospective;
    }

    public function setRetrospective(?Retrospective $retrospective): void
    {
        $this->retrospective = $retrospective;
    }
}
