<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\DTO\Input\UserRegistrationInput;
use App\DTO\Response\UserRegistrationResponse;
use App\Service\AuthService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AuthMutation implements MutationInterface, AliasedInterface
{
    // TODO implement token refresh

    public function __construct(
        private readonly AuthService $authService,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    public function emailVerificationTokenMutation(Argument $args): array
    {
        $token = $args->offsetGet('verificationToken');

        if ($token === null) {
            throw new \InvalidArgumentException('Verification token is required.');
        }

        return $this->authService->emailTokenVerification(verificationToken: $token);
    }

    public function emailVerificationTokenResendMutation(Argument $args): array
    {
        $email = $args->offsetGet('email');

        if ($email === null) {
            throw new \InvalidArgumentException('Email is required.');
        }

        return $this->authService->resendEmailVerificationToken(email: $email);
    }

    /**
     * @throws ExceptionInterface
     */
    public function userRegistrationMutation(Argument $args): UserRegistrationResponse
    {
        $userRegistrationInput = $this->denormalizer->denormalize($args->offsetGet('userRegistrationInput'), UserRegistrationInput::class, 'array');

        return $this->authService->registerUser(userRegistrationInput: $userRegistrationInput);
    }

    public static function getAliases(): array
    {
        return [];
    }
}
