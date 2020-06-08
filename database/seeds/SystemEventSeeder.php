<?php

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\SystemEvent;
use App\SystemListeners\ChangeUserTags;
use \App\Services\Users\TagService;
use \App\Models\SystemEventAction;
use \App\Models\SystemEventModel;
use \App\Models\SystemEventModelAction;

class SystemEventSeeder extends Seeder
{
    protected $events = [
        //REGISTRATION
        \App\Events\User\RegistrationEvent::class => [ //add tag tutor.requested.subjects
            'model_type' => Site::class,
            'model_id' => Site::ONLINETUTORSERVICE,
            'action' => ChangeUserTags::class,
            'data' => ['add' => ['client']]
        ],
        //END REGISTRATION
        //TUTOR SUBJECTS
        \App\Events\User\Subjects\RequestUserSubjectsEvent::class => [ //add tag tutor.requested.subjects
            'model_type' => Site::class,
            'model_id' => Site::ONLINETUTORSERVICE,
            'action' => ChangeUserTags::class,
            'data' => ['add' => [TagService::USER_TAG_TUTOR_REQUESTED_SUBJECTS], 'tag' => 'tutor', 'project_group_id' => null]
        ],
        \App\Events\User\Subjects\AdditionalRequestUserSubjectsEvent::class => [ //add tag tutor.requested.subjects
            'model_type' => Site::class,
            'model_id' => Site::ONLINETUTORSERVICE,
            'action' => ChangeUserTags::class,
            'data' => ['add' => [TagService::USER_TAG_TUTOR_REQUESTED_SUBJECTS], 'tag' => 'tutor', 'project_group_id' => null]
        ],
        \App\Events\User\Subjects\ApproveUserSubjectsEvent::class => [ //add tag tutor and remove tag tutor.requested.subjects
            'model_type' => Site::class,
            'model_id' => Site::ONLINETUTORSERVICE,
            'action' => ChangeUserTags::class,
            'data' => ['add' => [TagService::USER_TAG_TUTOR], 'remove' => [TagService::USER_TAG_TUTOR_REQUESTED_SUBJECTS], 'tag' => 'tutor', 'project_group_id' => null]
        ],
        \App\Events\User\Subjects\PartialApproveUserSubjectsEvent::class => [ //add tag tutor
            'model_type' => Site::class,
            'model_id' => Site::ONLINETUTORSERVICE,
            'action' => ChangeUserTags::class,
            'data' => ['add' => [TagService::USER_TAG_TUTOR], 'tag' => 'tutor', 'project_group_id' => null]
        ],
        \App\Events\User\Subjects\DeclineUserSubjectsEvent::class => [ //add tag tutor
            'model_type' => Site::class,
            'model_id' => Site::ONLINETUTORSERVICE,
            'action' => ChangeUserTags::class,
            'data' => ['remove' => [TagService::USER_TAG_TUTOR], 'tag' => 'tutor', 'project_group_id' => null]
        ],
        \App\Events\User\Subjects\PartialDeclineUserSubjectsEvent::class => [],
        //END TUTOR SUBJECTS
        \App\Events\Ots\Events\RequestEvent::class => [],
        \App\Events\Ots\Events\ChangeStatusEvent::class => [],
        \App\Events\Ots\Events\ConfirmRequestEvent::class => [],
        \App\Events\Ots\Messages\NewMessage::class => [],
        \App\Events\Ots\Messages\ReadMessage::class => []
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $systemEvent = new SystemEvent();
        $systemEventAction = new SystemEventAction();
        $systemEventModel = new SystemEventModel();
        $systemEventModelAction = new SystemEventModelAction();

        foreach($this->events as $event => $data) {

            if(!($eventModel = $systemEvent->getItemByFields(['model_type' => $event]))) {
                $eventModel = $systemEvent->create(['model_type' => $event]);
            }

            $eventModelAction = null;
            if(!empty($data['action']) && !($eventModelAction = $systemEventAction->getItemByFields(['model_type' => $data['action']]))) {
                $eventModelAction = $systemEventAction->create(['model_type' => $data['action']]);
            }

            $eventModelModel = null;
            if($data && !empty($data['model_type']) && !empty($data['model_id']) && $eventModel) {
                $eventModelModel = $systemEventModel->getItemByFields([
                    'system_event_id' => $eventModel->id,
                    'model_type' => $data['model_type'],
                    'model_id' => $data['model_id']
                ]);

                if(!$eventModelModel) {
                    $eventModelModel = $systemEventModel->create([
                        'system_event_id' => $eventModel->id,
                        'model_type' => $data['model_type'],
                        'model_id' => $data['model_id']
                    ]);
                }
            }

            if($eventModelAction && $eventModelModel) {
                if((!$systemEventModelAction->getItemByFields([
                    'system_event_model_id' => $eventModelModel->id,
                    'system_event_action_id' => $eventModelAction->id
                ]))) {
                    $last_action = $systemEventModelAction->getInitDbByFields([
                        'system_event_model_id' => $eventModelModel->id
                    ])->orderBy('order', 'desc')->first();

                    $order = $last_action ? $last_action->order++ : 1;

                    $systemEventModelAction->create([
                        'system_event_model_id' => $eventModelModel->id,
                        'system_event_action_id' => $eventModelAction->id,
                        'order' => $order,
                        'data' => $data['data'] ? json_encode($data['data']) : ''
                    ]);
                }
            }

        }
    }
}
