<?php

namespace App\Models\Filters\User;

use App\Components\Eloquent\Builder;
use App\Models\Contracts\Filter;
use App\Models\CustomVar;
use App\Models\Filters\BaseFilter;
use App\Models\Site;
use App\Models\User;
use App\Models\OtsProject;
use App\Services\Users\TagService;
use Illuminate\Support\Facades\DB;

/**
 * User: Sergei
 * Date: 10.02.20
 */
class TagsFilter extends BaseFilter implements Filter
{
    /**
     * To filter by project tags
     *
     * @param Builder $builder
     * @param mixed $value
     * @param null $data
     *
     * @return Builder
     */
    public static function apply(Builder $builder, $value, $data = null): Builder {
        /**
         * @TODO Переделать запрос с учетом индексов, прогнать explain, сейчас есть проблема type=ALL
         */
//        $user = $data['user'] ?? null;
//        $builder->join('custom_var_tags as fct', function($query) use ($user){
//            $query->where('fct.model_id', '=', DB::Raw('x.id'))
//                ->where('fct.model_type', '=', self::quote(User::class))
//                ->where('fct.owner_model_type', '=', self::quote(OtsProject::class))
//                ->where('fct.owner_model_id', '=', self::quote($user->getCurrentProject('id')));
//        })
//            ->join('custom_var_codes as cvcode', 'cvcode.var_id', '=', DB::Raw('fct.tag_id'))
//            ->whereIn('cvcode.code', $value);
//
//        $builder = (new CustomVar())->queryByModels(
//            $builder,
//            [
//                Site::class => $user->getSiteId(),
//                OtsProject::class => $user->getCurrentProject('id'),
//                User::class => $user->getId()
//            ],
//            false,
//            $parent_alias = 'cvcode'
//        );

        $builder->whereExists(function ($query) use ($value, $data)
        {
            /**
             * @var User $user
             */
            $user = $data['user'] ?? null;//$data['required_user_id'] ?? null;
            $query->from('custom_var_relates as cvr')
                ->leftJoin('custom_vars as cv', 'cv.id', '=', 'cvr.var_id')
                ->leftJoin('custom_var_models as cvcm', 'cvcm.var_id', '=', 'cv.id')
                ->leftJoin('category_codes as cc', 'cv.category_id', '=', 'cc.category_id')
                ->leftJoin('custom_var_codes as cvcode', function ($query)
            {
                $query->where('cvcode.var_id', '=', DB::Raw('cvr.var_id'));
            });

            $query->where('cc.code', TagService::DEFAULT_LIST_USER_TAGS)
                ->whereIn('cvcode.code', $value)
                ->where('cvr.model_id', '=', DB::Raw('x.id'))
                    ->where('cvr.model_type', '=', self::quote(User::class))
                    ->where('cvr.owner_model_type', '=', self::quote(OtsProject::class))
                    ->where('cvr.owner_model_id', '=', self::quote($user->getCurrentProject('id')));

            $query = (new CustomVar())->queryByModels(
                $query,
                [
                    Site::class => $user->getSiteId(),
                    OtsProject::class => $user->getCurrentProject('id'),
                    User::class => $user->getId()
                ],
                false,
                'cvcm'
            );

            $query->where('cvcm.active', true);
        });

        return $builder;
    }
}