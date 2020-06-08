<?php
/**
 * User: Sergei
 * Date: 11.05.20
 */

namespace App\Services\Variables\Lists;

use App\Models\User;

class TagList extends DefaultList
{
    /**
     * @return string
     */
    protected function getListCode(): string
    {
        return 'list.tags.users';
    }

    /**
     * @param User $user
     * @return array
     */
    public function getTeachAvailable(User $user)
    {
        return $this->filterList(function($item, &$result) use ($user) {
            if($user->hasPermissionTo('var.' . $item['id']) &&
                $item['vcode'] &&
                in_array($item['vcode'], config('project.teaching_tags'))) {
                $result[] = $item;
            }

            return true;
        });
    }

}