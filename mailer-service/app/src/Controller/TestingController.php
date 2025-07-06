<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class TestingController extends AbstractController
{
    public function __construct(
        private readonly MailerService $mailerService
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/testing', name: 'app_testing')]
    public function index(): Response
    {
        $this->mailerService->sendEmail(
            to: 'recipient@example.com',
            subject: 'Welcome to Our Service',
            templateName: 'email_verification',
            templateData: [
                'name' => 'John Doe',
                'activationLink' => 'https://example.com/activate?token=12345',
            ],
            from: 'noreply@example.com',
            fromName: 'Example Service',
        );

        dd('XD');
    }
}
