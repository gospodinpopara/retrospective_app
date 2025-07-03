<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\SiteNotification;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preFlush, method: 'handlePreFlush', entity: SiteNotificationEol::class)]
class SiteNotificationEol
{
    /**
     * Automatically sets the EOL date to one year from now if it is not already set.
     *
     * @throws \Exception
     */
    public function handlePreFlush(SiteNotification $siteNotification): void
    {
        if ($siteNotification->getEolDate() === null) {
            $siteNotification->setEolDate(new \DateTime()->modify('+1 year'));
        }
    }
}
