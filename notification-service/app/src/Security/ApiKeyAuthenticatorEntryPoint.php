<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * ApiKeyAuthenticatorEntryPoint is responsible for handling authentication challenges
 * when an unauthenticated user tries to access a protected resource that requires an API key.
 * It returns a JSON response with a 401 Unauthorized status and a message indicating that
 * authentication is required.
 */
class ApiKeyAuthenticatorEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $data = [
            'message' => 'Authentication Required (Please provide a valid X-API-KEY header).',
        ];

        if ($authException) {
            $data['detail'] = $authException->getMessageKey();
        }

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
