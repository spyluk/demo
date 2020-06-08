<?php

namespace App\GraphQL\Query\Ots\Messages;

use App\GraphQL\Query\BaseQuery;
use App\Models\OtsMessageUserStatistic;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class MessagesStatisticQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessagesStatisticQuery',
        'description' => 'A messages statistic query'
    ];

    /**
     *
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('message.statistic.view');
    }

    /**
     *
     * @param $root
     * @param $args
     * @return bool
     */
    public function authorize(array $args): bool
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function type(): Type
    {
        return Type::listOf(GraphQL::type('OtsMessagesStatistic'));
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'main' => [
                'type' => Type::boolean()
            ]
        ];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $main = $args['main'] ?? false;
        $messagesUserStatistic = (new OtsMessageUserStatistic)
            ->getTagsByUserAndSite($this->user()->id, $this->site()->id, $main);

        return $messagesUserStatistic;
    }
}