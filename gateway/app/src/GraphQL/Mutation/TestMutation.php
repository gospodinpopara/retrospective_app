<?php

namespace App\GraphQL\Mutation;

use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class TestMutation implements MutationInterface, AliasedInterface
{

    public function testMutation(): array
    {
        return [
            "name" => "Dummy Mutation"
        ];
    }

    #[\Override]
    public static function getAliases(): array
    {
        return [];
    }

}


