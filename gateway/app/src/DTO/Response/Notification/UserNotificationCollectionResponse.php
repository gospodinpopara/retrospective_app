<?php

declare(strict_types=1);

namespace App\DTO\Response\Notification;

use App\DTO\Pagination;
use App\Model\UserNotification;

class UserNotificationCollectionResponse
{
    /** @var UserNotification[] */
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
