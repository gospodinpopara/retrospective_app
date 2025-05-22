<?php

declare(strict_types=1);

namespace App\DTO\Response\Retrospective;

use App\DTO\Pagination;
use App\Entity\Retrospective;

class RetrospectiveCollectionResponse
{
    /**
     * @var Retrospective[]
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

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}
