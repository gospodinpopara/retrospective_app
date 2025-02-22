<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidationErrorFormatter
{
    /**
     * Formats validation errors into a structured array grouped by field.
     *
     * @param ConstraintViolationListInterface $violations
     *
     * @return array
     */
    public function formatValidationErrors(ConstraintViolationListInterface $violations): array
    {
        $errorMessages = [];

        foreach ($violations as $violation) {
            $field = $violation->getPropertyPath();
            $errorMessages[$field][] = $violation->getMessage();
        }

        return $errorMessages;
    }
}
