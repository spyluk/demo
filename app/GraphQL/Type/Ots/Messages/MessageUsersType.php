<?php

namespace App\GraphQL\Type\Ots\Messages;

use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\Type;

class MessageUsersType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessageUsers',
        'description' => 'A message users type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'user_id' => [
                'type' => Type::int()
            ],
            'first_name' => [
                'type' => Type::string(),
            ],
            'last_name' => [
                'type' => Type::string(),
            ]
        ];
    }
}