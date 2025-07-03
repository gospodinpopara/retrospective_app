<?php

declare(strict_types=1);

namespace App\DTO\Filter;

use App\DTO\PaginationTrait;
use App\Utils\DTOValidationUtil;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ParticipantFilter
{
    use PaginationTrait;
    private ?string $email = null;
    private ?string $firstName = null;
    private ?string $lastName = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

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
