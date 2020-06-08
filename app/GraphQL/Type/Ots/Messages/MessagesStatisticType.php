<?php

namespace App\GraphQL\Type\Ots\Messages;

use App\GraphQL\Type\BaseType;
use GraphQL\Type\Definition\Type;

class MessagesStatisticType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessagesStatistic',
        'description' => 'A message statistic type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'count' => [
                'type' => Type::int()
            ],
            'count_read' => [
                'type' => Type::int(),
            ],
            'tag' => [
                'type' => Type::string(),
            ]
        ];
    }
}