<?php

namespace App\GraphQL\Query\User;

use App\GraphQL\Query\BaseQuery;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL;
use App\Models\User;

class LogoutQuery extends BaseQuery
{
    protected $attributes = [
        'name' => 'UserLoginQuery',
        'description' => 'A User login query'
    ];

    /**
     * @return mixed
     */
    public function type(): Type
    {
        return Type::string();
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return null
     * @throws \Exception
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if($this->user() && $this->user()->token()) {
            $this->user()->token()->revoke();
            $this->user()->token()->delete();

            return 'success';
        }

        return 'error';
    }
}