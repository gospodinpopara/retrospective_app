<?php

declare(strict_types=1);

namespace App\DTO\Input\Retrospective;

use Symfony\Component\Validator\Constraints as Assert;

class RetrospectiveCreateInput
{
    #[Assert\NotBlank(message: 'Title cannot be empty')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Title cannot be longer than {{ limit }} characters',
    )]
    private string $title;

    #[Assert\NotBlank(message: 'Description cannot be empty')]
    #[Assert\Length(
        max: 512,
        maxMessage: 'Description cannot be longer than {{ limit }} characters',
    )]
    private string $description;

    #[Assert\NotNull(message: 'Start time must be provided')]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThan(
        'now',
        message: 'Start time must be in the future',
    )]
    private \DateTimeInterface $startTime;

    public function __construct(string $title, string $description, \DateTimeInterface $startTime)
    {
        $this->title = trim($title);
        $this->description = trim($description);
        $this->startTime = $startTime;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }
}
