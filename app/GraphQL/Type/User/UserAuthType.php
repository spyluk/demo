<?php

namespace App\GraphQL\Type\User;

use App\GraphQL\Type\UserType;
use GraphQL\Type\Definition\Type;

class UserAuthType extends UserType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'UserAuth',
        'description' => 'User registration/login'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return parent::fields() +
            $this->accessFilter([
            'token' => [
                'type' => Type::string(),
                'access' => true //user()->hasPermissionTo("user.token")
            ]
        ]);
    }
}