<?php

declare(strict_types=1);

namespace App\DTO\Filter;

use App\DTO\PaginationTrait;
use App\Entity\RetrospectiveParticipant;
use App\Utils\DTOValidationUtil;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RetrospectiveInvitesFilter
{
    use PaginationTrait;
    #[Assert\Choice(choices: RetrospectiveParticipant::STATUS_OPTIONS, message: 'Invalid status provided.')]
    private ?string $status = null;
    #[Assert\Choice(choices: ['ASC', 'DESC'], message: 'Invalid order direction provided.')]
    private ?string $orderByRetrospectiveStartTime = 'DESC';

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderByRetrospectiveStartTime(): ?string
    {
        return $this->orderByRetrospectiveStartTime;
    }

    public function setOrderByRetrospectiveStartTime(?string $orderByRetrospectiveStartTime): self
    {
        $this->orderByRetrospectiveStartTime = $orderByRetrospectiveStartTime;

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
