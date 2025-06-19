<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\Validator\Validation;

class DTOValidationUtil
{
    /**
     * @param object $dto
     *
     * @return void
     */
    public static function validateDto(object $dto): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($dto);

        if (\count($violations) > 0) {
            $errorMessages = [];

            foreach ($violations as $violation) {
                $field = $violation->getPropertyPath();
                $message = $violation->getMessage();

                if (!isset($errorMessages[$field])) {
                    $errorMessages[$field] = $message;
                } elseif (\is_array($errorMessages[$field])) {
                    $errorMessages[$field][] = $message;
                } else {
                    $errorMessages[$field] = [$errorMessages[$field], $message];
                }
            }

            $errorMessage = json_encode($errorMessages, \JSON_PRETTY_PRINT);

            if ($errorMessage === false) {
                throw new \InvalidArgumentException('Failed to encode validation errors: '.json_last_error_msg());
            }

            throw new \InvalidArgumentException($errorMessage);
        }
    }
}
