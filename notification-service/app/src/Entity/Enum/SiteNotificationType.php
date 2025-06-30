<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum SiteNotificationType: string
{
    case INFO = 'info';
    case RETROSPECTIVE_INVITATION = 'retrospective_invitation';
    case RETROSPECTIVE_REMINDER = 'retrospective_reminder';
}
