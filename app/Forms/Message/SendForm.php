<?php

namespace App\Forms\Message;

use App\Models\User;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use App\Validators\Forms\Message\Send\ToUserValidator;
use Illuminate\Validation\ValidationException;

class SendForm
{
    /**
     * @param User $user
     * @param $site_id
     * @param array $data
     * @return \App\Models\OtsMessage|null
     * @throws ValidationException
     * @throws \Exception
     */
    public function send(User $user, $site_id, array $data)
    {
        try {
            $data = $data + ['site_id' => $site_id, 'current_user_id' => $user->getId()];
            $validate = (new ToUserValidator($data))->validate();
        } catch (ValidationException $e) {
            throw $e;
        }

        $tags = !empty($validate['tags']) ? $validate['tags'] : ["common"];
        $message = (new OtsMessageService($user))->
            messageToUser(
                $validate['subject'],
                $validate['message'],
                $user->getId(),
                $site_id,
                $validate['user_id'],
                $tags
            );

        if(!$message) {
            throw new \Exception('Error sending message.');
        }

        return $message;
    }
}