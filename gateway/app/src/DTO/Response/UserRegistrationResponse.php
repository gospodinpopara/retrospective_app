<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\User;

class UserRegistrationResponse
{
    private bool $success;
    private ?array $errors;
    private ?User $user;

    public function __construct(bool $success, ?array $errors = [], ?User $user = null)
    {
        $this->success = $success;
        $this->errors = $errors;
        $this->user = $user;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
