<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;

class ValidationErrorType extends ScalarType implements AliasedInterface
{
    public string $name = 'ValidationError';

    public ?string $description = 'A scalar type representing validation errors as a map of field names to arrays of error messages in key value pair format';

    #[\Override]
    public function serialize($value): mixed
    {
        if (!\is_array($value)) {
            throw new \UnexpectedValueException('Validation errors must be an array');
        }

        return $value;
    }

    #[\Override]
    public function parseValue($value): mixed
    {
        if (!\is_array($value)) {
            throw new \UnexpectedValueException('Cannot parse non-array value as validation errors');
        }

        return $value;
    }

    #[\Override]
    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        if (property_exists($valueNode, 'values')) {
            return array_map(
                fn ($node) => $this->parseLiteral($node, $variables),
                $valueNode->values,
            );
        }

        /* @phpstan-ignore-next-line */
        return $valueNode->value;
    }

    #[\Override]
    public static function getAliases(): array
    {
        return ['ValidationError'];
    }
}
