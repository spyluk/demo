<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;

class PaginationType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'Pagination',
        'description' => 'A pagination'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'current_page' => [
                'type' => Type::int()
            ],
            'last_page' => [
                'type' => Type::int()
            ],
            'per_page' => [
                'type' => Type::int()
            ],
            'total' => [
                'type' => Type::int()
            ],
            'pages' => [
                'type' => Type::int()
            ],
        ];
    }

}