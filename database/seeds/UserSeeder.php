<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Forms\RegistrationForm;
use App\Models\Site;
use \App\Models\Language;
use \App\Services\VariableService;
use \App\Services\Users\TagService;
use \App\Services\Ots\ProjectService;
use \App\Models\OtsProject;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
                ['data' => ['email' => 'visitor@test.com', 'password' => 'qwqwqw', 'first_name' => 'Visitor', 'last_name' => 'Visitor' ], 'ip' => '104.248.3.32', 'lang' => Language::RUSSIAN, 'site' => Site::TEST, 'tags' => ['visitor'], 'type_id' => '1'],
                ['data' => ['email' => 'admin@test.com', 'password' => 'qwqwqw', 'first_name' => 'Admin', 'last_name' => 'Admin' ], 'ip' => '104.248.3.32', 'lang' => Language::RUSSIAN, 'site' => Site::TEST, 'tags' => ['client', 'manager']],
                ['data' => ['email' => 'valera@test.com', 'password' => 'qwqwqw', 'first_name' => 'Valera', 'last_name' => 'Romanov' ], 'ip' => '81.163.52.172', 'lang' => Language::RUSSIAN, 'site' => Site::TEST, 'tags' => ['client']],
            ];

        foreach ($users as $user_data) {
            $user_data['data']['password_confirmation'] = $user_data['data']['password'];

            try
            {
                $user = (new RegistrationForm())->register(
                    $user_data['data'],
                    Site::TEST,
                    $user_data['ip'],
                    $user_data['lang']
                );

                $default_project = (new ProjectService)->getProjectByIdOrDefault($user);
                $user->setCurrentProject($default_project);
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'active' => !isset($user->active) ? 1 : $user->active,
                        'type_id' => !isset($user->type_id) ? 2 : $user->type_id,
                    ]);

                (new TagService)->add($user_data['tags'], $user, OtsProject::class, $default_project->id, true);
            } catch (\Illuminate\Validation\ValidationException $e) {
                print_r($e->errors());exit;
            }
        }
    }

    public function getUserTags()
    {
        $return = [];
        $res = VariableService::getCustomModelTagsByCategory(TagService::DEFAULT_LIST_USER_TAGS);

        foreach($res as $item) {
            $return[$item->code][$item->model_type][$item->model_id] = $item->var_id;
        }

        return $return;
    }
}
