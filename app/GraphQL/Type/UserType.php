<?php

namespace App\GraphQL\Type;

use App\Models\CustomVar;
use App\Models\OtsProject;
use App\Models\Site;
use App\Models\User;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use GraphQL;

class UserType extends BaseType
{
    /**
     * @var array
     */
    protected $attributes = [
        'model' => 'App\\Models\\User',
        'name' => 'User',
        'description' => 'A type'
    ];

    /**
     * @return array
     */
    public function fields(): array
    {
        return $this->accessFilter([
            'id' => [
                'type' => Type::int()
            ],
            'email' => [
                'type' => Type::string(),
            ],
            'first_name' => [
                'type' => Type::string(),
            ],
            'last_name' => [
                'type' => Type::string(),
            ],
            'confirmation_code' => [
                'type' => Type::string(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
            'tags' => [
                'type' => Type::listOf(new ObjectType([
                    'name' => 'UserTags' . rand(1,10000),
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
                'query' => function($args, $query) {
                    $query->select('custom_var_relates.model_id', 'custom_var_relates.var_id', 'cvcode.code as tag', 'cvv.value as name')
                        ->join('custom_var_values as cvv', function($query){
                            $query->where('cvv.var_id', '=', DB::raw('custom_var_relates.var_id'))
                            ->where('cvv.language_id', language('id'));
                        })
                        ->leftJoin('custom_var_models as cvcm', 'cvcm.var_id', '=', DB::raw('custom_var_relates.var_id'))
                        ->where('custom_var_relates.owner_model_type', OtsProject::class)
                        ->where('custom_var_relates.owner_model_id', user()->getCurrentProject('id'));

                    $query = (new CustomVar())->queryByModels(
                        $query,
                        [
                            Site::class => user()->getSiteId(),
                            OtsProject::class => user()->getCurrentProject('id'),
                            User::class => user()->getId()
                        ],
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
            ],
            'groups' => [
                'type' => Type::listOf(new ObjectType([
                    'name' => 'UserGroups' . rand(1,10000),
                    'model' => 'App\\Models\\CustomVarRelate',
                    'fields' => [
                        'var_id' => [
                            'type' => Type::string(),
                        ],
                        'name' => [
                            'type' => Type::string(),
                        ],
                    ],
                    'standardSelect' => false
                ])),
                'query' => function($args, $query) {
                    $query->select('custom_var_relates.model_id', 'custom_var_relates.var_id', 'cvv.value as name')
                        ->join('custom_var_values as cvv', function($query){
                            $query->where('cvv.var_id', '=', DB::raw('custom_var_relates.var_id'))
                            ->where('cvv.language_id', language('id'));
                        })
                        ->leftJoin('custom_var_models as cvcm', 'cvcm.var_id', '=', DB::raw('custom_var_relates.var_id'))
                        ->where('custom_var_relates.owner_model_type', OtsProject::class)
                        ->where('custom_var_relates.owner_model_id', user()->getCurrentProject('id'));

                    $query = (new CustomVar())->queryByModels(
                        $query,
                        [
                            OtsProject::class => user()->getCurrentProject('id'),
                        ],
                        false,
                        'cvcm'
                    );

                    return $query;
                }
            ],
            'about' => [
                'type' => new ObjectType([
                    'name' => 'UserAbout' . rand(1,10000),
                    'fields' => [
                        'text' => [
                            'type' => Type::string(),
                            'selectable' => false //Поле не существет, генерится в резолвере
                        ],
                        'short_text' => [
                            'type' => Type::string(),
                            'selectable' => false //Поле не существет, генерится в резолвере
                        ],
                    ],
                    'standardSelect' => false
                ])
            ],
            'avatar' => [
                'type' => \GraphQL::type('Media'),
                /**
                 * selected fields need for working media lib
                 */
                'always' => ['file_name', 'name', 'collection_name', 'model_type', 'disk'],
            ],
            'teaching' => [
                'type' => Type::listOf(new ObjectType([
                    'model' => 'App\Models\OtsUserSubject',
                    'name' => 'UserTeach' . rand(1,10000),
                    'fields' => [
                        'count_requested_subjects' => [
                            'type' => Type::int()
                        ],
                        'tag_id' => [
                            'type' => Type::int()
                        ],
                        'tag' => [
                            'type' => Type::string()
                        ],
                        'count_approved_subjects' => [
                            'type' => Type::int(),
                        ],
                        'subjects' => [
                            'type' => Type::listOf(GraphQL::type('OtsUserSubjects')),
                            'args'          => [
                                'approved' => [
                                    'type' => Type::boolean(),
                                ],
                            ]
                        ],
                    ],
                    'standardSelect' => false
                ])),
                'query' => function($args, $query) {
                    $query->join('custom_var_codes as cvcode', 'cvcode.var_id', '=', 'ots_user_subjects.tag_id')
                        ->select('ots_user_subjects.*', 'cvcode.code as tag');
                    if(!empty($args['tags'])) {
                        $query->where('cvcode.code', $args['tags']);
                    }
                    return $query;
                },
                'args'          => [
                    'tags' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'Users by tags'
                    ],
                ],
            ]
        ]);
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     * @return array
     */
    public function resolveAboutField($root, $args, $context, ResolveInfo $info) {
        $result = [];
        $about = $root->about;
        if($about) {
            foreach($about as $item) {
                $key = str_replace('about.', '', $item->code);
                $result[$key] = $item->value;
            }
        }
        return $result;
    }
}
