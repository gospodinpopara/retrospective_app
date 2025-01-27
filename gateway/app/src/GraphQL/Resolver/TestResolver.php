<?php

namespace App\GraphQL\Resolver;

use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Random\RandomException;

class TestResolver implements QueryInterface
{

    /**
     * @throws RandomException
     */
    public function getTest(): array
    {
        return [
            "name" => "Dummy Resolver"
        ];
    }

}