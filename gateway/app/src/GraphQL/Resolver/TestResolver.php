<?php

namespace App\GraphQL\Resolver;

use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Random\RandomException;
use Symfony\Bundle\SecurityBundle\Security;

class TestResolver implements QueryInterface
{

    public function __construct(
        private readonly Security $security
    )
    {
    }


    /**
     * @throws RandomException
     */
    public function getTest(): array
    {
        $user = $this->security->getUser();

        dd($user);

        return [
            "name" => "Dummy Resolver"
        ];
    }

}