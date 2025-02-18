<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Resolver
{
    private static ?PropertyAccessor $accessor = null;

    /**
     * @param ResolveInfo $info
     *
     * @return mixed|null
     */
    public function __invoke(mixed $objectOrArray, mixed $args, mixed $context, ResolveInfo $info): mixed
    {
        $fieldName = $info->fieldName;
        $value = static::valueFromObjectOrArray($objectOrArray, $fieldName);

        return $value instanceof \Closure ? $value($objectOrArray, $args, $context, $info) : $value;
    }

    /**
     * @return mixed|null
     */
    public static function valueFromObjectOrArray(mixed $objectOrArray, mixed $fieldName): mixed
    {
        $value = null;
        $index = \sprintf('[%s]', $fieldName);

        if (self::isReadable($objectOrArray, $index)) {
            $value = self::getAccessor()->getValue($objectOrArray, $index);
        } elseif (\is_object($objectOrArray) && self::isReadable($objectOrArray, $fieldName)) {
            $value = self::getAccessor()->getValue($objectOrArray, $fieldName);
        }

        return $value;
    }

    public static function setObjectOrArrayValue(mixed &$objectOrArray, mixed $fieldName, mixed $value): void
    {
        $index = \sprintf('[%s]', $fieldName);

        if (self::isWritable($objectOrArray, $index)) {
            self::getAccessor()->setValue($objectOrArray, $index, $value);
        } elseif (\is_object($objectOrArray) && self::isWritable($objectOrArray, $fieldName)) {
            self::getAccessor()->setValue($objectOrArray, $fieldName, $value);
        }
    }

    /**
     * @return bool
     */
    private static function isReadable(mixed $objectOrArray, mixed $indexOrProperty): bool
    {
        return self::getAccessor()->isReadable($objectOrArray, $indexOrProperty);
    }

    /**
     * @return bool
     */
    private static function isWritable(mixed $objectOrArray, mixed $indexOrProperty): bool
    {
        return self::getAccessor()->isWritable($objectOrArray, $indexOrProperty);
    }

    private static function getAccessor(): PropertyAccessor
    {
        if (null === self::$accessor) {
            self::$accessor = PropertyAccess::createPropertyAccessor();
        }

        return self::$accessor;
    }
}
