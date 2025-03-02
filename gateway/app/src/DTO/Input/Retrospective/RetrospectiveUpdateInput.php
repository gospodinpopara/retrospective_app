<?php

declare(strict_types=1);

namespace App\DTO\Input\Retrospective;

use App\Entity\Retrospective;
use Symfony\Component\Validator\Constraints as Assert;

class RetrospectiveUpdateInput
{
    #[Assert\NotBlank(message: 'Title cannot be empty')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Title cannot be longer than {{ limit }} characters',
    )]
    private ?string $title;

    #[Assert\NotBlank(message: 'Description cannot be empty')]
    #[Assert\Length(
        max: 512,
        maxMessage: 'Description cannot be longer than {{ limit }} characters',
    )]
    private ?string $description;

    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThan(
        'now',
        message: 'Start time must be in the future',
    )]
    private ?\DateTimeInterface $startTime;

    #[Assert\Choice(choices: [Retrospective::STATUS_SCHEDULED, Retrospective::STATUS_CANCELLED, Retrospective::STATUS_FINISHED])]
    private ?string $status;

    public function __construct(?string $title, ?string $description, ?\DateTimeInterface $startTime, ?string $status)
    {
        $this->title = $title !== null ? trim($title) : null;
        $this->description = $description !== null ? trim($description) : null;
        $this->startTime = $startTime;
        $this->status = $status;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
}
