<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Input\Card\CardCreateInput;
use App\DTO\Input\Card\CardUpdateInput;
use App\DTO\Response\RetrospectiveCard\RetrospectiveCardCreateMutationResponse;
use App\DTO\Response\RetrospectiveCard\RetrospectiveCardUpdateMutationResponse;
use App\Entity\Card;
use App\Entity\CardUpvote;
use App\Entity\Retrospective;
use App\Entity\RetrospectiveParticipant;
use App\Entity\User;
use App\Exception\RetrospectiveNotActiveException;
use App\Repository\CardRepository;
use App\Repository\CardUpvoteRepository;
use App\Repository\RetrospectiveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RetrospectiveCardService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RetrospectiveRepository $retrospectiveRepository,
        private readonly RetrospectiveService $retrospectiveService,
        private readonly CardRepository $cardRepository,
        private readonly CardUpvoteRepository $cardUpvoteRepository
    ) {
    }

    /**
     * @param int  $cardId
     * @param User $user
     *
     * @return Card
     */
    public function getRetrospectiveCard(int $cardId, User $user): Card
    {
        $card = $this->cardRepository->find($cardId);

        if ($card === null) {
            throw new NotFoundHttpException("Card with ID {$cardId} not found.");
        }

        $retrospective = $card->getRetrospective();

        $isOwner = $this->retrospectiveService->isRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospective->getId());
        $isParticipant = $this->retrospectiveService->isUserRetrospectiveParticipant(userId: $user->getId(), retrospectiveId: $retrospective->getId(), status: RetrospectiveParticipant::STATUS_ACCEPTED);

        if (!$isOwner && !$isParticipant) {
            throw new AccessDeniedException('You are not authorized to access this retrospective.');
        }

        return $card;
    }

    /**
     * @param CardCreateInput $input
     * @param User            $user
     *
     * @return RetrospectiveCardCreateMutationResponse
     *
     * @throws RetrospectiveNotActiveException
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function createRetrospectiveCard(CardCreateInput $input, User $user): RetrospectiveCardCreateMutationResponse
    {
        $retrospective = $this->retrospectiveRepository->find($input->getRetrospectiveId());

        if ($retrospective === null) {
            throw new NotFoundHttpException("Retrospective with ID {$input->getRetrospectiveId()} not found.");
        }

        if ($retrospective->getStatus() !== Retrospective::STATUS_ACTIVE) {
            throw new RetrospectiveNotActiveException(message: 'Retrospective is not active.');
        }

        $isOwner = $this->retrospectiveService->isRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $input->getRetrospectiveId());
        $isParticipant = $this->retrospectiveService->isUserRetrospectiveParticipant(userId: $user->getId(), retrospectiveId: $input->getRetrospectiveId(), status: RetrospectiveParticipant::STATUS_ACCEPTED);

        if (!$isOwner && !$isParticipant) {
            throw new AccessDeniedException('You are not authorized to access this retrospective.');
        }

        $card = new Card();
        $card->setTitle($input->getTitle())
            ->setDescription($input->getDescription())
            ->setType($input->getType())
            ->setRetrospective($retrospective)
            ->setUser($user);

        $this->entityManager->persist($card);
        $this->entityManager->flush();

        return RetrospectiveCardCreateMutationResponse::success($card);
    }

    /**
     * @param CardUpdateInput $input
     * @param User            $user
     *
     * @return RetrospectiveCardUpdateMutationResponse
     *
     * @throws RetrospectiveNotActiveException
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function updateRetrospectiveCard(CardUpdateInput $input, User $user): RetrospectiveCardUpdateMutationResponse
    {
        $card = $this->entityManager->getRepository(Card::class)->find($input->getCardId());

        if ($card === null) {
            throw new NotFoundHttpException("Card with ID {$input->getCardId()} not found.");
        }

        $retrospective = $card->getRetrospective();

        if ($retrospective->getStatus() !== Retrospective::STATUS_ACTIVE) {
            throw new RetrospectiveNotActiveException(message: 'Retrospective is not active.');
        }

        $isCardOwner = $card->getUser()->getId() === $user->getId();
        $isRetrospectiveOwner = $retrospective->getOwner()->getId() === $user->getId();

        if (!$isCardOwner && !$isRetrospectiveOwner) {
            throw new AccessDeniedException('You are not authorized to delete this card.');
        }

        if ($input->getTitle()) {
            $card->setTitle($input->getTitle());
        }

        if ($input->getDescription()) {
            $card->setDescription($input->getDescription());
        }

        if ($input->getType()) {
            $card->setType($input->getType());
        }

        $this->entityManager->persist($card);
        $this->entityManager->flush();

        return RetrospectiveCardUpdateMutationResponse::success($card);
    }

    /**
     * @param int  $id
     * @param User $user
     *
     * @return bool
     *
     * @throws RetrospectiveNotActiveException
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function deleteRetrospectiveCard(int $id, User $user): bool
    {
        $card = $this->entityManager->getRepository(Card::class)->find($id);

        if ($card === null) {
            throw new NotFoundHttpException("Card with ID {$id} not found.");
        }

        $retrospective = $card->getRetrospective();

        if ($retrospective->getStatus() !== Retrospective::STATUS_ACTIVE) {
            throw new RetrospectiveNotActiveException(message: 'Retrospective is not active.');
        }

        $isCardOwner = $card->getUser()->getId() === $user->getId();
        $isRetrospectiveOwner = $retrospective->getOwner()->getId() === $user->getId();

        if (!$isCardOwner && !$isRetrospectiveOwner) {
            throw new AccessDeniedException('You are not authorized to delete this card.');
        }

        $this->entityManager->remove($card);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int  $cardId
     * @param User $user
     *
     * @return bool
     *
     * @throws RetrospectiveNotActiveException
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function toggleRetrospectiveCardUpvote(int $cardId, User $user): bool
    {
        $card = $this->cardRepository->find($cardId);

        if ($card === null) {
            throw new NotFoundHttpException("Card with ID {$cardId} not found.");
        }

        if ($card->getUser()->getId() === $user->getId()) {
            throw new AccessDeniedException('You cannot upvote your own card.');
        }

        $retrospective = $card->getRetrospective();

        if ($retrospective->getStatus() !== Retrospective::STATUS_ACTIVE) {
            throw new RetrospectiveNotActiveException(message: 'Retrospective is not active.');
        }

        $isOwner = $this->retrospectiveService->isRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospective->getId());
        $isParticipant = $this->retrospectiveService->isUserRetrospectiveParticipant(userId: $user->getId(), retrospectiveId: $retrospective->getId(), status: RetrospectiveParticipant::STATUS_ACCEPTED);

        if (!$isOwner && !$isParticipant) {
            throw new AccessDeniedException('You are not authorized to access this retrospective.');
        }

        $existingUpvote = $this->cardUpvoteRepository->findOneBy([
            'card' => $card,
            'user' => $user,
        ]);

        if ($existingUpvote) {
            $this->entityManager->remove($existingUpvote);
            $this->entityManager->flush();

            return true;
        }

        $cardUpvote = new CardUpvote();
        $cardUpvote->setCard($card)
            ->setUser($user);

        $this->entityManager->persist($cardUpvote);
        $this->entityManager->flush();

        return true;
    }
}
