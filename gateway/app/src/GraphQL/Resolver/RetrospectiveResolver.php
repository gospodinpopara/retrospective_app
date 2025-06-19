<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\DTO\Filter\RetrospectiveFilter;
use App\DTO\Response\Retrospective\RetrospectiveCollectionResponse;
use App\Entity\Retrospective;
use App\Security\AuthorizationTrait;
use App\Service\RetrospectiveService;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RetrospectiveResolver implements QueryInterface
{
    use AuthorizationTrait;

    public function __construct(
        private readonly RetrospectiveService $retrospectiveService,
        private readonly Security $security
    ) {
    }

    /**
     * @param Argument $argument
     *
     * @return Retrospective
     */
    public function getRetrospective(Argument $argument): Retrospective
    {
        $user = $this->getAuthenticatedUser($this->security);

        $retrospectiveId = $argument->offsetGet('id');

        if ($retrospectiveId === null) {
            throw new \InvalidArgumentException('Retrospective ID is required');
        }

        return $this->retrospectiveService->getUserRetrospective($retrospectiveId, $user);
    }

    /**
     * @param Argument $argument
     *
     * @return RetrospectiveCollectionResponse
     */
    public function getRetrospectives(Argument $argument): RetrospectiveCollectionResponse
    {
        $user = $this->getAuthenticatedUser($this->security);

        $retrospectiveFilters = RetrospectiveFilter::createFromArray($argument->offsetGet('filters'));

        return $this->retrospectiveService->getUserRetrospectives($retrospectiveFilters, $user);
    }
}
