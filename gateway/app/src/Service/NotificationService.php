<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Filter\UserNotificationFilter;
use App\DTO\Response\Notification\LatestUserNotificationsResponse;
use App\DTO\Response\Notification\UserNotificationCollectionResponse;
use App\Message\UserNotificationMessage;
use App\Model\UserNotification;
use App\Utils\ApiHelper;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService
{
    public function __construct(
        private readonly HttpClientInterface $notificationClient,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @param int $userId
     *
     * @return LatestUserNotificationsResponse
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \DateMalformedStringException
     */
    public function getLatestUserNotifications(int $userId): LatestUserNotificationsResponse
    {
        $response = $this->notificationClient->request('GET', "/api/user_notifications/latest_site_notifications/{$userId}");

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException(message: 'Failed to fetch latest site notifications', code: $response->getStatusCode());
        }

        $latestSiteNotifications = $response->toArray();

        $notificationList = $this->formatUserNotificationList($latestSiteNotifications['notifications']);

        return new LatestUserNotificationsResponse(
            notificationList: $notificationList,
            notAckedCount: $latestSiteNotifications['notAckedCount'],
            notVisitedCount: $latestSiteNotifications['notVisitedCount'],
            totalItems: \count($notificationList),
        );
    }

    /**
     * @param UserNotificationFilter $filters
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
    public function getUserNotifications(UserNotificationFilter $filters): UserNotificationCollectionResponse
    {
        $query = [
            'userId' => $filters->getUserId(),
            'page' => $filters->getPage(),
            'itemsPerPage' => $filters->getItemsPerPage(),
        ];

        $response = $this->notificationClient->request('GET', '/api/user_notifications', [
            'query' => $query,
            'headers' => [
                'Accept' => 'application/ld+json',
            ],
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException(message: 'Failed to fetch user notifications', code: $response->getStatusCode());
        }

        $notificationsData = $response->toArray();
        $notificationList = $this->formatUserNotificationList($notificationsData['member']);
        $pagination = ApiHelper::convertJsonLdPagination($response->toArray());

        return new UserNotificationCollectionResponse(
            data: $notificationList,
            currentPage: $pagination->getCurrentPage(),
            itemsPerPage: $pagination->getItemsPerPage(),
            totalItems: $pagination->getTotalItems(),
            totalPages: $pagination->getTotalPages(),
        );
    }

    /**
     * @param array $list
     *
     * @return UserNotification[]
     *
     * @throws \DateMalformedStringException
     */
    private function formatUserNotificationList(array $list): array
    {
        $userNotificationList = [];

        foreach ($list as $userNotification) {
            $notification = new UserNotification()
                ->setSiteNotificationId($userNotification['notification']['id'] ?? null)
                ->setUserNotificationId($userNotification['id'] ?? null)
                ->setTitle($userNotification['notification']['title'] ?? null)
                ->setBody($userNotification['notification']['body'] ?? null)
                ->setLink($userNotification['notification']['link'] ?? null)
                ->setType($userNotification['notification']['type'] ?? null)
                ->setVisited($userNotification['visited'] ?? null)
                ->setServed($userNotification['served'] ?? null)
                ->setGeneric($userNotification['notification']['generic'] ?? null);

            if (!empty($userNotification['notification']['dateFrom'])) {
                $notification->setDateFrom(new \DateTime($userNotification['notification']['dateFrom']));
            }

            if (!empty($userNotification['notification']['dateTo'])) {
                $notification->setDateTo(new \DateTime($userNotification['notification']['dateTo']));
            }

            if (!empty($userNotification['createdAt'])) {
                $notification->setCreatedAt(new \DateTime($userNotification['createdAt']));
            }

            $userNotificationList[] = $notification;
        }

        return $userNotificationList;
    }

    /**
     * Dispatches a user notification message to RabbitMQ.
     *
     * @param int $userId The ID of the user to notify.
     * @param string $title The title of the notification.
     * @param string $body The main content/body of the notification.
     * @param string $link The URL link associated with the notification.
     * @param string $type The type of notification (e.g., 'email', 'sms', 'in_app').
     * @param DateTimeInterface $dateFrom The date from which the notification is valid.
     * @param DateTimeInterface $dateTo The date until which the notification is valid.
     * @param DateTimeInterface|null $eolDate (Optional) End of life date for the notification.
     * @throws ExceptionInterface
     */
    public function sendUserNotification(
        int $userId,
        string $title,
        string $body,
        string $link,
        string $type,
        DateTimeInterface $dateFrom,
        DateTimeInterface $dateTo,
        ?DateTimeInterface $eolDate = null
    ): void {
        $message = new UserNotificationMessage(
            $userId,
            $title,
            $body,
            $link,
            $type,
            $dateFrom,
            $dateTo,
            $eolDate
        );

        $this->messageBus->dispatch(
            $message,
            [new AmqpStamp('notifications.user.send')]
        );

    }
}
