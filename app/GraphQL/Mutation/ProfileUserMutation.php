<?php

namespace App\GraphQL\Mutation;

use App\Forms\User\ProfileForm;
use App\Forms\User\UpdateForm;
use App\GraphQL\Traits\BaseTrait;
use App\GraphQL\Error\ValidationError;
use App\GraphQL\TypeDefination\UploadType;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Validation\ValidationException;

class ProfileUserMutation extends Mutation
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'ProfileUserMutation',
        'description' => 'Editing user data'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool|void
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('user.profile');;
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
        return GraphQL::type('User'); //If error put: UserType
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
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
            'avatar' =>  [
                'name' => 'avatar',
                'type' => GraphQL::type('Upload'),
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
            return (new ProfileForm)->update($this->user()->id, $args);
        } catch (ValidationException $e) {
            throw new ValidationError($e->errors(), $e->validator);
        }
    }
}