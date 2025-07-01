<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestingController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/api/testing', name: 'app_testing')]
    public function index(): Response
    {
        return new JsonResponse([]);
    }
}
