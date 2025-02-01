<?php

namespace App\Service;

use App\Entity\EmailVerificationToken;
use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

class AuthService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) { }

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
     * @param User $user
     * @return void
     */
    public function activateUser(User $user): void
    {
        $user->setIsEmailVerified(true);
        $user->setAccountStatus(User::ACCOUNT_STATUS_ACTIVE);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}