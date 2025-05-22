<?php

declare(strict_types=1);

namespace App\DTO\Filter;

class RetrospectiveFilter
{
    private int $page = 1;
    private int $itemsPerPage = 15;
    private ?bool $isOwner = null;

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

    public function getIsOwner(): ?bool
    {
        return $this->isOwner;
    }

    public function setIsOwner(?bool $isOwner): void
    {
        $this->isOwner = $isOwner;
    }

    /**
     * @param array|null $filterInput
     *
     * @return self
     */
    public static function createFromArray(?array $filterInput): self
    {
        $filter = new self();

        if ($filterInput === null) {
            return $filter;
        }

        if (isset($filterInput['page'])) {
            $pageValue = filter_var($filterInput['page'], \FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($pageValue !== false) {
                $filter->setPage($pageValue);
            }
        }

        if (isset($filterInput['itemsPerPage'])) {
            $itemsPerPageValue = filter_var($filterInput['itemsPerPage'], \FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($itemsPerPageValue !== false) {
                $filter->setItemsPerPage($itemsPerPageValue);
            }
        }

        if (isset($filterInput['isOwner'])) {
            $isOwnerValue = filter_var($filterInput['isOwner'], \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
            if ($isOwnerValue !== null) {
                $filter->setIsOwner($isOwnerValue);
            }
        }

        return $filter;
    }
}
