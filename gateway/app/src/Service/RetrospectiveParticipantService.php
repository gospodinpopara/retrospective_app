<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Input\RetrospectiveParticipant\InviteUserToRetrospectiveInput;
use App\DTO\Input\RetrospectiveParticipant\RemoveUserFromRetrospectiveInput;
use App\DTO\Input\RetrospectiveParticipant\UpdateRetrospectiveParticipantInput;
use App\DTO\Response\RetrospectiveParticipant\InviteRetrospectiveParticipantResponse;
use App\DTO\Response\RetrospectiveParticipant\UpdateRetrospectiveParticipantResponse;
use App\Entity\RetrospectiveParticipant;
use App\Entity\User;
use App\Repository\RetrospectiveParticipantRepository;
use App\Repository\RetrospectiveRepository;
use App\Repository\UserRepository;
use App\Utils\ValidationErrorFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RetrospectiveParticipantService
{
    use ValidationErrorFormatter;

    public function __construct(
        private readonly RetrospectiveParticipantRepository $retrospectiveParticipantRepository,
        private readonly RetrospectiveRepository $retrospectiveRepository,
        private readonly RetrospectiveService $retrospectiveService,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @param User                           $owner
     * @param InviteUserToRetrospectiveInput $inviteUserToRetrospectiveInput
     *
     * @return InviteRetrospectiveParticipantResponse
     */
    public function createRetrospectiveParticipant(User $owner, InviteUserToRetrospectiveInput $inviteUserToRetrospectiveInput): InviteRetrospectiveParticipantResponse
    {
        $retrospective = $this->retrospectiveRepository->find($inviteUserToRetrospectiveInput->getRetrospectiveId());

        if ($retrospective === null) {
            throw new NotFoundHttpException(message: "Retrospective resource with id: {$inviteUserToRetrospectiveInput->getRetrospectiveId()} not found");
        }

        if ($this->retrospectiveService->isUserRetrospectiveOwner(userId: $owner->getId(), retrospectiveId: $retrospective->getId()) === false) {
            throw new AccessDeniedException('You are not authorized to modify this retrospective.');
        }

        $participant = $this->userRepository->find($inviteUserToRetrospectiveInput->getParticipantId());

        if ($participant === null) {
            throw new NotFoundHttpException(message: "Participant with id: {$inviteUserToRetrospectiveInput->getParticipantId()} not found");
        }

        if ($participant->getId() === $owner->getId()) {
            return InviteRetrospectiveParticipantResponse::failure(errors: ['Cannot invite yourself to your own retrospective']);
        }

        $existingParticipation = $this->retrospectiveParticipantRepository->findOneBy([
            'retrospective' => $retrospective,
            'user' => $participant,
        ]);

        if ($existingParticipation !== null) {
            return InviteRetrospectiveParticipantResponse::failure(errors: ['Participant is already invited to this retrospective']);
        }

        $retrospectiveParticipant = new RetrospectiveParticipant();
        $retrospectiveParticipant->setRetrospective($retrospective);

        $retrospectiveParticipant->setUser($participant);
        $retrospectiveParticipant->setStatus(RetrospectiveParticipant::STATUS_PENDING);

        $this->entityManager->persist($retrospectiveParticipant);
        $this->entityManager->flush();

        return InviteRetrospectiveParticipantResponse::success(retrospectiveParticipant: $retrospectiveParticipant);
    }

    /**
     * @param User                             $owner
     * @param RemoveUserFromRetrospectiveInput $removeUserFromRetrospectiveInput
     *
     * @return bool
     */
    public function deleteRetrospectiveParticipant(User $owner, RemoveUserFromRetrospectiveInput $removeUserFromRetrospectiveInput): bool
    {
        $retrospective = $this->retrospectiveRepository->find($removeUserFromRetrospectiveInput->getRetrospectiveId());

        if ($retrospective === null) {
            throw new NotFoundHttpException(message: "Retrospective resource with id: {$removeUserFromRetrospectiveInput->getRetrospectiveId()} not found");
        }

        if ($this->retrospectiveService->isUserRetrospectiveOwner(userId: $owner->getId(), retrospectiveId: $retrospective->getId()) === false) {
            throw new AccessDeniedException('You are not authorized to modify this retrospective.');
        }

        $participant = $this->userRepository->find($removeUserFromRetrospectiveInput->getParticipantId());

        if ($participant === null) {
            throw new NotFoundHttpException(message: "Participant with id: {$removeUserFromRetrospectiveInput->getParticipantId()} not found");
        }

        $existingParticipation = $this->retrospectiveParticipantRepository->findOneBy([
            'retrospective' => $retrospective,
            'user' => $participant,
        ]);

        if ($existingParticipation === null) {
            throw new NotFoundHttpException(message: 'Participant is not a member of this retrospective');
        }

        if ($participant->getId() === $owner->getId()) {
            throw new AccessDeniedException('Cannot remove the retrospective owner');
        }

        $this->entityManager->remove($existingParticipation);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User                                $authorizedUser
     * @param UpdateRetrospectiveParticipantInput $input
     *
     * @return UpdateRetrospectiveParticipantResponse
     */
    public function updateRetrospectiveParticipant(User $authorizedUser, UpdateRetrospectiveParticipantInput $input): UpdateRetrospectiveParticipantResponse
    {
        $validationErrors = $this->validator->validate($input);

        if (\count($validationErrors) > 0) {
            return UpdateRetrospectiveParticipantResponse::failure(errors: $this->formatValidationErrors($validationErrors));
        }

        $retrospective = $this->retrospectiveRepository->find($input->getRetrospectiveId());

        if ($retrospective === null) {
            throw new NotFoundHttpException('Retrospective not found');
        }

        $participantUser = $this->userRepository->find($input->getParticipantId());

        if ($participantUser === null) {
            throw new NotFoundHttpException('Participant user not found');
        }

        $retrospectiveParticipant = $this->retrospectiveParticipantRepository->findOneBy([
            'user' => $participantUser,
            'retrospective' => $retrospective,
        ]);

        if ($retrospectiveParticipant === null) {
            throw new NotFoundHttpException("Participant not found or doesn't belong to this retrospective");
        }

        // Check permissions - only owner or the participant themselves can update resource
        $isOwner = $this->retrospectiveService->isUserRetrospectiveOwner($authorizedUser->getId(), $retrospective->getId());
        $isSelf = $participantUser->getId() === $authorizedUser->getId();

        if ($isOwner === false && $isSelf === false) {
            throw new AccessDeniedException('You can only update your own participation status or you must be the retrospective owner');
        }

        $retrospectiveParticipant->setStatus($input->getStatus());

        $this->entityManager->persist($retrospectiveParticipant);
        $this->entityManager->flush();

        return UpdateRetrospectiveParticipantResponse::success(retrospectiveParticipant: $retrospectiveParticipant);
    }
}
