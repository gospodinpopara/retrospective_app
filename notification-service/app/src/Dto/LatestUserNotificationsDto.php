<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\UserNotification;
use Symfony\Component\Serializer\Annotation\Groups;

class LatestUserNotificationsDto
{
    #[Groups('notification')]
    private int $notAckedCount;

    #[Groups('notification')]
    private int $notVisitedCount;

    /**
     * @var UserNotification[]
     */
    #[Groups('notification')]
    private array $notifications;

    public function __construct(int $notAckedCount, int $notVisitedCount, array $notifications)
    {
        $this->notAckedCount = $notAckedCount;
        $this->notVisitedCount = $notVisitedCount;
        $this->notifications = $notifications;
    }

    public function getNotAckedCount(): int
    {
        return $this->notAckedCount;
    }

    public function getNotVisitedCount(): int
    {
        return $this->notVisitedCount;
    }

    public function getNotifications(): array
    {
        return $this->notifications;
    }
}
