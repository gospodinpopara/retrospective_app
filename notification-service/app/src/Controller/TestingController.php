<?php

namespace App\Controller;

use App\Security\AdminUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TestingController extends AbstractController
{

    public function __construct(
        private readonly Security $security
    )
    {
    }

    #[Route('/api/testing', name: 'app_testing')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        dd("ADMIN JESTE!!!");
    }
}
