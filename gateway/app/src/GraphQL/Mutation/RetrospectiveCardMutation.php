<?php

declare(strict_types=1);

namespace App\GraphQL\Mutation;

use App\DTO\Input\Card\CardCreateInput;
use App\DTO\Input\Card\CardUpdateInput;
use App\DTO\Response\RetrospectiveCard\RetrospectiveCardCreateMutationResponse;
use App\DTO\Response\RetrospectiveCard\RetrospectiveCardUpdateMutationResponse;
use App\Exception\RetrospectiveNotActiveException;
use App\Security\AuthorizationTrait;
use App\Service\RetrospectiveCardService;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RetrospectiveCardMutation implements MutationInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly Security $security,
        private readonly RetrospectiveCardService $retrospectiveCardService
    ) {
    }

    /**
     * @param Argument $args
     *
     * @return RetrospectiveCardCreateMutationResponse
     *
     * @throws UserError
     */
    public function createRetrospectiveCardMutation(Argument $args): RetrospectiveCardCreateMutationResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        try {
            return $this->retrospectiveCardService->createRetrospectiveCard(input: CardCreateInput::createFromArray($args->offsetGet('cardCreateInput')), user: $user);
        } catch (RetrospectiveNotActiveException) {
            throw new UserError(message: 'Retrospective is not active.', code: Response::HTTP_BAD_REQUEST);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Retrospective Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Access Denied: '.$exception->getMessage(), code: Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param Argument $args
     *
     * @return RetrospectiveCardUpdateMutationResponse
     *
     * @throws UserError
     */
    public function updateRetrospectiveCardMutation(Argument $args): RetrospectiveCardUpdateMutationResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        try {
            return $this->retrospectiveCardService->updateRetrospectiveCard(
                input: CardUpdateInput::createFromArray($args->offsetGet('cardUpdateInput')),
                user: $user,
            );
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Card Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        } catch (RetrospectiveNotActiveException) {
            throw new UserError(message: 'Retrospective is not active.', code: Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Access Denied: '.$exception->getMessage(), code: Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param Argument $args
     *
     * @return bool
     */
    public function deleteRetrospectiveCardMutation(Argument $args): bool
    {
        $user = $this->getAuthenticatedUser($this->security);

        $id = $args->offsetGet('id');

        try {
            return $this->retrospectiveCardService->deleteRetrospectiveCard(id: $id, user: $user);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Card Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        } catch (RetrospectiveNotActiveException) {
            throw new UserError(message: 'Retrospective is not active.', code: Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Access Denied: '.$exception->getMessage(), code: Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @param Argument $args
     *
     * @return bool
     *
     * @throws RetrospectiveNotActiveException
     */
    public function toggleRetrospectiveCardUpvoteMutation(Argument $args): bool
    {
        $user = $this->getAuthenticatedUser($this->security);

        try {
            return $this->retrospectiveCardService->toggleRetrospectiveCardUpvote(cardId: $args->offsetGet('cardId'), user: $user);
        } catch (NotFoundHttpException $exception) {
            throw new UserError(message: 'Card Not Found: '.$exception->getMessage(), code: Response::HTTP_NOT_FOUND);
        } catch (RetrospectiveNotActiveException) {
            throw new UserError(message: 'Retrospective is not active.', code: Response::HTTP_BAD_REQUEST);
        } catch (AccessDeniedException $exception) {
            throw new UserError(message: 'Access Denied: '.$exception->getMessage(), code: Response::HTTP_FORBIDDEN);
        }
    }
}
