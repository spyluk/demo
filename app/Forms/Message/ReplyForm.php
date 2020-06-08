<?php

namespace App\Forms\Message;

use App\Models\User;
use App\Services\Ots\Messages\MessageService as OtsMessageService;
use App\Validators\Forms\Message\ReplyValidator;
use Illuminate\Validation\ValidationException;

class ReplyForm
{
    /**
     * @param User $user
     * @param $site_id
     * @param array $data
     * @return \App\Models\OtsMessage|null
     * @throws ValidationException
     * @throws \Exception
     */
    public function send(User $user, array $data)
    {
        try {
            $validate = (new ReplyValidator($data))->validate();
        } catch (ValidationException $e) {
            throw $e;
        }

        $message = (new OtsMessageService($user))
            ->messageReply(
                "",
                $validate['message'],
                $user->getId(),
                $validate['main_id']
            );

        if(!$message) {
            throw new \Exception('Error sending message.');
        }

        return $message;
    }
}