<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\Service\AuthService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class AuthMutation implements MutationInterface, AliasedInterface
{
    // TODO implement token refresh

    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function emailVerificationTokenMutation(Argument $arguments): array
    {
        $token = $arguments->offsetGet('verificationToken');

        if ($token === null) {
            throw new \InvalidArgumentException('Verification token is required.');
        }

        return $this->authService->emailTokenVerification(verificationToken: $token);
    }

    public function emailVerificationTokenResendMutation(Argument $arguments): array
    {
        $email = $arguments->offsetGet('email');

        if ($email === null) {
            throw new \InvalidArgumentException('Email is required.');
        }

        return $this->authService->resendEmailVerificationToken(email: $email);
    }

    public function userRegistrationMutation(): array
    {
        // TODO implement user registration

        return [];
    }

    public static function getAliases(): array
    {
        return [];
    }
}
