<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

trait PaginationTrait
{
    #[Assert\Positive(message: 'Page number must be a positive integer.')]
    public int $page = 1;

    #[Assert\Positive(message: 'Page number must be a positive integer.')]
    public int $itemsPerPage = 15;

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
