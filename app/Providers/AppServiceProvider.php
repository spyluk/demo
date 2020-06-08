<?php

namespace App\Providers;

use App\GraphQL\Traits\BaseTrait;
use App\Models\CategoryType;
use App\Models\CustomVar;
use App\Models\OtsEvent;
use App\Models\OtsEventType;
use App\Models\OtsProject;
use App\Models\OtsProjectGroup;
use App\Models\Site;
use App\Models\User;
use App\Services\Users\TagService;
use App\Services\Variables\Lists\SubjectList;
use App\Services\Variables\Lists\TagList;
use App\Services\VariableService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;
use App\Services\Ots\Users\SubjectService;

class AppServiceProvider extends ServiceProvider
{
    use BaseTrait;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('numericarray', function ($attribute, $value, $parameters)
        {
            if(!is_array($value)) {
                return false;
            }
            foreach ($value as $v) {
                if (!is_numeric($v)) {
                    return false;
                }
            }
            return true;
        });

        $this->app['validator']->extend('available_tag', function ($attribute, $tag, $parameters)
        {
            $tags = is_array($tag) ? $tag : [$tag];

            $available_tags = (new TagList($this->user()->getCurrentProject(), $tags, $this->language('id')))
                ->getOnAllAvailable($this->user());

            return !!$available_tags;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
