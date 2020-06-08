<?php

namespace App\GraphQL\Type\Ots\Messages;

use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\Type;

class MessageAggregateType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessageAggregate',
        'description' => 'A message aggregate type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ],
            'subject' => [
                'type' => Type::string(),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'main_id' => [
                'type' => Type::int(),
            ],
            'users' => [
                'type' => Type::listOf(Type::string()),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'count' => [
                'type' => Type::int()
            ],
            'read' => [
                'type' => Type::int()
            ],
        ];
    }
}