<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Entity\Retrospective;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class RetrospectiveResolver implements QueryInterface
{
    public function getRetrospectives(): Retrospective
    {
        return new Retrospective();
    }
}
