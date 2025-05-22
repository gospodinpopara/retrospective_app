<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RetrospectiveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: RetrospectiveRepository::class)]
class Retrospective
{
    public const string STATUS_SCHEDULED = 'scheduled';
    public const string STATUS_FINISHED = 'finished';
    public const string STATUS_CANCELLED = 'cancelled';
    public const string STATUS_ACTIVE = 'active';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 512)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'retrospectives')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, RetrospectiveParticipant>
     */
    #[ORM\OneToMany(targetEntity: RetrospectiveParticipant::class, mappedBy: 'retrospective')]
    private Collection $retrospectiveParticipants;

    /**
     * @var Collection<int, Card>
     */
    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'retrospective')]
    private Collection $cards;

    public function __construct()
    {
        $this->retrospectiveParticipants = new ArrayCollection();
        $this->cards = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!\in_array($status, [self::STATUS_SCHEDULED, self::STATUS_FINISHED, self::STATUS_CANCELLED, self::STATUS_ACTIVE], true)) {
            throw new \InvalidArgumentException('Invalid status provided');
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return Collection<int, RetrospectiveParticipant>
     */
    public function getRetrospectiveParticipants(): Collection
    {
        return $this->retrospectiveParticipants;
    }

    public function addRetrospectiveParticipant(RetrospectiveParticipant $retrospectiveParticipant): static
    {
        if (!$this->retrospectiveParticipants->contains($retrospectiveParticipant)) {
            $this->retrospectiveParticipants->add($retrospectiveParticipant);
            $retrospectiveParticipant->setRetrospective($this);
        }

        return $this;
    }

    public function removeRetrospectiveParticipant(RetrospectiveParticipant $retrospectiveParticipant): static
    {
        if ($this->retrospectiveParticipants->removeElement($retrospectiveParticipant)) {
            // set the owning side to null (unless already changed)
            if ($retrospectiveParticipant->getRetrospective() === $this) {
                $retrospectiveParticipant->setRetrospective(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setRetrospective($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getRetrospective() === $this) {
                $card->setRetrospective(null);
            }
        }

        return $this;
    }
}
