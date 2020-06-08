<?php

namespace App\GraphQL\Type\Ots\Messages;

use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\Type;

class MessageType extends SimpleMessageType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessage',
        'description' => 'Detailed message info type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return parent::fields() + $this->accessFilter([
            'read' => [
                'type' => Type::int()
            ],
            'read_at' => [
                'type' => Type::int()
            ],
            'last_at' => [
                'type' => Type::int()
            ],
            'count' => [
                'type' => Type::int()
            ],
            'user' => [
                'type' => \GraphQL::type('OtsMessageUsers')
            ],
            'users' => [
                'type' => Type::listOf(\GraphQL::type('OtsMessageUsers')),
            ]
        ]);
    }
}