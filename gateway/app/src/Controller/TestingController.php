<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\RetrospectiveParticipantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class TestingController extends AbstractController
{
    public function __construct(
        //        private readonly Security $security,
        //        private readonly RetrospectiveParticipantService $retrospectiveParticipantService,
        //        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/testing', name: 'testing')]
    public function test(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
