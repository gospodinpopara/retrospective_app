<?php

declare(strict_types=1);

namespace App\DTO\Response\RetrospectiveCard;

use App\DTO\Response\GraphQLMutationResponse;
use App\Entity\Card;

class RetrospectiveCardUpdateMutationResponse extends GraphQLMutationResponse
{
    private ?Card $card;

    public function __construct(bool $success, ?array $errors = [], ?Card $card = null)
    {
        parent::__construct($success, $errors);
        $this->card = $card;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public static function success(Card $card): self
    {
        return new self(success: true, errors: [], card: $card);
    }

    public static function failure(array $errors): self
    {
        return new self(success: false, errors: $errors);
    }
}
