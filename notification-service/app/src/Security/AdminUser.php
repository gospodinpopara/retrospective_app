<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class AdminUser implements UserInterface
{
    public const string IDENTIFIER = 'admin_user';

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials(): void
    {

    }

    public function getUserIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
