<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\LatestUserNotificationsDto;
use App\Repository\UserNotificationRepository;
use App\Service\SiteNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class UserNotificationController extends AbstractController
{
    public function __construct(
        private readonly UserNotificationRepository $userNotificationRepository,
        private readonly SiteNotificationService $siteNotificationService,
        private readonly NormalizerInterface $normalizer
    ) {
    }

    /**
     * @param int $userId
     *
     * @return JsonResponse
     */
    #[IsGranted('ROLE_ADMIN')]
    public function getNotVisitedCount(int $userId): JsonResponse
    {
        try {
            $notVisitedCount = $this->userNotificationRepository->getNotVisitedCount($userId);
        } catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => $e->getMessage()],
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse(
            data: ['notVisitedCount' => $notVisitedCount],
            status: Response::HTTP_OK,
        );
    }

    /**
     * @param int $userId
     *
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     */
    #[IsGranted('ROLE_ADMIN')]
    public function getLatestSiteNotifications(int $userId): JsonResponse
    {
        try {
            $notAckedCount = $this->userNotificationRepository->getNotAckedCount(userId: $userId);
            $notVisitedCount = $this->userNotificationRepository->getNotVisitedCount(userId: $userId);
            $latestNotifications = $this->userNotificationRepository->getLatestActiveUserNotifications(userId: $userId);

            // Convert pending generic notifications to personal notifications
            $this->siteNotificationService->convertPendingGenericNotificationsToPersonal($userId);

            // Set notifications as served
            $this->userNotificationRepository->setAllAsServed($userId);

            $responseDto = new LatestUserNotificationsDto(
                notAckedCount: $notAckedCount,
                notVisitedCount: $notVisitedCount,
                notifications: $latestNotifications,
            );

            $normalizedData = $this->normalizer->normalize($responseDto, 'json', ['groups' => 'notification']);
        } catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => $e->getMessage()],
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        return new JsonResponse(
            data: $normalizedData,
            status: Response::HTTP_OK,
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[IsGranted('ROLE_ADMIN')]
    public function setAllAsAck(Request $request): JsonResponse
    {
        try {
            $requestParameters = json_decode((string) $request->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if (!isset($requestParameters['userId']) || !\is_int($requestParameters['userId']) || $requestParameters['userId'] <= 0) {
                return new JsonResponse(
                    data: ['message' => 'Invalid or missing userId in the request body.'],
                    status: Response::HTTP_BAD_REQUEST,
                );
            }

            $userId = $requestParameters['userId'];

            $ackedCount = $this->userNotificationRepository->setAllAsAck($userId);

            return new JsonResponse(
                data: ['ackedCount' => $ackedCount],
                status: Response::HTTP_OK,
            );
        } catch (\JsonException $e) {
            return new JsonResponse(
                data: ['message' => 'Invalid JSON format: '.$e->getMessage()],
                status: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
