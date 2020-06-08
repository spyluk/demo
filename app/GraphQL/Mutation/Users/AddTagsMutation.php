<?php

namespace App\GraphQL\Mutation\Users;

use App\GraphQL\Traits\BaseTrait;
use App\Models\OtsProject;
use App\Models\User;
use App\Services\Users\TagService;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AddTagsMutation extends Mutation
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'usersAddTagsMutation',
        'description' => 'A mutation to Users add tags'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('management.users.assign.tags');
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
        return Type::boolean();
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'tags' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'Tags to bind to user'
            ],
            'users' => [
                'type' => Type::listOf(Type::int()),
                'description' => 'Users who need to bind tags'
            ]
        ];
    }

    /**
     * @param array $args
     * @return array
     */
    protected function rules(array $args = []): array
    {
        return [
            'tags' => 'required',
            'users' => 'required'
        ];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return mixed
     * @throws \Exception
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $users = (new User)->getItemsByFields([
            'x.id' => $args['users'],
            'project_id' => $this->user()->getCurrentProject('id')
        ]);

        $userTagsService = (new TagService($this->user()));
        foreach($users as $user) {
            $user->setCurrentProject($this->user()->getCurrentProject());
            $userTagsService->add(
                $args['tags'],
                $user,
                OtsProject::class,
                $this->user()->getCurrentProject('id'),
                $this->user());
        }

        return true;
    }
}