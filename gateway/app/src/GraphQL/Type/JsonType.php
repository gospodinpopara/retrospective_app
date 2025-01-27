<?php

namespace App\GraphQL\Type;

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use JsonException;

class JsonType extends ScalarType implements AliasedInterface
{
    /**
     * Serialize the PHP value into a serialized value.
     *
     * @param mixed $value the PHP value
     *
     * @return string the serialized value
     *
     * @throws JsonException if the value cannot be encoded
     */
    #[\Override]
    public function serialize($value): string
    {
        if (!is_array($value) && !is_object($value)) {
            throw new JsonException('Expected an array or object for JSON serialization.');
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * Parse the serialized value into a PHP value.
     *
     * @param mixed $value the serialized value
     *
     * @return mixed the PHP value
     *
     * @throws JsonException if the value cannot be decoded
     */
    #[\Override]
    public function parseValue($value): mixed
    {
        if (!is_string($value)) {
            throw new JsonException('Expected a string for JSON decoding.');
        }

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Parse the AST literal node into a PHP value.
     *
     * @param Node $valueNode the AST literal node
     * @param array|null $variables the variables
     *
     * @return mixed the PHP value
     *
     * @throws JsonException if the value cannot be decoded
     */
    #[\Override]
    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        if (!isset($valueNode->value)) {
            throw new JsonException('Expected a value node with a value property.');
        }

        return json_decode($valueNode->value, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get the aliases for this type.
     *
     * @return array the aliases
     */
    #[\Override]
    public static function getAliases(): array
    {
        return ['Json'];
    }
}