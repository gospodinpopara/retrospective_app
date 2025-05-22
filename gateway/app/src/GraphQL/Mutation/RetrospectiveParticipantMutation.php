<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\DTO\Input\RetrospectiveParticipant\InviteUserToRetrospectiveInput;
use App\DTO\Input\RetrospectiveParticipant\RemoveUserFromRetrospectiveInput;
use App\DTO\Input\RetrospectiveParticipant\UpdateRetrospectiveParticipantInput;
use App\DTO\Response\RetrospectiveParticipant\InviteRetrospectiveParticipantResponse;
use App\DTO\Response\RetrospectiveParticipant\UpdateRetrospectiveParticipantResponse;
use App\Security\AuthorizationTrait;
use App\Service\RetrospectiveParticipantService;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RetrospectiveParticipantMutation implements MutationInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly Security $security,
        private readonly RetrospectiveParticipantService $retrospectiveParticipantService
    ) {
    }

    /**
     * @param Argument $args
     *
     * @return InviteRetrospectiveParticipantResponse
     */
    public function createRetrospectiveParticipantMutation(Argument $args): InviteRetrospectiveParticipantResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        $inviteUserToRetrospectiveInput = new InviteUserToRetrospectiveInput(
            retrospectiveId: $args->offsetGet('retrospectiveId'),
            participantId: $args->offsetGet('participantId'),
        );

        try {
            return $this->retrospectiveParticipantService->createRetrospectiveParticipant(owner: $user, inviteUserToRetrospectiveInput: $inviteUserToRetrospectiveInput);
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
    public function deleteRetrospectiveParticipantMutation(Argument $args): bool
    {
        $user = $this->getAuthenticatedUser($this->security);

        $removeUserFromRetrospectiveInput = new RemoveUserFromRetrospectiveInput(
            retrospectiveId: $args->offsetGet('retrospectiveId'),
            participantId: $args->offsetGet('participantId'),
        );

        try {
            return $this->retrospectiveParticipantService->deleteRetrospectiveParticipant(owner: $user, removeUserFromRetrospectiveInput: $removeUserFromRetrospectiveInput);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Unauthorized: '.$exception->getMessage(), code: Response::HTTP_UNAUTHORIZED);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Argument $args
     *
     * @return UpdateRetrospectiveParticipantResponse
     */
    public function updateRetrospectiveParticipantMutation(Argument $args): UpdateRetrospectiveParticipantResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        $updateRetrospectiveParticipantInput = new UpdateRetrospectiveParticipantInput(
            retrospectiveId: $args->offsetGet('retrospectiveId'),
            participantId: $args->offsetGet('participantId'),
            status: $args->offsetGet('status'),
        );

        try {
            return $this->retrospectiveParticipantService->updateRetrospectiveParticipant(
                authorizedUser: $user,
                input: $updateRetrospectiveParticipantInput,
            );
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Unauthorized: '.$exception->getMessage(), code: Response::HTTP_UNAUTHORIZED);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $exception) {
            throw new UserError(message: 'Invalid argument: '.$exception->getMessage(), code: Response::HTTP_BAD_REQUEST);
        }
    }
}
