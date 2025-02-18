<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private const array ACCOUNT_STATUS_MESSAGES = [
        User::ACCOUNT_STATUS_PENDING => 'Your account is pending approval.',
        User::ACCOUNT_STATUS_SUSPENDED => 'Your account is suspended.',
        User::ACCOUNT_STATUS_BANNED => 'Your account is banned.',
        User::ACCOUNT_STATUS_DELETED => 'Your account is deleted.',
    ];

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isEmailVerified()) {
            throw new CustomUserMessageAccountStatusException('Your email address is not verified.');
        }

        $accountStatus = $user->getAccountStatus();

        if ($accountStatus !== User::ACCOUNT_STATUS_ACTIVE) {
            $message = self::ACCOUNT_STATUS_MESSAGES[$accountStatus] ?? 'Your account is not active';
            throw new CustomUserMessageAccountStatusException($message);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
