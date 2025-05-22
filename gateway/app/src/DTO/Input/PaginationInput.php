<?php

declare(strict_types=1);

namespace App\DTO\Input;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationInput
{
    #[Assert\NotNull(message: 'Page is required.')]
    #[Assert\Positive(message: 'Page must be a positive integer.')]
    private int $page;

    #[Assert\NotNull(message: 'Items per page is required.')]
    #[Assert\Positive(message: 'Items per page must be a positive integer.')]
    private int $itemsPerPage;

    public function __construct(int $page = 1, int $itemsPerPage = 15)
    {
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }
}
