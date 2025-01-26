<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TestingController extends AbstractController
{
    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        return new JsonResponse("Volume testing");
    }
}