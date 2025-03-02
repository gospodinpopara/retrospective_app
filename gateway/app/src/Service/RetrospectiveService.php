<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Input\Retrospective\RetrospectiveCreateInput;
use App\DTO\Input\Retrospective\RetrospectiveUpdateInput;
use App\DTO\Response\Retrospective\RetrospectiveCreateMutationResponse;
use App\DTO\Response\Retrospective\RetrospectiveUpdateMutationResponse;
use App\Entity\Retrospective;
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
        // Validate input
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

        if ($this->isUserRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospective->getId()) === false) {
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

        if ($this->isUserRetrospectiveOwner(userId: $user->getId(), retrospectiveId: $retrospective->getId()) === false) {
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
    public function isUserRetrospectiveOwner(int $userId, int $retrospectiveId): bool
    {
        return $this->retrospectiveRepository->isRetrospectiveOwner(userId: $userId, retrospectiveId: $retrospectiveId);
    }
}
