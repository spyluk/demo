<?php

use Illuminate\Database\Seeder;
use App\Models\Site;
use \App\Models\CustomVarModel;
use \App\Services\Users\TagService;

class RolesToTagSeeder extends Seeder
{

    /**
     * tags and roles
     * @var array
     */
    protected $model_tags = [
        'visitor' => [
            Site::class => [ Site::ONLINETUTORSERVICE => ['visitor']]
        ],
        'client' => [
            Site::class => [ Site::ONLINETUTORSERVICE => ['visitor', 'client']]
        ],
        'manager' => [
            Site::class => [ Site::ONLINETUTORSERVICE => ['manager']]
        ],
        'tutor' => [
            Site::class => [ Site::ONLINETUTORSERVICE => ['tutor']]
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $custom_var_models = (new CustomVarModel)->getCustomModelByCategoryAndModel(
            TagService::DEFAULT_LIST_USER_TAGS, Site::class, Site::ONLINETUTORSERVICE);

        /**
         * @var CustomVarModel $cvc_model
         */
        foreach($custom_var_models as $cvc_model) {
            if(empty($this->model_tags[$cvc_model->code])) {
                continue;
            }
            $model_tag = $this->model_tags[$cvc_model->code];

            if(!empty($model_tag[$cvc_model->model_type]) && !empty($model_tag[$cvc_model->model_type][$cvc_model->model_id])) {
                foreach($model_tag[$cvc_model->model_type][$cvc_model->model_id] as $role) {
                    $cvc_model->assignRole($role);
                }
            }
        }
    }
}
