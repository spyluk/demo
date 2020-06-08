<?php

namespace App\GraphQL\Type\Ots\Messages;

use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\Type;

class MessagesType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessages',
        'description' => 'A messages type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'pagination' =>  [
                'type' => \GraphQL::type('Pagination')
            ],
            'items' => [
                'type' => Type::listOf(\GraphQL::type('OtsMessage'))
            ],
            'tags' => [
                'type' => Type::listOf(Type::string())
            ]
        ];

//        'structure' => [
//        'type' => Type::arr(),
//        'description' => 'Visual system structure'
//    ]

    }
}