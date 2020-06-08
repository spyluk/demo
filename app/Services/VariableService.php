<?php
/**
 * 
 * User: sergei
 * Date: 06.10.18
 * Time: 0:06
 */

namespace App\Services;

use App\Components\Database\StructuringResult;
use App\Models\AclPermission;
use App\Models\AclRole;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\CustomVar;
use App\Models\CustomVarAttr;
use App\Models\CustomVarCode;
use App\Models\CustomVarModel;
use App\Models\CustomVarRelate;
use App\Models\CustomVarValue;
use App\Models\Language;
use App\Models\OtsProject;
use App\Models\OtsTutor;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VariableService
{
    /**
     * @param $codes
     * @param int $site_id
     * @param null $user
     * @param array $models
     * @param int $language_id
     * @return array
     */
    public static function getVars($codes, int $site_id, $user = null, array $models = [], int $language_id = Language::ENGLISH)
    {
        $ret = [];
        if($codes) {
            $vars = (new CustomVar)->getVarByCategoryCode($codes, $site_id, $models, $language_id)->toArray();

            $ret = StructuringResult::apply($vars, [
                '$code' => function ($result, $skey, $data, $sitem) use ($user) {
                    if ($data['type_id'] == CustomVar::TYPE_LIST) {
                        $result = !$result ? [] : $result;
                        if ($data['type_id'] == CustomVar::TYPE_LIST &&
                            (is_null($user) || ($user->getId() == $data['model_id']) || ($user && $user->hasPermissionTo('var.' . $data['id'])))) {
                            $attr = $data['attr'] ? (array)json_decode($data['attr'], true) : '';
                            $result[] = ['value' => $data['value'], 'id' => $data['id'], 'attr' => $attr, 'parent_id' => $data['parent_id'], 'vcode' => $data['vcode']];
                        }
                    }

                    return $result;
                }
            ]);
        }

        return $ret;
    }

    /**
     * @param $tags
     * @param $site_id
     * @param $tag_list
     * @param null $user
     * @param array $models
     * @return array
     */
    public static function getFilteredTags($tags, $site_id, $tag_list, $user = null, $models = [])
    {
        $result = [];

        $available_tags = self::getVars($tag_list, $site_id, $user, $models, language('id'));
        if($available_tags && !empty($available_tags[$tag_list])) {
            $result = StructuringResult::apply($available_tags[$tag_list], [
                '$id' => 'vcode'
            ]);
        }

        return array_flip(array_intersect_key(
            array_flip($result),
            array_flip($tags)));
    }

    /**
     * @param $ids
     * @param $site_id
     * @param $code
     * @param null $user
     * @param array $models
     * @return array
     */
    public static function getFilteredList($ids, $site_id, $code, $user = null, $models = [])
    {
        $result = [];

        $available_tags = self::getVars($code, $site_id, $user, $models, language('id'));
        if($available_tags && !empty($available_tags[$code])) {
            $result = collect($available_tags[$code])->mapWithKeys(function ($item) use ($ids) {
                return in_array($item['id'], $ids) ? [$item['id'] => $item['id']] : [];
            })->all();
        }

        return $result;
    }

    /**
     * @param array $tags_by_lists
     * @param $site_id
     * @param null $user
     * @param array $models
     * @return array
     */
    public static function getFilteredTagsLists($tags_by_lists, $site_id, $user = null, $models = [])
    {
        $result = [];
        $lists = array_keys($tags_by_lists);
        $available_tags = self::getVars($lists, $site_id, $user, $models);
        foreach($lists as $list) {
            if($available_tags && !empty($available_tags[$list])) {
                $result[$list] = StructuringResult::apply($available_tags[$list], [
                    '$id' => 'vcode'
                ]);
            }
        }

        foreach($result as $key => $item) {
            $result[$key] = collect($result[$key])->intersect($tags_by_lists[$key]);
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getCustomModelTagsByCategory($category)
    {
        return DB::select(
            "SELECT * FROM `category_codes` cc ".
            "left join `custom_var_codes` cvc on (cvc.category_id=cc.category_id) ".
            "left join `custom_var_models` cvm on (cvm.var_id=cvc.var_id) ".
            "where cc.code = ?", [$category]);
    }
    /**
     * Add a value to the model by category (one to one, one category_code one value)
     *
     * @param string $category_code
     * @param string $model_type
     * @param int $model_id
     * @param int $site_id
     * @param int $language_id
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    public static function setModelVar(
        string $category_code,
        string $model_type,
        int $model_id,
        int $site_id,
        int $language_id,
        string $value,
        string $owner_model_type = null,
        int $owner_model_id = null)
    {
        $owner_model_type = $owner_model_type ? $owner_model_type : Site::class;
        $owner_model_id = $owner_model_id ? $owner_model_id : $site_id;
        $category = (new Category)->getCategoryByCode($site_id, $category_code);

        if(!$category) {
            throw new \Exception('Category by code "' .$category_code. '" not found.');
        }

        try {
            $customVarRelate = (new CustomVarRelate)
                ->getByModelAndCategory($owner_model_type, $owner_model_id, $model_type, $model_id, $category->id);

            $customVarData = [
                'site_id' => $site_id, 'type_id' => CustomVar::TYPE_VAR, 'format_id' => CustomVar::FORMAT_STRING, 'category_id' => $category->id
            ];

            if(!$customVarRelate) {
                $customVar = (new CustomVar)->create($customVarData);
                $customVarId = $customVar->id;
            } else {
                $customVarId = $customVarRelate->var_id;
            }

            $customVarRelateData = [
                    'owner_model_type' => $owner_model_type,
                    'owner_model_id' => $owner_model_id,
                    'model_type' => $model_type,
                    'model_id' => $model_id,
                    'var_id' => $customVarId];
            if(!$customVarRelate)
            {
                (new CustomVarRelate())->create($customVarRelateData);
            }

            if($customVarId) {
                $customVarValue = (new CustomVarValue())->updateOrCreate(['language_id' => $language_id, 'var_id' => $customVarId], ['value' => $value]);
            }
            DB::commit();
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollback();
        }


        if($customVar ?? $customVarValue ?? false) {
                return true;
        }

        return false;
    }

    /**
     * @param string $category_code
     * @param string $model_type
     * @param int $model_id
     * @param int $site_id
     * @param int $language_id
     * @param string $value
     * @param string|null $attr
     * @param int|null $parent_id
     * @param string|null $code
     * @param int $type
     * @param int $format
     * @param array $roles
     * @return bool
     * @throws \Exception
     */
    public function add(
        string $category_code,
        string $model_type,
        int $model_id,
        int $site_id,
        int $language_id,
        string $value,
        string $attr = null,
        int $parent_id = null,
        string $code = null,
        int $type = CustomVar::TYPE_VAR,
        int $format = CustomVar::FORMAT_JSON,
        $roles = []
        )
    {
        $category = (new Category)->getCategoryByCode($site_id, $category_code);

        if(!$category) {
            throw new \Exception('Category by code "' .$category_code. '" not found.');
        }

        if(is_null($value) && is_null($attr)) {
            throw new \Exception('Required parameters not set.');
        }

        try {
            DB::beginTransaction();

            $count = (new CustomVar())->getInitDbByFields([
                'site_id' => $site_id,
                'parent_id' => $parent_id,
                'category_id' => $category->id
            ])->count();

            $var = (new CustomVar)->create([
                'site_id' => $site_id,
                'type_id' => $type,
                'format_id' => $format,
                'parent_id' => $parent_id,
                'category_id' => $category->id,
                'order' => $count + 1
            ]);

            (new CustomVarModel())->create([
                'var_id' => $var->id,
                'model_type' => $model_type,
                'model_id' => $model_id
            ]);

            if(!is_null($value)) {
                (new CustomVarValue())->create([
                    'var_id' => $var->id,
                    'language_id' => $language_id,
                    'value' => $value
                ]);
            }

            if(!is_null($code)) {
                (new CustomVarCode())->create([
                    'var_id' => $var->id,
                    'category_id' => $category->id,
                    'code' => $code
                ]);
            }

            if(!is_null($attr)) {
                (new CustomVarAttr())->create([
                    'var_id' => $var->id,
                    'value' => $attr
                ]);
            }

            foreach($roles as $role) {
                $this->assigneRole($role, $var->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }

        return true;
    }

    /**
     * @param string $var_id
     * @param int $language_id
     * @param string $value
     * @param string|null $attr
     * @param int|null $parent_id
     * @param string|null $code
     * @param int $type
     * @param int $format
     * @param array $roles
     * @return bool
     * @throws \Exception
     */
    public function change(
        string $var_id,
        int $language_id,
        string $value,
        string $attr = null,
        int $parent_id = null,
        string $code = null,
        int $type = CustomVar::TYPE_VAR,
        int $format = CustomVar::FORMAT_JSON,
        $roles = []
        )
    {
        $var = (new CustomVar)->getById($var_id);

        if(!$var) {
            throw new \Exception('Var not exists.');
        }

        try {
            DB::beginTransaction();

            $var = (new CustomVar)->create([
                'type_id' => $type,
                'format_id' => $format,
                'parent_id' => $parent_id,
            ]);

            (new CustomVarModel())->create([
                'var_id' => $var->id,
                'model_type' => $model_type,
                'model_id' => $model_id
            ]);

            if(!is_null($value)) {
                (new CustomVarValue())->create([
                    'var_id' => $var->id,
                    'language_id' => $language_id,
                    'value' => $value
                ]);
            }

            if(!is_null($code)) {
                (new CustomVarCode())->create([
                    'var_id' => $var->id,
                    'category_id' => $category->id,
                    'code' => $code
                ]);
            }

            if(!is_null($attr)) {
                (new CustomVarAttr())->create([
                    'var_id' => $var->id,
                    'value' => $attr
                ]);
            }

            foreach($roles as $role) {
                $this->assigneRole($role, $var->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }

        return true;
    }

    /**
     * @param int $var_id
     * @param int $language_id
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    public function changeValue(int $var_id, int $language_id, string $value)
    {
        $var = (new CustomVarValue())->getItemByFields([
            'var_id' => $var_id,
            'language_id' => $language_id
        ]);

        if(!$var) {
            throw new \Exception('Var value not exists.');
        }

        try {
            $var->value = $value;
            $var->save();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int $var_id
     * @param int $parent_id
     * @param int $order
     * @return bool
     * @throws \Exception
     */
    public function changeParent(int $var_id, int $parent_id, int $order)
    {
        if(!($var = (new CustomVar())->getById($var_id))) {
            throw new \Exception('Var value not exists.');
        }

        try {
            if($this->resort($var->category_id, $parent_id, null, $order)) {
                $this->resort($var->category_id, $var->parent_id, $var_id);
                $var->parent_id = $parent_id;
                $var->order = $order;
                $var->save();
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int $var_id
     * @param int $order
     * @return bool
     * @throws \Exception
     */
    public function changeOrder(int $var_id, int $order)
    {
        if(!($var = (new CustomVar())->getById($var_id))) {
            throw new \Exception('Var value not exists.');
        }

        $list = (new CustomVar())->getInitDbByFields([
            'category_id' => $var->category_id,
            'parent_id' => $var->parent_id])->orderBy('order', 'ASC')->get();

        $order = (count($list) > $order) ? $order : count($list);

        try {
            $this->resort($var->category_id, $var->parent_id, $var_id, $order);
            $var->order = $order;
            $var->save();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int $category_id
     * @param $parent_id
     * @param null $except_var_id
     * @param null $except_order
     * @return bool
     */
    protected function resort(int $category_id, $parent_id, $except_var_id = null, $except_order = null)
    {
        $list = (new CustomVar())->getInitDbByFields([
            'category_id' => $category_id,
            'parent_id' => $parent_id])->orderBy('order', 'ASC')->get();

        if($except_order && count($list) < $except_order-1) {
            return false;
        }

        if($list) {
            $iter = 1;
            foreach($list as $item) {
                $iter = (is_null($except_order) || $except_order != $iter) ? $iter : $iter + 1;
                if($item->id != $except_var_id) {
                    $item->order = $iter;
                    $item->save();
                    $iter ++;
                }
            }
        }

        return true;
    }

    /**
     * @param $role
     * @param $var_id
     */
    protected function assigneRole($role, $var_id)
    {
        $role = AclRole::query()->where('name', $role)->first();
        $permission = AclPermission::findOrCreateVar($var_id);

        $permission->assignRole($role);
    }

    /**
     * @param int $var_id
     * @return bool
     */
    public function remove(int $var_id)
    {
        try {
            DB::beginTransaction();

            if($children  = (new CustomVar)->getItemsByFields(['parent_id' => $var_id])) {
                foreach($children as $child) {
                    $this->remove($child->id);
                }
            }

            $var = (new CustomVar)->getById($var_id);

            if($var) {
                $models = [CustomVarRelate::class, CustomVarModel::class, CustomVarAttr::class, CustomVarValue::class, CustomVarCode::class];
                foreach($models as $class_model) {
                    $class_model::where('var_id', $var_id)->delete();
                }
            }

            $var->delete();

            DB::commit();
        } catch (\Exception $e) {
            print_r($e->getMessage());exit;
            DB::rollback();
            return false;
        }

        return true;
    }
}