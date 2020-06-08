<?php

namespace App\GraphQL\Query;

use App\Services\UserService;
use App\Validators\Forms\UsersValidator;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Closure;
use Illuminate\Support\Facades\DB;

class UsersQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'UsersQuery',
        'description' => 'Get users with or without a tag, depending on access rights'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('users.view');
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
        return GraphQL::paginate('User');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'tags' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'Users by tags'
            ],
            'groups' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'Users by groups'
            ],
            'group_tag_id' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'Users by groups'
            ],
            'search' => [
                'type' => Type::string(),
                'description' => 'Search user by First/Last Name or Email'
            ],
            'subjects' => [
                'type' => Type::listOf(Type::int()),
                'description' => 'Users by subjects'
            ],
            'page' => [
                'type' => Type::int()
            ],
            'per_page' => [
                'type' => Type::int()
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
            'tags' => 'available_tag',
            'groups' => 'project_group_by_id',
            'page' => 'numeric|min:0',
            'per_page' => 'numeric|min:1|max:'.self::MAX_PER_PAGE,
        ];
    }


    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @param Closure $getSelectFields
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function resolve($root, $args, $context, ResolveInfo $info, Closure $getSelectFields)
    {
        $validate = new UsersValidator($args);

        if (!$validate->fails())
        {
            $per_page = (!empty($args['per_page']) && $args['per_page'] <= static::MAX_PER_PAGE) ? $args['per_page'] :
                static::DEFAULT_PER_PAGE;

            /**
             * @var $fields \Rebing\GraphQL\Support\SelectFields
             */
            $fields = $getSelectFields();
            $with = $fields->getRelations();
            $page = !empty($args['page']) ? $args['page'] : 1;

            $filter = array_diff_key($args, array_flip(['page', 'per_page']));
            $filter['project_id'] = $this->user()->getCurrentProject('id');

//            DB::connection()->enableQueryLog();
            $data = (new UserService($this->user()))
                ->getList($this->site('id'), $filter, $page, $with, $per_page);
//            print_r(DB::getQueryLog());exit;
//            exit('main');
//print_r($data);exit;
            return $data;
        }
        return [];
    }
}
