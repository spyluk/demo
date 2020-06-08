<?php

namespace App\GraphQL\Type;

use App\Components\Database\Pagination\LengthAwarePaginator;
use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class UsersType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'Users',
        'description' => 'A public users type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'items' => [
                'type' => Type::listOf(\GraphQL::type('User')),
//                'resolveFn' => function ($root, $args, $context, ResolveInfo $info) {
////                    /**
////                     * @var LengthAwarePaginator $root
////                     */
////            print_r($info->getFieldSelection());
//                $root->load('tutor');
////            exit;
//                    return $root->items();
//                }
            ],
            'tags' => [
                'type' => Type::listOf(Type::string())
            ]
        ];
    }

}