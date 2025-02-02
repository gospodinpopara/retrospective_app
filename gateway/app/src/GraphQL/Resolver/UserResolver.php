<?php

namespace App\GraphQL\Resolver;

use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserResolver implements QueryInterface
{
    public function __construct(
        private readonly Security $security
    ) { }

    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

}