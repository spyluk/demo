<?php

namespace App\GraphQL\Query;

use App\Components\Database\StructuringResult;
use App\Models\CustomVar;
use App\Models\OtsProject;
use App\Models\Site;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\TypeDefination\Type;
use GraphQL\Type\Definition\Type as BaseType;
use App\GraphQL\TypeDefination\ArrayType;
use Illuminate\Support\Facades\DB;

class VarsQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'VarsQuery',
        'description' => 'A query vars'
    ];

    public function authenticated($root, $args, $context)
    {
        return true;
    }

    public function authorize(array $args): bool
    {
        return true;
    }

    /**
     * @return Type
     */
    public function type(): BaseType
    {
        return \GraphQL::type('Variable');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'codes' => [
                'type' => new ArrayType('vars'),
                'description' => 'Run module bu codes, if "code" and "codes" are specified, "code" will be executed and all "codes" will be passed to it as parameters'
            ],
        ];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return array
     */
    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $ret = [];
        if(!empty($args['codes']))
        {
            $language_id = $this->language('id');
            $user = $this->user();
            $models = [
                Site::class => $this->site('id'),
                OtsProject::class => $user->getCurrentProject('id'),
                User::class => $user->id
            ];

            $vars = (new CustomVar())
                ->getVarByCategoryCode($args['codes'], $this->site('id'), $models, $language_id)->toArray();

            $ret = StructuringResult::apply($vars, [
                '$code' => function ($result, $skey, $data, $sitem) use ($user)
                {
                    if ($data['type_id'] == CustomVar::TYPE_LIST)
                    {
                        $result = !$result ? [] : $result;
                        if ($data['type_id'] == CustomVar::TYPE_LIST && (is_null($user) || ($user && $user->hasPermissionTo('var.' . $data['id']))))
                        {
                            $attr = $data['attr'] ? (array) json_decode($data['attr'], true) : '';
                            $result[] = [
                                'value' => $data['value'],
                                'id' => $data['id'],
                                'attr' => $attr,
                                'parent_id' => $data['parent_id'],
                                'vcode' => $data['vcode']
                            ];
                        }
                    }

                    return $result;
                }
            ]);
        }

        return ['variables' => $ret];
    }
}
