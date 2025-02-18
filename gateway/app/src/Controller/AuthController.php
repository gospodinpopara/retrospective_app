<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\EmailVerificationTokenRepository;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly AuthService $authService,
    ) { }

    #[Route('/api/token/refresh_token_invalidate', name: 'app_refresh_token_invalidate', methods: ['POST'])]
    public function invalidateRefreshToken(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse(
                ['message' => 'Unauthorized. User not found or not authenticated correctly.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $this->authService->invalidateUserRefreshTokens($user);

        return new JsonResponse(
            ['message' => 'Successfully logged out. All refresh tokens invalidated.'],
            Response::HTTP_OK
        );
    }
}