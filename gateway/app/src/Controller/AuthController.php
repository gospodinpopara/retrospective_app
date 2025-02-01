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
        private readonly EmailVerificationTokenRepository $emailVerificationTokenRepository,
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

    #[Route('/api/email_verification', name: 'app_email_verification', methods: ['GET'])]
    public function emailVerificationToken(Request $request): JsonResponse
    {
        $verificationToken = $request->query->get('verification_token');

        $token = $this->emailVerificationTokenRepository->findOneBy(['token' => $verificationToken]);

        if($token === null) {
            return new JsonResponse(
                ['message' => 'Email verification failed, invalid token provided.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if($token->getExpiresAt() < new \DateTimeImmutable()) {
            return new JsonResponse(
                ['message' => 'Email verification failed, token expired.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $token->getUser();

        if($user === null) {
            return new JsonResponse(
                ['message' => 'Cannot associate verification token to user.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (in_array($user->getAccountStatus(), [User::ACCOUNT_STATUS_BANNED, User::ACCOUNT_STATUS_SUSPENDED], true)) {
            return new JsonResponse(
                ['message' => 'Cannot activate user, account is suspended.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->authService->activateUser($user);

        return new JsonResponse(
            ['message' => 'Email successfully verified. Your account is now active.'],
            Response::HTTP_OK
        );
    }
}