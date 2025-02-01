<?php

namespace App\GraphQL\Mutation;

use App\Service\AuthService;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class AuthMutation implements MutationInterface, AliasedInterface
{
    // TODO implement token refresh

    public function __construct(
        private readonly AuthService $authService,
    ) { }

    public function emailVerificationTokenMutation(): array
    {
        // TODO implement mutation logic

        return [];
    }

    public function emailVerificationTokenResendMutation(): array
    {
        // TODO implement token resend

        return [];
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