<?php

namespace App\GraphQL\Type\Custom;

use App\Models\CustomVar;
use App\Models\OtsProject;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use Illuminate\Support\Facades\DB;

/**
 * User: Sergei
 * Date: 01.06.20
 */
class Tags
{
    public static function get($name, $owner_model_type, $owner_model_id, $models)
    {
        return [
            'type' => Type::listOf(new ObjectType([
                'name' => $name . rand(1,10000),
                'model' => 'App\\Models\\CustomVarRelate',
                'fields' => [
                    'var_id' => [
                        'type' => Type::string(),
                    ],
                    'tag' => [
                        'type' => Type::string(),
                    ],
                    'name' => [
                        'type' => Type::string(),
                    ],
                ],
                'standardSelect' => false
            ])),
            'query' => function($args, $query) use ($owner_model_type, $owner_model_id, $models) {
                $query->select('custom_var_relates.model_id', 'custom_var_relates.var_id', 'cvcode.code as tag', 'cvv.value as name')
                    ->join('custom_var_values as cvv', function($query){
                        $query->where('cvv.var_id', '=', DB::raw('custom_var_relates.var_id'))
                            ->where('cvv.language_id', language('id'));
                    })
                    ->leftJoin('custom_var_models as cvcm', 'cvcm.var_id', '=', DB::raw('custom_var_relates.var_id'))
                    ->where('custom_var_relates.owner_model_type', $owner_model_type)
                    ->where('custom_var_relates.owner_model_id', $owner_model_id);

                $query = (new CustomVar())->queryByModels(
                    $query,
                    $models,
                    false,
                    'cvcm'
                );
                $query->where('cvcm.active', true);

                return $query;
            },
            'resolve' => function($root, $t) {
                foreach($root['tags'] as $key => $tag) {
                    if(!user()->hasPermissionTo('var.' . $tag->var_id)) {
                        $root['tags']->forget($key);
                    }
                }

                return $root['tags'];
            }
        ];
    }
}