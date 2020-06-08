<?php

namespace App\GraphQL\Query\Ots\Messages;

use App\GraphQL\Query\BaseQuery;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use App\Validators\Forms\Message\ItemsValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class MessagesQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessagesQuery',
        'description' => 'A messages query'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('messages.view');
    }

    /**
     * @param array $args
     * @return bool
     */
    public function authorize(array $args): bool
    {
        return true;
    }


    /**
     * @return mixed
     */
    public function type(): Type    {
        return GraphQL::type('OtsMessages');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'tags' => [
                'type' => Type::listOf(Type::string())
            ],
            'page' => [
                'type' => Type::int()
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
        $validate = new ItemsValidator($args);

        if (!$validate->fails())
        {
            $page = !empty($args['page']) ? $args['page'] : 1;
            $tags = !empty($args['tags']) ? (is_array($args['tags']) ? $args['tags'] : [$args['tags']]) : [];
            $data = (new OtsMessageService($this->user()))
                ->getList($tags, $page);
            $data['tags'] = $tags;
            return $data;
        }

        return [];
    }
}