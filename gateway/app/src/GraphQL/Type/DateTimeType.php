<?php

namespace App\GraphQL\Type;

use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;

class DateTimeType extends ScalarType implements AliasedInterface
{
    public string $name = 'DateTime';

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function serialize($value): string
    {
        if (\is_string($value)) {
            try {
                $value = new \DateTime($value);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Invalid DateTime string: '.$value);
            }
        }
        if (!$value instanceof \DateTimeInterface) {
            throw new \InvalidArgumentException('Expected DateTime instance or valid string.');
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * @param mixed $value
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    #[\Override]
    public function parseValue($value): \DateTime
    {
        return new \DateTime($value);
    }

    /**
     * @param Node $valueNode
     * @param null $variables
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    #[\Override]
    public function parseLiteral(Node $valueNode, $variables = null): \DateTime
    {
        /* @phpstan-ignore-next-line */
        return new \DateTime($valueNode->value);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public static function getAliases(): array
    {
        return ['DateTime'];
    }
}
