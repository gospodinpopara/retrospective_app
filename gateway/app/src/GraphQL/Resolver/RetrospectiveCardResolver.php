<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Entity\Card;
use App\Security\AuthorizationTrait;
use App\Service\RetrospectiveCardService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RetrospectiveCardResolver implements QueryInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly RetrospectiveCardService $retrospectiveCardService,
        private readonly Security $security
    ) {
    }

    public function getRetrospectiveCard(Argument $args): Card
    {
        $user = $this->getAuthenticatedUser($this->security);

        return $this->retrospectiveCardService->getRetrospectiveCard(cardId: $args->offsetGet('id'), user: $user);
    }
}
