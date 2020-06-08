<?php

namespace App\GraphQL\Query\Ots\Messages;

use App\GraphQL\Error\ValidationError;
use App\GraphQL\Query\BaseQuery;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use App\Validators\Forms\Message\ItemValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class MessageQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'OtsMessageQuery',
        'description' => 'A message thread query'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('message.view');
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
    public function type(): Type
    {
        return GraphQL::type('OtsMessages');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int()
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
     * @throws ValidationError
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $validator = new ItemValidator($args);

        if (!$validator->fails())
        {
            $page = !empty($args['page']) ? $args['page'] : 1;
            $id = $args['id'];
            $data = (new OtsMessageService($this->user()))
                ->getMessageThread($id, $page);

            if(!empty($data['items'])) {
                return $data;
            } else {
                $validator->setErrors(['Message not found']);
            }
        }

        $validationError = new ValidationError('Validation error');
        $validationError->setValidator($validator);
        throw $validationError;
    }
}