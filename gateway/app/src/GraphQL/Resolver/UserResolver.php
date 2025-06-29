<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserResolver implements QueryInterface
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    public function getParticipantsCollection(): array
    {
        return [];
    }
}
