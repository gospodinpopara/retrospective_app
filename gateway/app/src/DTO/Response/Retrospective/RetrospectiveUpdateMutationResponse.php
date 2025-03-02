<?php

declare(strict_types=1);

namespace App\DTO\Response\Retrospective;

use App\DTO\Response\GraphQLMutationResponse;
use App\Entity\Retrospective;

class RetrospectiveUpdateMutationResponse extends GraphQLMutationResponse
{
    private ?Retrospective $retrospective;

    public function __construct(bool $success, ?array $errors = [], ?Retrospective $retrospective = null)
    {
        parent::__construct($success, $errors);
        $this->retrospective = $retrospective;
    }

    public function getRetrospective(): ?Retrospective
    {
        return $this->retrospective;
    }

    public static function success(Retrospective $retrospective): self
    {
        return new self(success: true, errors: [], retrospective: $retrospective);
    }

    public static function failure(array $errors): self
    {
        return new self(success: false, errors: $errors);
    }
}
