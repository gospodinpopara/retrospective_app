<?php

declare(strict_types=1);

namespace App\DTO\Input\RetrospectiveParticipant;

use App\Entity\RetrospectiveParticipant;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRetrospectiveParticipantInput
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $retrospectiveId;
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $participantId;
    #[Assert\Choice([RetrospectiveParticipant::STATUS_PENDING, RetrospectiveParticipant::STATUS_DECLINED, RetrospectiveParticipant::STATUS_ACCEPTED])]
    private string $status;

    public function __construct(int $retrospectiveId, int $participantId, string $status)
    {
        $this->retrospectiveId = $retrospectiveId;
        $this->participantId = $participantId;
        $this->status = $status;
    }

    public function getRetrospectiveId(): int
    {
        return $this->retrospectiveId;
    }

    public function getParticipantId(): int
    {
        return $this->participantId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
