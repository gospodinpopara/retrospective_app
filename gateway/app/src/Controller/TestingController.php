<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class TestingController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly AuthService $authService
    ) { }

    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        dd($user);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}