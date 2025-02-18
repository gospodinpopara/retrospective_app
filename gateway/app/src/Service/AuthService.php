<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\EmailVerificationToken;
use App\Entity\RefreshToken;
use App\Entity\User;
use App\Model\SiteMessageModel;
use App\Repository\EmailVerificationTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

class AuthService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EmailVerificationTokenRepository $emailVerificationTokenRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function invalidateUserRefreshTokens(User $user): void
    {
        $refreshTokens = $this->entityManager->getRepository(RefreshToken::class)
            ->findBy(['username' => $user->getUserIdentifier()]);

        foreach ($refreshTokens as $refreshToken) {
            $this->entityManager->remove($refreshToken);
        }

        $this->entityManager->flush();
    }

    /**
     * @throws RandomException
     */
    public function createEmailVerificationToken(User $user): EmailVerificationToken
    {
        $token = new EmailVerificationToken();
        $token->setToken(bin2hex(random_bytes(32)));
        $token->setExpiresAt(new \DateTimeImmutable('+1 hour'));
        $token->setUser($user);

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    /**
     * @param string $email
     *
     * @return array
     */
    public function resendEmailVerificationToken(string $email): array
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Email verification token sent.',
                'messageType' => SiteMessageModel::MESSAGE_WARNING,
            ];
        }

        if (\in_array($user->getAccountStatus(), [User::ACCOUNT_STATUS_BANNED, User::ACCOUNT_STATUS_SUSPENDED], true)) {
            return [
                'success' => false,
                'message' => 'Cannot activate user, account is suspended, status: '.$user->getAccountStatus(),
                'messageType' => SiteMessageModel::MESSAGE_INFO,
            ];
        }

        if ($user->getAccountStatus() === User::ACCOUNT_STATUS_ACTIVE && $user->isEmailVerified()) {
            return [
                'success' => false,
                'message' => 'Your account is already verified.',
                'messageType' => SiteMessageModel::MESSAGE_INFO,
            ];
        }

        // $token = $this->createEmailVerificationToken($user);
        // TODO -> generate token and send email [Independent mailer service implementation]

        return [
            'success' => true,
            'message' => 'Verification email successfully sent, check you inbox',
            'messageType' => SiteMessageModel::MESSAGE_SUCCESS,
        ];
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function activateUser(User $user): void
    {
        $user->setIsEmailVerified(true);
        $user->setAccountStatus(User::ACCOUNT_STATUS_ACTIVE);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param string $verificationToken
     *
     * @return array
     */
    public function emailTokenVerification(string $verificationToken): array
    {
        $emailVerificationToken = $this->emailVerificationTokenRepository->findOneBy(['token' => $verificationToken]);

        if ($emailVerificationToken === null) {
            return [
                'success' => false,
                'message' => 'Email verification failed, invalid token provided.',
                'messageType' => SiteMessageModel::MESSAGE_WARNING,
            ];
        }

        if ($emailVerificationToken->getExpiresAt() < new \DateTimeImmutable()) {
            return [
                'success' => false,
                'message' => 'Email verification failed, token expired.',
                'messageType' => SiteMessageModel::MESSAGE_WARNING,
            ];
        }

        $user = $emailVerificationToken->getUser();

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Cannot associate verification token to user.',
                'messageType' => SiteMessageModel::MESSAGE_WARNING,
            ];
        }

        if (\in_array($user->getAccountStatus(), [User::ACCOUNT_STATUS_BANNED, User::ACCOUNT_STATUS_SUSPENDED], true)) {
            return [
                'success' => false,
                'message' => 'Cannot activate user, account is suspended, status: '.$user->getAccountStatus(),
                'messageType' => SiteMessageModel::MESSAGE_INFO,
            ];
        }

        $this->activateUser($user);

        return [
            'success' => true,
            'message' => 'User successfully verified and activated.',
            'messageType' => SiteMessageModel::MESSAGE_SUCCESS,
        ];
    }
}
