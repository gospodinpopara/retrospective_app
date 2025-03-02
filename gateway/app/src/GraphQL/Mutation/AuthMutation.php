<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\DTO\Input\Auth\UserRegistrationInput;
use App\DTO\Response\Auth\UserRegistrationMutationResponse;
use App\Service\AuthService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AuthMutation implements MutationInterface
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * @param Argument $args
     *
     * @return array
     */
    public function emailVerificationTokenMutation(Argument $args): array
    {
        $token = $args->offsetGet('verificationToken');

        if ($token === null) {
            throw new \InvalidArgumentException('Verification token is required.');
        }

        return $this->authService->emailTokenVerification(verificationToken: $token);
    }

    /**
     * @param Argument $args
     *
     * @return array
     */
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
    public function userRegistrationMutation(Argument $args): UserRegistrationMutationResponse
    {
        $userRegistrationInput = $this->denormalizer->denormalize($args->offsetGet('userRegistrationInput'), UserRegistrationInput::class, 'array');

        return $this->authService->registerUser(userRegistrationInput: $userRegistrationInput);
    }
}
