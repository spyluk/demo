<?php

namespace App\GraphQL\Query\User;

use App\Forms\LoginForm;
use App\GraphQL\Error\ValidationError;
use App\GraphQL\Query\BaseQuery;
use App\Validators\DefaultValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\ValidationException;

class LoginQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'UserLoginQuery',
        'description' => 'A user login query'
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
        return GraphQL::type('UserAuth');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'email' => [
                'type' => Type::string()
            ],
            'password' => [
                'type' => Type::string()
            ]
        ];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return \App\Models\User
     * @throws ValidationError
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        try
        {
            $user = (new LoginForm())->login($args);

            if(!empty($info->getFieldSelection()['token']))
            {
               $user->token = $user->createToken('api')->accessToken;
            }

            return $user;

        } catch (ValidationException $e) {
            throw new ValidationError($e->errors(), $e->validator);
        } catch (\Exception $e) {
            throw new ValidationError(['credential' => [$e->getMessage()]], (new DefaultValidator([],['credential' => 'string'])));
        }
    }
}