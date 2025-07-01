<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UserNotification;
use App\Repository\SiteNotificationRepository;
use App\Repository\UserNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class SiteNotificationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SiteNotificationRepository $siteNotificationRepository,
        private readonly UserNotificationRepository $userNotificationRepository
    ) {
    }

    /**
     * @param int $userId
     *
     * @return void
     */
    public function convertPendingGenericNotificationsToPersonal(int $userId): void
    {
        $activeGenericSiteNotifications = $this->siteNotificationRepository->getActiveGenericSiteNotifications();
        $activeGenericUserNotification = $this->userNotificationRepository->getUserActiveGenericNotifications();

        $pendingNotifications = array_filter(
            $activeGenericSiteNotifications,
            static fn ($siteNotification) => !\in_array($siteNotification->getId(), array_map(fn ($un) => $un->getNotification()->getId(), $activeGenericUserNotification), true),
        );

        foreach ($pendingNotifications as $pendingNotification) {
            $userNotification = new UserNotification();

            $userNotification
                ->setUserId($userId)
                ->setNotification($pendingNotification);

            $this->entityManager->persist($userNotification);
        }

        $this->entityManager->flush();
    }
}
