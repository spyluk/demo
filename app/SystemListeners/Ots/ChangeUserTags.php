<?php
/**
 * User: Sergei
 * Date: 18.03.20
 */

namespace App\SystemListeners;

use App\Models\OtsProject;
use App\Services\Users\TagService;

/**
 * Class ChangeUserTags
 *
 * @package App\SystemListeners
 */
class ChangeUserTags
{
    /**
     * ChangeUserTags constructor.
     *
     * @param $event
     * @param array $data
     */
    public function __construct($event, array $data)
    {
        if(is_object($event) && $event->user &&
            (empty($data['tag']) || $event->tag_code == $data['tag']) &&
            (!isset($data['project_group_id']) || $event->project_group_id == $data['project_group_id'])) {
            $event->user->setCurrentProject(user()->getCurrentProject());
            if(!empty($data['remove'])) {
                (new TagService)->remove(
                    $data['remove'],
                    $event->user,
                    OtsProject::class,
                    user()->getCurrentProject('id'),
                    true);
            }

            if(!empty($data['add'])) {
                (new TagService)->add(
                    $data['add'],
                    $event->user,
                    OtsProject::class,
                    user()->getCurrentProject('id'),
                    true);
            }
        }
    }
}