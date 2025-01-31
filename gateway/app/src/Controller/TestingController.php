<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class TestingController extends AbstractController
{

    public function __construct() {
    }

    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
        ]);
    }
}