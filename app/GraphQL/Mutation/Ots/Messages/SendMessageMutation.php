<?php

namespace App\GraphQL\Mutation\Ots\Messages;

use App\Forms\Message\SendForm;
use App\GraphQL\Traits\BaseTrait;
use App\GraphQL\Error\ValidationError;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\ValidationException;

class SendMessageMutation extends Mutation
{
    use BaseTrait;
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'SendMessageMutation',
        'description' => 'A mutation to send message'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('message.send');
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
        return GraphQL::type('OtsSimpleMessage'); //If error put: MessageType
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'user_id' => [
                'type' => Type::int(),
            ],
            'subject' => [
                'type' => Type::string(),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'tags' => [
                'type' => Type::listOf(Type::string())
            ]
        ];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return \App\Models\OtsMessage|null
     * @throws ValidationError
     * @throws \Exception
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        try
        {
            return (new SendForm)->send($this->user(), $this->site('id'), $args);
        } catch (ValidationException $e) {
            throw new ValidationError($e->errors(), $e->validator);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}