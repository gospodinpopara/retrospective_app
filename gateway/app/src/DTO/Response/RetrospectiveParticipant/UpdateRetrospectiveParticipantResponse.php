<?php

declare(strict_types=1);

namespace App\DTO\Response\RetrospectiveParticipant;

use App\DTO\Response\GraphQLMutationResponse;
use App\Entity\RetrospectiveParticipant;

class UpdateRetrospectiveParticipantResponse extends GraphQLMutationResponse
{
    private ?RetrospectiveParticipant $retrospectiveParticipant;

    public function __construct(bool $success, ?array $errors = [], ?RetrospectiveParticipant $retrospectiveParticipant = null)
    {
        parent::__construct($success, $errors);
        $this->retrospectiveParticipant = $retrospectiveParticipant;
    }

    public function getRetrospectiveParticipant(): ?RetrospectiveParticipant
    {
        return $this->retrospectiveParticipant;
    }

    public static function success(RetrospectiveParticipant $retrospectiveParticipant): self
    {
        return new self(success: true, errors: [], retrospectiveParticipant: $retrospectiveParticipant);
    }

    public static function failure(array $errors): self
    {
        return new self(success: false, errors: $errors);
    }
}
