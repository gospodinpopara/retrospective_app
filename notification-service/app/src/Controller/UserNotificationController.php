<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserNotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
class UserNotificationController extends AbstractController
{
    public function __construct(
        private readonly UserNotificationRepository $userNotificationRepository
    ) {
    }

    #[IsGranted('ROLE_ADMIN')]
    public function getNotVisitedCount(int $userId): JsonResponse
    {
        return $this->json([
            'notVisitedCount' => $this->userNotificationRepository->getNotVisitedCount($userId),
        ]);
    }
}
