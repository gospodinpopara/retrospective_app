<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\DTO\Input\Retrospective\RetrospectiveCreateInput;
use App\DTO\Input\Retrospective\RetrospectiveUpdateInput;
use App\DTO\Response\Retrospective\RetrospectiveCreateMutationResponse;
use App\DTO\Response\Retrospective\RetrospectiveUpdateMutationResponse;
use App\Security\AuthorizationTrait;
use App\Service\RetrospectiveService;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class RetrospectiveMutation implements MutationInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly Security $security,
        private readonly RetrospectiveService $retrospectiveService
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function createRetrospectiveMutation(Argument $args): RetrospectiveCreateMutationResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        $retrospectiveCreateInputArgument = $args->offsetGet('retrospectiveCreateInput');

        if (empty($retrospectiveCreateInputArgument)) {
            throw new UserError(message: 'Missing retrospective input parameters', code: Response::HTTP_BAD_REQUEST);
        }

        $retrospectiveCreateInput = new RetrospectiveCreateInput(
            title: $retrospectiveCreateInputArgument['title'],
            description: $retrospectiveCreateInputArgument['description'],
            startTime: $retrospectiveCreateInputArgument['startTime'],
        );

        return $this->retrospectiveService->createRetrospective(retrospectiveCreateInput: $retrospectiveCreateInput, user: $user);
    }

    /**
     * @param Argument $args
     *
     * @return RetrospectiveUpdateMutationResponse
     */
    public function updateRetrospectiveMutation(Argument $args): RetrospectiveUpdateMutationResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        $id = $args->offsetGet('id');
        $inputData = $args->offsetGet('retrospectiveUpdateInput');

        $retrospectiveUpdateInput = new RetrospectiveUpdateInput(
            title: $inputData['title'] ?? null,
            description: $inputData['description'] ?? null,
            startTime: $inputData['startTime'] ?? null,
            status: $inputData['status'] ?? null,
        );

        try {
            return $this->retrospectiveService->updateRetrospective(id: $id, retrospectiveUpdateInput: $retrospectiveUpdateInput, user: $user);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Unauthorized: '.$exception->getMessage(), code: Response::HTTP_UNAUTHORIZED);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Argument $args
     *
     * @return bool
     */
    public function deleteRetrospectiveMutation(Argument $args): bool
    {
        $user = $this->getAuthenticatedUser($this->security);

        try {
            return $this->retrospectiveService->deleteRetrospective(id: $args->offsetGet('id'), user: $user);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Unauthorized: '.$exception->getMessage(), code: Response::HTTP_UNAUTHORIZED);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        }
    }
}
