<?php

namespace App\GraphQL\Query;

use App\GraphQL\TypeDefination\ArrayType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class VsmQuery extends BaseQuery
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'VsmQuery',
        'description' => 'A query visual system'
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
     * @return \GraphQL\Type\Definition\ListOfType
     */
    public function type(): Type
    {
        return \GraphQL::type('Vsm');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'codes' => [
                'type' => new ArrayType('params'),
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
        $site_id = $this->site('id');
        $request = request()->all();
        unset($request['query']);

        try
        {
            vsmrun('module', [
                'data' => $args +
                    ['site_id' => $site_id] +
                    ['request' => $request]
            ]);
        } catch (\Exception $e) {
//                if(!$e instanceof AuthViewException) {
//                    throw new \Exception($e->getMessage());
//                }
        }

        return ['structure' => vsmget()];
    }
}
