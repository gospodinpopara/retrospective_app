<?php

declare(strict_types=1);

namespace App\DTO\Response;

class GraphQLMutationResponse
{
    protected bool $success;
    protected ?array $errors;

    protected function __construct(bool $success, ?array $errors = [])
    {
        $this->success = $success;
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
