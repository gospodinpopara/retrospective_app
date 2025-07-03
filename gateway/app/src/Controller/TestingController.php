<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TestingController extends AbstractController
{
    public function __construct(
    ) {
    }

    /**
     * @return JsonResponse
     *
     * @throws TransportExceptionInterface
     */
    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        return new JsonResponse(['OK']);
    }
}
