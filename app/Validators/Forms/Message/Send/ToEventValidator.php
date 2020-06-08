<?php

namespace App\Validators\Forms\Message\Send;

use App\Validators\DefaultValidator;

class ToEventValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $return = [
            'subject' => 'required|string',
            'event_id'  => 'required|int',
            'message' => 'required|string',
            'tags' => ''
        ];

        return $return;
    }
}
