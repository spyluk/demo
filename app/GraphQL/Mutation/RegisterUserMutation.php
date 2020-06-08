<?php

namespace App\GraphQL\Mutation;

use App\Forms\RegistrationForm;
use App\GraphQL\Traits\BaseTrait;
use App\GraphQL\Error\ValidationError;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\ValidationException;

class RegisterUserMutation extends Mutation
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'CreateUserMutation',
        'description' => 'A mutation to create User'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return true;
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
        return GraphQL::type('UserAuth'); //If error put: UserType
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'email' => [
                'type' => Type::string(),
            ],
            'password' => [
                'type' => Type::string(),
            ],
            'password_confirmation' => [
                'type' => Type::string(),
            ],
            'first_name' => [
                'type' => Type::string(),
            ],
            'last_name' => [
                'type' => Type::string(),
            ],
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
        try
        {
            $user = (new RegistrationForm)->register(
                $args,
                (int)$this->site('id'),
                $this->ip(),
                $this->language('id')
            );

            if(!empty($info->getFieldSelection()['token']))
            {
                $user->token = $user->createToken('api')->accessToken;
            }

            return $user;

        } catch (ValidationException $e) {
            throw new ValidationError($e->errors(), $e->validator);
        }
    }
}