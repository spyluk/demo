<?php
/**
 * 
 * User: sergei
 * Date: 18.06.19
 */

namespace App\Vsm;

use App\Models\Site;
use App\Models\VsmModule as VsmModuleModel;
use App\Components\Database\StructuringResult;
use App\GraphQL\Query\BaseQuery;
use App\Vsm\Models\View as ViewModel;
use Illuminate\Support\Arr;

/**
 * Class VsManager
 * @package App\Components\Module
 */
class VsmManager
{
    /**
     * @var array|null
     */
    protected static $modules = null;
    /**
     * @var array|null
     */
    protected static $relations = null;
    /**
     * @var array|null
     */
    protected static $code_modules = null;
    /**
     * @var array
     */
    protected static $load_modules = [];
    /**
     * @var mixed|null
     */
    protected static $result = [];
    /**
     * @var string
     */
    protected static $key_separator = "|";

    /**
     * @param $inputs
     * @param null $graphql_schema
     * @return mixed
     */
    public static function query($inputs, $graphql_schema = null)
    {
        $isBatch = empty($inputs['query']);

        if (is_null($graphql_schema)) {
            $graphql_schema = config('graphql.schema');
        }

        if (!$isBatch) {
            $data = self::executeQuery($graphql_schema, $inputs);
        } else {
            $data = [];
            foreach ($inputs as $input) {
                $data[] = self::executeQuery($graphql_schema, $input);
            }
        }

        $errors = !$isBatch ? Arr::get($data, 'errors', []) : [];
        $authorized = array_reduce($errors, function ($authorized, $error) {
            return !$authorized || Arr::get($error, 'message') === 'Unauthorized' ? false : true;
        }, true);

        if ($authorized) {
            return $data;
        }

        return null;
    }

    /**
     * @param $schema
     * @param $input
     * @return mixed
     */
    protected static function executeQuery($schema, $input)
    {
        $variablesInputName = config('graphql.variables_input_name', 'variables');
        $query = Arr::get($input, 'query');
        $variables = Arr::get($input, $variablesInputName);
        if (is_string($variables)) {
            $variables = json_decode($variables, true);
        }

        $operationName = Arr::get($input, 'operationName');

        return app('graphql')->query($query, $variables, [
            'context' => BaseQuery::user(),
            'schema' => $schema,
            'operationName' => $operationName
        ]);
    }

    /**
     * @param $key_or_data
     * @param null $value
     */
    public static function setResult($key_or_data, $value = null)
    {
        $set = function($keys, $data, $value) use (&$set) {
            if(!is_array($keys)) {
                $keys = explode(self::$key_separator, $keys);
            }

            if(($key = array_shift($keys))) {
                if($keys) {
                    $data[$key] = isset($data[$key]) ? $data[$key] : [];
                    $data[$key] = $set($keys, $data[$key], $value);
                } else {
                    if(!empty($data[$key]) && is_array($data[$key]) && is_array($value)) {
                        if(array_keys($data[$key]) !== range(0, count($data[$key]) - 1)) {
                            $data[$key] += $value;
                        } else {
                            $data[$key] = array_merge($data[$key], $value);
                        }
                    } else {
                        $data[$key] = $value;
                    }
                }
            }

            return $data;
        };

        if(is_null($value) && $key_or_data && is_array($key_or_data)) {
            self::$result = $key_or_data;
        } else {
            self::$result = $set($key_or_data, self::$result, $value);
        }
    }

    /**
     * @param null $key
     * @return mixed|null
     */
    public static function getResult($key = null)
    {
        $get = function($keys, $data) use (&$get) {
            $return = null;
            if(!is_array($keys)) {
                $keys = explode(self::$key_separator, $keys);
            }

            if(($key = array_shift($keys)) && isset($data[$key])) {
                if($keys) {
                    $return = $get($keys, $data[$key]);
                } else {
                    $return = $data[$key];
                }
            }

            return $return;
        };

        return !$key ? self::$result : $get($key, self::$result);
    }

    /**
     * @param $code
     * @return int|null
     */
    public static function getModuleIdByCode($code)
    {
        return isset(self::$code_modules[$code]) ? self::$code_modules[$code] : null;
    }

    /**
     * @param $code
     * @param null $site_id
     * @return ViewModel|null
     */
    public static function getLoadModuleByСode($code, $site_id = null)
    {
        $site_id = $site_id ? $site_id : site('id');
        self::initModules($site_id);

        if(($module_id = self::getModuleIdByCode($code))) {
            if(!isset(self::$load_modules[$module_id])) {
                self::initModulesByCodes([$code], $site_id);
            }
        }

        return isset(self::$load_modules[$module_id]) ? self::$load_modules[$module_id] : null;
    }

    /**
     * @param $site_id
     */
    public static function initModules($site_id)
    {
        if(is_null(self::$modules)) {
            self::$modules = [];
            $moduleModel = new VsmModuleModel;
            if($res = $moduleModel->getModuleRelationIds($site_id)) {
                foreach($res as $item) {
                    if($item['relate_id']) {
                        self::$relations[$item['module_id']][] = $item['relate_id'];
                    }

                    self::$modules[$item['module_id']] = $item['module_id'];
                    self::$code_modules[$item['code']] = $item['module_id'];
                }
            }
        }
    }

    /**
     * @param $codes
     * @param $site_id
     */
    public static function initModulesByCodes($codes, $site_id)
    {
        self::initModules($site_id);
        $moduleModel = new VsmModuleModel;
        $relations = self::$relations;
        $getRelations = function(&$result, $id) use (&$getRelations, $relations) {
            if(empty($result[$id])) {
                $result[$id] = $id;
                if (!empty($relations[$id])) {
                    foreach ($relations[$id] as $module_id) {
                        $result[$module_id] = $module_id;
                        $getRelations($result, $module_id);
                    }
                }
            }
        };

        $load_modules = [];
        foreach($codes as $code) {
            if(!empty(self::$code_modules[$code])) {
                $getRelations($load_modules, self::$code_modules[$code]);
            }
        }

        if($load_modules) {
            self::$load_modules = StructuringResult::apply(
                $moduleModel->getModulesWithData($load_modules, Site::class, $site_id),
                ['$module_id' => function($result, $skey, $data, $sitem) {//'*']
                    return new ViewModel($data);
                }]
            );
        }
    }
}