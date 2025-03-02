<?php

declare(strict_types=1);

namespace App\DTO\Input\RetrospectiveParticipant;

class RemoveUserFromRetrospectiveInput
{
    private int $retrospectiveId;
    private int $participantId;

    public function __construct(int $retrospectiveId, int $participantId)
    {
        $this->retrospectiveId = $retrospectiveId;
        $this->participantId = $participantId;
    }

    public function getRetrospectiveId(): int
    {
        return $this->retrospectiveId;
    }

    public function getParticipantId(): int
    {
        return $this->participantId;
    }
}
