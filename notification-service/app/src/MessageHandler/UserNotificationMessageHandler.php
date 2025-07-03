<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Enum\SiteNotificationType;
use App\Entity\SiteNotification;
use App\Entity\UserNotification;
use App\Message\UserNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserNotificationMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(UserNotificationMessage $message): void
    {
        $siteNotification = new SiteNotification();
        $siteNotification
            ->setTitle($message->getTitle())
            ->setBody($message->getBody())
            ->setLink($message->getLink())
            ->setType(SiteNotificationType::tryFrom($message->getType()))
            ->setDateFrom($message->getDateFrom())
            ->setDateTo($message->getDateTo())
            ->setEolDate($message->getEolDate())
            ->setGeneric(false);

        $userNotification = new UserNotification();
        $userNotification
            ->setUserId($message->getUserId())
            ->setNotification($siteNotification);

        $this->entityManager->persist($userNotification);
        $this->entityManager->flush();
    }
}
