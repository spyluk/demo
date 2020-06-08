<?php

namespace App\Validators\Forms\Message;

use App\Validators\DefaultValidator;

class ReplyValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $return = [
//            'subject' => 'required|string',
            'main_id'  => 'required|int',
            'message' => 'required|string',
            'tags' => ''
        ];

        return $return;
    }
}
