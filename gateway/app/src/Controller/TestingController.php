<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class TestingController extends AbstractController
{
    public function __construct(
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {

        return new JsonResponse(["OK"]);
    }
}
