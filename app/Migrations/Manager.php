<?php

namespace App\Migrations;

use App\Models\CustomVar;
use App\Models\CustomVarAttr;
use App\Models\CustomVarCode;
use App\Models\CustomVarModel;
use App\Models\CustomVarRelate;
use App\Models\CustomVarValue;
use Illuminate\Support\Facades\DB;
use App\Models\Site;
use App\Models\CategoryType;
use App\Models\Category;
use App\Models\SiteLanguage as SiteLanguageModel;
use App\Models\AclRole;
use App\Models\AclPermission;

/**
 * 
 * User: sergei
 * Date: 19.09.18
 * Time: 11:45
 */
class Manager
{
    /**
     * @param $category
     * @param $type
     * @param null $code
     * @param bool $system
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|object|static
     * @throws \Exception
     */
    public static function addCategoryIfNotExists($category, $type, $code = null, $system = false)
    {
        print $category . "\r\n";
        $categories = explode("/", $category);

        $res = null;
        $parent_id = null;
        $path = null;
        $level = 0;

        foreach ($categories as $key => $category_item) {
            if (!$res = DB::table('categories')->where('name', $category_item)->where('type_id', $type)->where('parent_id', $parent_id)->first()) {
                $res = (new Category)->create([
                    'site_id' => Site::ONLINETUTORSERVICE,
                    'name' => $category_item,
                    'parent_id' => $parent_id,
                    'type_id' => $type,
                    'level' => $level,
                    'system' => $system]);
                $res = $res['id'];
            } else {
                $res = $res->id;
            }
            $level++;
            $parent_id = $res;
        }

        if (!$res) {
            throw new \Exception('Not create category: ' . print_r($category_item, 1));
        }

        if($code) {
            if (!DB::table('category_codes')->where('category_id', $res)->first()) {
                DB::table('category_codes')->insert([
                    ['category_id' => $res, 'code' => $code]
                ]);
            }
        }

        return $res;
    }

    /**
     * @param $path
     * @param $callback
     * @return array
     */
    public static function configHandler($path, $callback)
    {
        $return = [];
        if (is_dir($path) && $scanned_directory = array_diff(scandir($path), array('..', '.'))) {
            foreach ($scanned_directory as $file) {
                $filePath = $path . '/' . $file;
                if (!is_dir($filePath)) {
                    $return = array_merge($return, $callback($filePath, $file));
                }
            }
        }

        return $return;
    }

    /**
     * @param $model_id
     * @param $model_type
     * @param $config
     * @param $type
     * @param int $site_id
     */
    public static function addVariableByConfig($model_id, $model_type, $config, $type, $site_id = Site::ONLINETUTORSERVICE)
    {
        $addVar = function ($var_id, $site_id, $type, $format, $vars, $category_id = null, $parent_id = null, $root_id = null, $prent_models = []) use (&$addVar) {
            foreach ($vars as $var) {
                $models = !empty($var['models']) ? $var['models'] :
                    (!empty($prent_models) ? $prent_models : [['model' => Site::class, 'id' => $site_id, 'active' => 1]]);

                $newVar = (new CustomVar())->updateOrCreate(
                    ['id' => $var_id],
                    [
                        'type_id' => $type,
                        'format_id' => $format,
                        'site_id' => $site_id,
                        'parent_id' => $parent_id,
                        'category_id' => $category_id ? $category_id : null,
                        'order' => isset($var['order']) ? $var['order'] : null,
                    ]
                );
                $root_id = !$root_id ? $newVar->id : $root_id;
                print ' var.' . $newVar->id . '; ';

                if (isset($var['attr']) && is_array($var['attr'])) {
                    $res = (new CustomVarAttr)->updateOrCreate(
                        ['var_id' => $var_id],
                        [
                            'var_id' => $newVar->id,
                            'value' => json_encode($var['attr'])
                        ]
                    );

                    print ' attr: ' . $res->id . '; ';
                }

                if (isset($var['code'])) {
                    $res = (new CustomVarCode())->updateOrCreate(
                        ['var_id' => $var_id],
                        [
                            'var_id' => $newVar->id,
                            'code' => (string)$var['code'],
                            'category_id' => $category_id
                        ]
                    );
                    print ' code: ' . $res->id . '; ';

                    foreach($models as $model) {
                        $res = (new CustomVarModel())->updateOrCreate(
                            [
                                'var_id' => $newVar->id,
                                'model_id' => $model['id'],
                                'model_type' => $model['model']
                            ],
                            [
                                'var_id' => $newVar->id,
                                'model_id' => $model['id'],
                                'model_type' => $model['model'],
                                'active' => $model['active']
                            ]
                        );
                        print ' code model: ' . $res->id . '; ';
                    }
                }

                if (!empty($var['value'])) {
                    print ' value: ';
                    foreach ($var['value'] as $code_lang => $trans_var) {
                        $site_languages = (new SiteLanguageModel)->getSiteLanguagesByCode(Site::ONLINETUTORSERVICE, $code_lang);

                        if ($site_languages) {
                            $res = (new CustomVarValue)->updateOrCreate(
                                ['var_id' => $var_id, 'language_id' => $site_languages->id],
                                [
                                    'var_id' => $newVar->id,
                                    'language_id' => $site_languages->id,
                                    'value' => is_array($trans_var) ? json_encode($trans_var) : $trans_var,
                                ]
                            );
                            print $res->id . ', ';
                        }
                    }
                }

                if(isset($var['roles'])) {
                    foreach($var['roles'] as $role) {
                        $role = AclRole::query()->where('name', $role)->first();
                        $permission_name = 'var.'.$newVar->id;
                        $permission = AclPermission::findOrCreate($permission_name);

                        $permission->assignRole($role);
                    }

                    print ($var_id ? "update" : 'add') . ' var roles: ' . json_encode($var['roles']) . '; ';
                }

                if (isset($var['sub'])) {
                    print "\r\n";
                    $addVar($var_id, $site_id, $type, $format, $var['sub'], $category_id, $newVar->id, $root_id, $models);
                }
            }
            print "\r\n";
            return $root_id;
        };

        $var_id = $category_id = null;
        if((!empty($config['category']) && !empty($config['category_code'])) || !empty($config['category_id'])) {
            $var = DB::table('custom_vars as cv')
                ->leftJoin('category_codes as cc', 'cc.category_id', '=', 'cv.category_id')
                ->leftJoin('categories as c', 'cv.category_id', '=', 'c.id')
//                ->where('c.type_id', '=', CategoryType::VARS)
                ->select('cv.id as var_id', 'cv.category_id');

            if(!empty($config['category_id'])) {
                $var->where('cc.category_id', '=', $config['category_id']);
            } else {
                $var->where('cc.code', '=', $config['category_code']);
            }

            $var = $var->first();
            print ($var ? "update var. " . $var->var_id : 'add var' ) . '; ';
            if($var) {
                $var_id = $var->var_id;
            }

            $category_id = ($var && !empty($var->category_id)) ? $var->category_id : (
                !empty($config['category_id']) ? $config['category_id'] : null
            );

            if(!$category_id) {
                $category_id = self::addCategoryIfNotExists(
                    $config['category'], CategoryType::VARS, $config['category_code'], !empty($config['system'])
                );
                print "add category: " . $category_id . '; ';
            } else {
                print "exists category: " . $category_id . '; ';
            }

        }

        $var_id = $addVar($var_id, $site_id, $type, $config['format'], $config['vars'], $category_id);

        if($var_id && $model_id) {
            $res = (new CustomVarRelate)->updateOrCreate(
                ['var_id' => $var_id],
                [
                    'var_id' => $var_id,
                    'owner_model_id' => $site_id,
                    'owner_model_type' => Site::class,
                    'model_id' => $model_id,
                    'model_type' => $model_type
                ]
            );
            print ' var relations: ' . $res->id . '; ';
        }

        print "\r\n";
    }
}