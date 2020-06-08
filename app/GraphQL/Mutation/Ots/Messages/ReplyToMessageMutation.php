<?php

namespace App\GraphQL\Mutation\Ots\Messages;

use App\Forms\Message\ReplyForm;
use App\GraphQL\Traits\BaseTrait;
use App\GraphQL\Error\ValidationError;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\ValidationException;

class ReplyToMessageMutation extends Mutation
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
        return self::user()->hasPermissionTo('message.reply');
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
        return GraphQL::type('OtsMessage'); //If error put: MessageType
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'main_id' => [
                'type' => Type::int(),
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
            $message = (new ReplyForm())->send($this->user(), $args);
            return (new OtsMessageService($this->user()))
                ->getMessage($message->id);
        } catch (ValidationException $e) {
            throw new ValidationError($e->errors(), $e->validator);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}