<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\UserNotification;
use App\Service\NotificationService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TestingController extends AbstractController
{

    public function __construct(
    ) { }

    /**
     * @return JsonResponse
     */
    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        return new JsonResponse(["OK"]);
    }
}
