<?php
/**
 *
 * User: sergei
 * Date: 16.01.19
 * Time: 16:53
 */

namespace App\Services\Users;

use App\Components\Database\StructuringResult;
use App\Models\CustomVarRelate;
use App\Models\Language;
use App\Models\OtsMessage;
use App\Models\OtsMessageEvent;
use App\Models\Site;
use App\Models\User;
use App\Models\OtsProject;
use App\Services\Variables\Lists\TagList;
use App\Services\VariableService;

class TagService
{
    /**
     * List user tags
     */
    const DEFAULT_LIST_USER_TAGS = 'list.tags.users';
    /**
     * Tutor tag
     */
    const USER_TAG_TUTOR = 'tutor';
    /**
     * Tutor tag 'requested subjects'
     */
    const USER_TAG_TUTOR_REQUESTED_SUBJECTS = 'tutor.requested.subjects';

    /**
     * @param array $tags
     * @param User $user
     * @param int $owner_model_type
     * @param int $owner_model_id
     * @param bool|User $by_system_or_by_user if tag set by system or
     * @return array|null
     * @throws \Exception
     */
    public function add(array $tags, User $user, $owner_model_type = null, $owner_model_id = null, $by_system_or_by_user = false)
    {
        $is_system = (is_bool($by_system_or_by_user) && $by_system_or_by_user ? true : false);
        $userSet = $by_system_or_by_user instanceof User ? $by_system_or_by_user : $user;

        $project = $user->getCurrentProject();
        $models = [
            Site::class => $userSet->site_id,
            OtsProject::class => $user->getCurrentProject('id'),
            User::class => $userSet->id
        ];

        $tags = (new TagList($project, $tags, Language::ENGLISH, $models));
        $tags = $is_system ? $tags->getSelected($userSet) : $tags->getAvailable($userSet);
        if(!$tags) {
            throw new \Exception('List of available tags is empty or not valid');
        }

        $userTagService = new CustomVarRelate();
        foreach($tags as $tag) {
            $userTagService->add($user, $user->id, $tag['id'], $owner_model_type, $owner_model_id);
        }

        return $tags;
    }

    /**
     * @param array $tags
     * @param User $user
     * @param int $owner_model_type
     * @param int $owner_model_id
     * @param bool $by_system_or_by_user
     * @return array|null
     * @throws \Exception
     */
    public function remove(array $tags, User $user, $owner_model_type = null, $owner_model_id = null, $by_system_or_by_user = false)
    {
        $is_system = (is_bool($by_system_or_by_user) && $by_system_or_by_user ? true : false);
        $userSet = $by_system_or_by_user instanceof User ? $by_system_or_by_user : $user;

        $project = $user->getCurrentProject();
        $models = [
            Site::class => $userSet->site_id,
            OtsProject::class => $user->getCurrentProject('id'),
            User::class => $userSet->id
        ];

        $tags = (new TagList($project, $tags, Language::ENGLISH, $models));
        $tags = $is_system ? $tags->getSelected($userSet) : $tags->getAvailable($userSet);
        if(!$tags) {
            throw new \Exception('List of available tags is empty or not valid');
        }

        $userTagService = new CustomVarRelate();
        foreach($tags as $tag) {
            $userTagService->remove($user, $user->id, $tag['id'], $owner_model_type, $owner_model_id);
        }

        return $tags;
    }
}