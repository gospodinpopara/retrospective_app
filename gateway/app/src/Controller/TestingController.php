<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class TestingController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        return new JsonResponse([
            'action' => 'Auth check',
            'success' => !($user === null),
            'userId' => $user?->getId(),
        ]);
    }
}
