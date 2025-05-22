<?php

declare(strict_types=1);

namespace App\DTO;

class Pagination
{
    private int $currentPage;
    private int $itemsPerPage;
    private int $totalItems;
    private int $totalPages;

    public function __construct(
        int $currentPage,
        int $itemsPerPage,
        int $totalItems,
        int $totalPages
    ) {
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
        $this->totalPages = $totalPages;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
}
