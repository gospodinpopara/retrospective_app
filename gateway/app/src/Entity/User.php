<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const string ACCOUNT_STATUS_ACTIVE = 'active';
    public const string ACCOUNT_STATUS_PENDING = 'pending';
    public const string ACCOUNT_STATUS_SUSPENDED = 'suspended';
    public const string ACCOUNT_STATUS_BANNED = 'banned';
    public const string ACCOUNT_STATUS_DELETED = 'deleted';

    public const array ACCOUNT_STATUS = [
        self::ACCOUNT_STATUS_ACTIVE,
        self::ACCOUNT_STATUS_PENDING,
        self::ACCOUNT_STATUS_SUSPENDED,
        self::ACCOUNT_STATUS_BANNED,
        self::ACCOUNT_STATUS_DELETED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Email should not be blank.')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email address.")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isEmailVerified = false;

    #[ORM\Column(length: 255, options: ['default' => self::ACCOUNT_STATUS_PENDING])]
    private string $accountStatus = self::ACCOUNT_STATUS_PENDING;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, Retrospective>
     */
    #[ORM\OneToMany(targetEntity: Retrospective::class, mappedBy: 'owner')]
    private Collection $retrospectives;

    /**
     * @var Collection<int, RetrospectiveParticipant>
     */
    #[ORM\OneToMany(targetEntity: RetrospectiveParticipant::class, mappedBy: 'user')]
    private Collection $retrospectiveParticipants;

    public function __construct()
    {
        $this->retrospectives = new ArrayCollection();
        $this->retrospectiveParticipants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        /* @phpstan-ignore-next-line */
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        /* @phpstan-ignore-next-line */
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->isEmailVerified;
    }

    public function setIsEmailVerified(bool $isEmailVerified): self
    {
        $this->isEmailVerified = $isEmailVerified;

        return $this;
    }

    public function getAccountStatus(): string
    {
        return $this->accountStatus;
    }

    public function setAccountStatus(string $accountStatus): self
    {
        if (!\in_array($accountStatus, self::ACCOUNT_STATUS, true)) {
            throw new \InvalidArgumentException(\sprintf("Invalid account status '%s'", $accountStatus));
        }

        $this->accountStatus = $accountStatus;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Retrospective>
     */
    public function getRetrospectives(): Collection
    {
        return $this->retrospectives;
    }

    public function addRetrospective(Retrospective $retrospective): static
    {
        if (!$this->retrospectives->contains($retrospective)) {
            $this->retrospectives->add($retrospective);
            $retrospective->setOwner($this);
        }

        return $this;
    }

    public function removeRetrospective(Retrospective $retrospective): static
    {
        if ($this->retrospectives->removeElement($retrospective)) {
            // set the owning side to null (unless already changed)
            if ($retrospective->getOwner() === $this) {
                $retrospective->setOwner(null);
            }
        }

        return $this;
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
            $retrospectiveParticipant->setUser($this);
        }

        return $this;
    }

    public function removeRetrospectiveParticipant(RetrospectiveParticipant $retrospectiveParticipant): static
    {
        if ($this->retrospectiveParticipants->removeElement($retrospectiveParticipant)) {
            // set the owning side to null (unless already changed)
            if ($retrospectiveParticipant->getUser() === $this) {
                $retrospectiveParticipant->setUser(null);
            }
        }

        return $this;
    }
}
