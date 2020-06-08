<?php

namespace App\Forms\Message;

use App\Models\User;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use App\Validators\Forms\Message\Send\ToEventValidator;
use App\Validators\Forms\Message\Send\ToUserValidator;
use Illuminate\Validation\ValidationException;

class SendToEventForm
{
    /**
     * @param User $user
     * @param array $data
     * @return \App\Models\OtsMessage|null
     * @throws ValidationException
     * @throws \Exception
     */
    public function send(User $user, array $data)
    {
        try {
            $validate = (new ToEventValidator($data))->validate();
        } catch (ValidationException $e) {
            throw $e;
        }

        $tags = !empty($validate['tags']) ? $validate['tags'] : [];
        $message = (new OtsMessageService($user))->
            messageToEvent(
                $validate['subject'],
                $validate['message'],
                $user->getId(),
                $validate['event_id'],
                $tags
            );

        if(!$message) {
            throw new \Exception('Error sending message.');
        }

        return $message;
    }
}