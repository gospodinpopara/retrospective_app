<?php

declare(strict_types=1);

namespace App\DTO\Response\RetrospectiveParticipant;

use App\DTO\Pagination;
use App\Entity\RetrospectiveParticipant;

class RetrospectiveInviteCollectionResponse
{
    /**
     * @var RetrospectiveParticipant[]
     */
    private array $data;
    private Pagination $pagination;

    public function __construct(
        array $data,
        int $currentPage,
        int $itemsPerPage,
        int $totalItems,
        int $totalPages
    ) {
        $this->data = $data;
        $this->pagination = new Pagination($currentPage, $itemsPerPage, $totalItems, $totalPages);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function setPagination(Pagination $pagination): self
    {
        $this->pagination = $pagination;

        return $this;
    }
}
