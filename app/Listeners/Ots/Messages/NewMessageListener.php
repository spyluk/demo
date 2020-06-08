<?php

namespace App\Listeners\Ots\Messages;

use App\Events\Ots\Messages\NewMessage;
use App\Models\User;
use App\Notifications\Ots\Messages\NewMessage as NewMessageNotification;

class NewMessageListener
{
    /**
     * RequestListener constructor.
     * @param NewMessage $event
     */
    public function handle(NewMessage $event)
    {
        if($event->user_id != $event->from_user_id) {
            $user = (new User)->getById($event->user_id);
            $user->notify(new NewMessageNotification($user));
        }
    }
}
