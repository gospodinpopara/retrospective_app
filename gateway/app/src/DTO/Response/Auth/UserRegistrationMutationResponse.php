<?php

declare(strict_types=1);

namespace App\DTO\Response\Auth;

use App\DTO\Response\GraphQLMutationResponse;
use App\Entity\User;

class UserRegistrationMutationResponse extends GraphQLMutationResponse
{
    private ?User $user;

    public function __construct(bool $success, ?array $errors = [], ?User $user = null)
    {
        parent::__construct($success, $errors);
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public static function success(User $user): self
    {
        return new self(success: true, errors: [], user: $user);
    }

    public static function failure(array $errors): self
    {
        return new self(success: false, errors: $errors);
    }
}
