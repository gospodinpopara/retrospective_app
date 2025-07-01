<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class NotificationMutation implements MutationInterface
{
    public function setVisitedUserNotificationMutation(): array
    {
        return [];
    }

    public function setAllAsAckMutation(): array
    {
        return [];
    }
}
