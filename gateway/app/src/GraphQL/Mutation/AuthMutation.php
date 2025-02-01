<?php

namespace App\GraphQL\Mutation;

use App\Model\SiteMessageModel;
use App\Service\AuthService;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Definition\Argument;

class AuthMutation implements MutationInterface, AliasedInterface
{
    // TODO implement token refresh

    public function __construct(
        private readonly AuthService $authService,
    ) { }

    public function emailVerificationTokenMutation(Argument $arguments): array
    {
        $token = $arguments->offsetGet('verificationToken');

        if($token === null) {
            throw new \InvalidArgumentException('Verification token is required.');
        }

        return $this->authService->emailTokenVerification(verificationToken: $token);
    }

    public function emailVerificationTokenResendMutation(): array
    {
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