<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Filter\ParticipantFilter;
use App\DTO\Response\Participants\ParticipantCollectionCollectionResponse;
use App\Entity\User;
use App\Repository\UserRepository;

class ParticipantService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @param ParticipantFilter $filter
     * @param User              $user
     *
     * @return ParticipantCollectionCollectionResponse
     */
    public function search(ParticipantFilter $filter, User $user): ParticipantCollectionCollectionResponse
    {
        $searchResults = $this->userRepository->searchParticipants($filter, $user);

        return new ParticipantCollectionCollectionResponse(
            data: $searchResults['items'],
            currentPage: $searchResults['currentPage'],
            itemsPerPage: $searchResults['itemsPerPage'],
            totalItems: $searchResults['totalItems'],
            totalPages: $searchResults['totalPages'],
        );
    }
}
