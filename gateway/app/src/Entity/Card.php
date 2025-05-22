<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    public const string TYPE_STOP = 'stop';
    public const string TYPE_LESS = 'less';
    public const string TYPE_KEEP = 'keep';
    public const string TYPE_MORE = 'more';
    public const string TYPE_START = 'start';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 1024)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Retrospective $retrospective = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, CardUpvote>
     */
    #[ORM\OneToMany(targetEntity: CardUpvote::class, mappedBy: 'card')]
    private Collection $cardUpvotes;

    public function __construct()
    {
        $this->cardUpvotes = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!\in_array($type, [self::TYPE_STOP, self::TYPE_LESS, self::TYPE_KEEP, self::TYPE_MORE, self::TYPE_START], true)) {
            throw new \InvalidArgumentException('Invalid status provided');
        }

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

    public function getRetrospective(): ?Retrospective
    {
        return $this->retrospective;
    }

    public function setRetrospective(?Retrospective $retrospective): static
    {
        $this->retrospective = $retrospective;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection<int, CardUpvote>
     */
    public function getCardUpvotes(): Collection
    {
        return $this->cardUpvotes;
    }

    public function addCardUpvote(CardUpvote $cardUpvote): static
    {
        if (!$this->cardUpvotes->contains($cardUpvote)) {
            $this->cardUpvotes->add($cardUpvote);
            $cardUpvote->setCard($this);
        }

        return $this;
    }

    public function removeCardUpvote(CardUpvote $cardUpvote): static
    {
        if ($this->cardUpvotes->removeElement($cardUpvote)) {
            // set the owning side to null (unless already changed)
            if ($cardUpvote->getCard() === $this) {
                $cardUpvote->setCard(null);
            }
        }

        return $this;
    }
}
