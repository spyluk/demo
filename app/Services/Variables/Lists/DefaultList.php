<?php
/**
 * User: Sergei
 * Date: 11.05.20
 */

namespace App\Services\Variables\Lists;

use App\Models\Language;
use App\Models\OtsProject;
use App\Models\Site;
use App\Models\User;
use App\Services\VariableService;

abstract class DefaultList
{
    /**
     * @var null
     */
    static $list = [];
    /**
     * @var OtsProject
     */
    protected $project;
    /**
     * @var array
     */
    protected $selected_items;
    /**
     * @var int
     */
    protected $language_id;
    /**
     * @var array
     */
    protected $models;

    /**
     * @return string
     */
    abstract protected function getListCode(): string;

    /**
     * SubjectList constructor.
     *
     * @param OtsProject $project
     * @param array|null $selected_items
     */
    public function __construct(OtsProject $project, array $selected_items = null, int $language_id = Language::ENGLISH, $models = [])
    {
        $this->project = $project;
        $this->selected_items = $selected_items;
        $this->language_id = $language_id;
        $this->models = $models ? $models : [
            Site::class => $this->project->site_id,
            OtsProject::class => $this->project->id
        ];
    }

    /**
     * Only if all requested elements in the selected_items array are available
     *
     * @param User $user
     * @return array
     */
    public function getOnAllAvailable(User $user)
    {
        return $this->filterList(function($item, &$result) use ($user) {
            if($user->hasPermissionTo('var.' . $item['id'])) {
                $result[] = $item;
            } else {
                $result = [];
                return false;
            }
            return true;
        });
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAvailable(User $user)
    {
        return $this->filterList(function($item, &$result) use ($user) {
            if($user->hasPermissionTo('var.' . $item['id'])) {
                $result[] = $item;
            }
            return true;
        });
    }

    /**
     * @param User $user
     * @return array
     */
    public function getSelected()
    {
        return $this->filterList(function($item, &$result) {
            $result[] = $item;
            return true;
        });
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        $list_code = $this->getListCode();
        if(!isset(self::$list[$list_code])) {
            $res = VariableService::getVars(
                [$list_code],
                $this->project->site_id,
                null,
                $this->models,
                $this->language_id
            );
            self::$list[$list_code] = $res[$list_code];
        }

        return self::$list[$list_code];
    }

    /**
     * @param $callback
     * @return array
     */
    protected function filterList($callback)
    {
        $findItem = function($item, $selected_items) {
            foreach($selected_items as $selected_item) {
                if(is_numeric($selected_item) && $selected_item == $item['id']) {
                    return true;
                }else if(is_string($selected_item) && $selected_item == $item['vcode']) {
                    return true;
                }
            }
            return false;
        };

        $result = [];
        if($list = $this->getAll()) {
            foreach ($list as $item) {
                if(is_null($this->selected_items) || $findItem($item, $this->selected_items, true))
                {
                    if(!$callback($item, $result)) {
                        break;
                    }
                }
            }
        }

        return $result;
    }
}