<?php

namespace App\GraphQL\Type;

use App\GraphQL\TypeDefination\Type;

class VsmType extends BaseType
{
    protected $attributes = [
        'name' => 'Vsm',
        'description' => 'A visual system type'
    ];

    public function fields(): array
    {
        return [
            'structure' => [
                'type' => Type::arr(),
                'description' => 'Visual system structure'
            ]
        ];
    }
}
