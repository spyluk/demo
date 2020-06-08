<?php

namespace App\Validators\Forms\Message\Send;

use App\Validators\DefaultValidator;

class ToUserValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $site_id = $this->data['site_id'];
        $current_user_id = $this->data['current_user_id'];
        $return = [
            'subject' => 'required|string',
            'user_id'  => 'required|int|exists:users,id,active,1,site_id,' . $site_id . '|not_in:' . $current_user_id,
            'message' => 'required|string',
            'tags' => ''
        ];

        return $return;
    }
}
