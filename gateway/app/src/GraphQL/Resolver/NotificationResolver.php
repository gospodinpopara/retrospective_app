<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\DTO\Filter\UserNotificationFilter;
use App\DTO\Response\Notification\LatestUserNotificationsResponse;
use App\DTO\Response\Notification\UserNotificationCollectionResponse;
use App\Security\AuthorizationTrait;
use App\Service\NotificationService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class NotificationResolver implements QueryInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly Security $security
    ) {
    }

    /**
     * @return LatestUserNotificationsResponse
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \DateMalformedStringException
     */
    public function resolveLatestUserNotifications(): LatestUserNotificationsResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        return $this->notificationService->getLatestUserNotifications(userId: $user->getId());
    }

    /**
     * @param Argument $args
     *
     * @return UserNotificationCollectionResponse
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \DateMalformedStringException
     */
    public function resolveUserNotifications(Argument $args): UserNotificationCollectionResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        $filters = UserNotificationFilter::createFromArray(array_merge(
            $args->offsetGet('filters') ?? [],
            ['userId' => $user->getId()],
        ));

        return $this->notificationService->getUserNotifications($filters);
    }
}
