<?php

namespace App\GraphQL\Type\Ots\Messages;

use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\Type;

class SimpleMessageType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsSimpleMessage',
        'description' => 'Only base message info type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return $this->accessFilter([
            'id' => [
                'type' => Type::int()
            ],
            'user_id' => [
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
            'created_at' => [
                'type' => Type::string(),
            ]
        ]);
    }
}