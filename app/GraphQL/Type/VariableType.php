<?php

namespace App\GraphQL\Type;

use App\GraphQL\TypeDefination\Type;

class VariableType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'Variable',
        'description' => 'A variable type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'variables' => [
                'type' => Type::arr('variable'),
                'description' => 'Variables'
            ]
        ];
    }
}
