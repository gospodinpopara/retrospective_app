<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\DTO\Filter\RetrospectiveInvitesFilter;
use App\DTO\Response\Retrospective\RetrospectiveCollectionResponse;
use App\Model\RetrospectiveInvite;
use App\Security\AuthorizationTrait;
use App\Service\RetrospectiveParticipantService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RetrospectiveParticipantsResolver implements QueryInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly RetrospectiveParticipantService $retrospectiveParticipantService,
        private readonly Security $security
    ) {
    }

    /**
     * @param Argument $args
     *
     * @return RetrospectiveInvite
     */
    public function getRetrospectiveInvite(Argument $args): RetrospectiveInvite
    {
        $user = $this->getAuthenticatedUser($this->security);

        return $this->retrospectiveParticipantService->getRetrospectiveInvite($args->offsetGet('id'), $user);
    }

    /**
     * @param Argument $args
     *
     * @return RetrospectiveCollectionResponse
     */
    public function getRetrospectiveInvites(Argument $args): RetrospectiveCollectionResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        return $this->retrospectiveParticipantService->getRetrospectiveInvites(
            filter: RetrospectiveInvitesFilter::createFromArray($args->offsetGet('filters')),
            user: $user,
        );
    }
}
