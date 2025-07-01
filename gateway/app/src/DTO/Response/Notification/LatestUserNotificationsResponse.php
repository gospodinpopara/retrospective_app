<?php

declare(strict_types=1);

namespace App\DTO\Response\Notification;

use App\Model\UserNotification;

class LatestUserNotificationsResponse
{
    /** @var UserNotification[] */
    private array $notificationList;
    private int $notAckedCount;
    private int $notVisitedCount;
    private int $totalItems;

    public function __construct(
        array $notificationList,
        int $notAckedCount,
        int $notVisitedCount,
        int $totalItems
    ) {
        $this->notificationList = $notificationList;
        $this->notAckedCount = $notAckedCount;
        $this->notVisitedCount = $notVisitedCount;
        $this->totalItems = $totalItems;
    }

    public function getNotificationList(): array
    {
        return $this->notificationList;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getNotVisitedCount(): int
    {
        return $this->notVisitedCount;
    }

    public function getNotAckedCount(): int
    {
        return $this->notAckedCount;
    }
}
