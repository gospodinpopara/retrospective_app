<?php

declare(strict_types=1);

namespace App\DTO\Filter;

use App\DTO\PaginationTrait;
use App\Utils\DTOValidationUtil;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserNotificationFilter
{
    use PaginationTrait;

    #[Assert\NotBlank(message: 'User ID cannot be blank.')]
    private int $userId;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param array|null $filterInput
     *
     * @return self
     */
    public static function createFromArray(?array $filterInput): self
    {
        $serializer = new Serializer([new ObjectNormalizer()]);

        $filter = $serializer->denormalize($filterInput, self::class);

        DTOValidationUtil::validateDto($filter);

        return $filter;
    }
}
