<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\DTO\Filter\ParticipantFilter;
use App\DTO\Response\Participants\ParticipantCollectionCollectionResponse;
use App\Security\AuthorizationTrait;
use App\Service\ParticipantService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ParticipantResolver implements QueryInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly ParticipantService $participantService,
        private readonly Security $security
    ) {
    }

    public function getParticipants(Argument $args): ParticipantCollectionCollectionResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        return $this->participantService->search(filter: ParticipantFilter::createFromArray($args->offsetGet('filters')), user: $user);
    }
}
