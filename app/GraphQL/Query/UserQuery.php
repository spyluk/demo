<?php

namespace App\GraphQL\Query;

use App\Models\User;
use App\Services\UserService;
use App\Services\VariableService;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Illuminate\Support\Facades\DB;
use Closure;

class UserQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'UserQuery',
        'description' => 'Current user'
    ];

    /**
     * @param $root
     * @param $args
     * @param $context
     * @return bool
     */
    public function authenticated($root, $args, $context)
    {
        return self::user()->hasPermissionTo('user.view');
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
        return GraphQL::type('User');
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
            'user_id' => [
                'type' => Type::int(),
                'description' => 'Get user by id'
            ]
        ];
    }

    /**
     * @TODO перенести в тз
     *      просмотр данных другого пользователя, доступен только при наличии management.users или имея доступ к тегу,
     *      который привязан к пользователю
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @param Closure $getSelectFields
     * @return \App\Models\User|null
     * @throws \Exception
     */
    public function resolve($root, $args, $context, ResolveInfo $info, Closure $getSelectFields)
    {
        $tags = !empty($args['tags']) ? (is_array($args['tags']) ? $args['tags'] : [$args['tags']]) : [];

        $tags = $tags ? VariableService::getFilteredTags($tags, $this->user()->getSiteId(), UserService::DEFAULT_LIST_USER_TAGS, $this->user()) :
            null;

        if(!$tags && isset($args['user_id']) && !$this->user()->hasPermissionTo('management.users')) {
            throw new \Exception('Operation not available');
        }

        /**
         * @var $fields \Rebing\GraphQL\Support\SelectFields
         */
//        DB::connection()->enableQueryLog();
        $fields = $getSelectFields();
        $with = $fields->getRelations();//exit;

//        DB::connection()->enableQueryLog();
        $user_id = !empty($args['user_id']) ? $args['user_id'] : $this->user()->getId();
        $user = (new User())->getInitDbByFields(['id' => $user_id])
            ->with($with)
            ->first();
//        DB::connection()->enableQueryLog();
//        print_r(DB::getQueryLog());exit;
//        print_r($user->tutor->about);
//        print_r(DB::getQueryLog());
//        exit;
        return $user;
    }
}
