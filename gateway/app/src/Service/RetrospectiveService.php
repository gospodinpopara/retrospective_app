<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Filter\RetrospectiveFilter;
use App\DTO\Input\Retrospective\RetrospectiveCreateInput;
use App\DTO\Input\Retrospective\RetrospectiveUpdateInput;
use App\DTO\Response\Retrospective\RetrospectiveCollectionResponse;
use App\DTO\Response\Retrospective\RetrospectiveCreateMutationResponse;
use App\DTO\Response\Retrospective\RetrospectiveUpdateMutationResponse;
use App\Entity\Retrospective;
use App\Entity\RetrospectiveParticipant;
use App\Entity\User;
use App\Repository\RetrospectiveRepository;
use App\Utils\ValidationErrorFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RetrospectiveService
{
    use ValidationErrorFormatter;

    public function __construct(
        private readonly RetrospectiveRepository $retrospectiveRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @param RetrospectiveCreateInput $retrospectiveCreateInput
     * @param User                     $user
     *
     * @return RetrospectiveCreateMutationResponse
     */
    public function createRetrospective(RetrospectiveCreateInput $retrospectiveCreateInput, User $user): RetrospectiveCreateMutationResponse
    {
        $validationErrors = $this->validator->validate($retrospectiveCreateInput);

        if (\count($validationErrors) > 0) {
            return RetrospectiveCreateMutationResponse::failure(errors: $this->formatValidationErrors($validationErrors));
        }

        $retrospective = new Retrospective();
        $retrospective->setTitle($retrospectiveCreateInput->getTitle());
        $retrospective->setDescription($retrospectiveCreateInput->getDescription());
        $retrospective->setStartTime($retrospectiveCreateInput->getStartTime());
        $retrospective->setStatus(Retrospective::STATUS_SCHEDULED);
        $retrospective->setOwner($user);

        $this->entityManager->persist($retrospective);
        $this->entityManager->flush();

        return RetrospectiveCreateMutationResponse::success($retrospective);
    }

    /**
     * @param int                      $id
     * @param RetrospectiveUpdateInput $retrospectiveUpdateInput
     * @param User                     $user
     *
     * @return RetrospectiveUpdateMutationResponse
     */
    public function updateRetrospective(int $id, RetrospectiveUpdateInput $retrospectiveUpdateInput, User $user): RetrospectiveUpdateMutationResponse
    {
        $retrospective = $this->retrospectiveRepository->find($id);

        if ($retrospective === null) {
            throw new NotFoundHttpException(message: "Retrospective resource with id: {$id} not found");
        }

        if ($this->isRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospective->getId()) === false) {
            throw new AccessDeniedException('You are not authorized to modify this retrospective.');
        }

        $validationErrors = $this->validator->validate($retrospectiveUpdateInput);

        if (\count($validationErrors) > 0) {
            return RetrospectiveUpdateMutationResponse::failure(errors: $this->formatValidationErrors($validationErrors));
        }

        // Only update fields that are defined and not null
        if ($retrospectiveUpdateInput->getTitle() !== null) {
            $retrospective->setTitle($retrospectiveUpdateInput->getTitle());
        }

        if ($retrospectiveUpdateInput->getDescription() !== null) {
            $retrospective->setDescription($retrospectiveUpdateInput->getDescription());
        }

        if ($retrospectiveUpdateInput->getStartTime() !== null) {
            $retrospective->setStartTime($retrospectiveUpdateInput->getStartTime());
        }

        if ($retrospectiveUpdateInput->getStatus() !== null) {
            $retrospective->setStatus($retrospectiveUpdateInput->getStatus());
        }

        $this->entityManager->persist($retrospective);
        $this->entityManager->flush();

        return RetrospectiveUpdateMutationResponse::success(retrospective: $retrospective);
    }

    /**
     * @param int  $id
     * @param User $user
     *
     * @return bool
     */
    public function deleteRetrospective(int $id, User $user): bool
    {
        $retrospective = $this->retrospectiveRepository->find($id);

        if ($retrospective === null) {
            throw new NotFoundHttpException(message: "Retrospective resource with id: {$id} not found");
        }

        if ($this->isRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospective->getId()) === false) {
            throw new AccessDeniedException('You are not authorized to modify this retrospective.');
        }

        $this->entityManager->remove($retrospective);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $userId
     * @param int $retrospectiveId
     *
     * @return bool
     */
    public function isRetrospectiveOwner(int $userId, int $retrospectiveId): bool
    {
        return $this->retrospectiveRepository->isUserRetrospectiveOwner(userId: $userId, retrospectiveId: $retrospectiveId);
    }

    /**
     * @param int         $userId
     * @param int         $retrospectiveId
     * @param string|null $status
     *
     * @return bool
     */
    public function isUserRetrospectiveParticipant(int $userId, int $retrospectiveId, ?string $status = null): bool
    {
        return $this->retrospectiveRepository->isUserRetrospectiveParticipant(userId: $userId, retrospectiveId: $retrospectiveId, status: $status);
    }

    /**
     * @param int  $retrospectiveId
     * @param User $user
     *
     * @return Retrospective
     */
    public function getUserRetrospective(int $retrospectiveId, User $user): Retrospective
    {
        $retrospective = $this->retrospectiveRepository->find($retrospectiveId);

        if ($retrospective === null) {
            throw new NotFoundHttpException(message: "Retrospective resource with id: {$retrospectiveId} not found");
        }

        $isOwner = $this->isRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospectiveId);
        $isParticipant = $this->isUserRetrospectiveParticipant(userId: $user->getId(), retrospectiveId: $retrospectiveId, status: RetrospectiveParticipant::STATUS_ACCEPTED);

        if (!$isOwner && !$isParticipant) {
            throw new AccessDeniedException('You are not authorized to access this retrospective.');
        }

        return $retrospective;
    }

    /**
     * @param int $retrospectiveId
     *
     * @return Retrospective
     */
    public function getRetrospective(int $retrospectiveId): Retrospective
    {
        return $this->retrospectiveRepository->find($retrospectiveId);
    }

    /**
     * @param RetrospectiveFilter $filter
     * @param User                $user
     *
     * @return RetrospectiveCollectionResponse
     */
    public function getUserRetrospectives(RetrospectiveFilter $filter, User $user): RetrospectiveCollectionResponse
    {
        $collection = $this->retrospectiveRepository->getUserRetrospectives($filter, $user->getId());

        return new RetrospectiveCollectionResponse(
            data: $collection['items'],
            currentPage: $collection['currentPage'],
            itemsPerPage: $collection['itemsPerPage'],
            totalItems: $collection['totalItems'],
            totalPages: $collection['totalPages'],
        );
    }
}
