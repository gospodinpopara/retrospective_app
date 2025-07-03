<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\Security\AuthorizationTrait;
use App\Service\NotificationService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class NotificationMutation implements MutationInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly Security $security,
        private readonly NotificationService $notificationService
    ) {
    }

    /**
     * @param Argument $args
     *
     * @return bool
     *
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function setVisitedUserNotificationMutation(Argument $args): bool
    {
        $user = $this->getAuthenticatedUser($this->security);

        $notificationId = $args->offsetGet('id');

        return $this->notificationService->setVisitedUserNotification($user, $notificationId);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function setAllAsAckMutation(): bool
    {
        $user = $this->getAuthenticatedUser($this->security);

        return $this->notificationService->setAllAsAck($user);
    }
}
