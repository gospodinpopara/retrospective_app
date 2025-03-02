<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use GraphQL\Error\UserError;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

trait AuthorizationTrait
{
    /**
     * Get the currently authenticated user or throw an error
     *
     * @throws UserError when user is not authenticated
     */
    protected function getAuthenticatedUser(Security $security): User
    {
        /** @var User|null $user */
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new UserError('Unauthorized, login required', Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    /**
     * Check if user has specific role
     *
     * @throws UserError when user doesn't have required role
     */
    protected function assertHasRole(Security $security, string $role): void
    {
        if (!$security->isGranted($role)) {
            throw new UserError('Access denied, insufficient permissions', Response::HTTP_FORBIDDEN);
        }
    }
}
