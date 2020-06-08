<?php

namespace App\Services;

use App\Models\OtsProject;
use App\Models\Site;
use Illuminate\Support\Facades\DB;

class SystemEventService
{
    /**
     * @var array
     */
    protected static $system_event_actions = [];

    /**
     * @param $name
     * @param $data
     * @param $site_id
     * @param $project_id
     */
    public function handle($name, $event, $site_id, $project_id)
    {
        if(is_object($event)) {
            if($actions = $this->getSystemEvent($name, $site_id, $project_id)) {
                foreach($actions as $action_orders) {
                    foreach($action_orders as $action => $data) {
                        if(class_exists($action)) {
                            new $action($event, $data);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $name
     * @param $site_id
     * @param $project_id
     * @return array|mixed
     */
    public function getSystemEvent($name, $site_id, $project_id)
    {
        if(empty(static::$system_event_actions[$project_id])) {
            $system_event_actions = DB::select(
            'SELECT sej.model_type, semaj.order, semaj.data, seaj.model_type as action from `system_event_models` as sem '.
            'left join `system_events` sej on (sem.system_event_id=sej.id) '.
            "left join `system_event_models` semj ON (semj.system_event_id=sem.system_event_id and semj.model_type=? and semj.model_id=?) ".
            'left join `system_event_model_actions` semaj on (semaj.system_event_model_id=sem.id) '.
            'left join `system_event_actions` seaj on (semaj.system_event_action_id=seaj.id) '.
            "where (sem.model_type=? and sem.model_id=? and semj.id is null) or ".
            "(sem.model_type=? and sem.model_id=?) order by semaj.order",
            [
                OtsProject::class,
                $project_id,
                Site::class,
                $site_id,
                OtsProject::class,
                $project_id
            ]);
            if($system_event_actions) {
                foreach($system_event_actions as $item) {
                    static::$system_event_actions[$project_id][$item->model_type][$item->order][$item->action] = $item->data ? json_decode($item->data, true) : [];
                }
            }
        }

        return !empty(static::$system_event_actions[$project_id][$name]) ? static::$system_event_actions[$project_id][$name] : [];
    }
}
