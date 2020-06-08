<?php

namespace App\Validators\Forms\Message;

use App\Validators\DefaultValidator;

class ItemsValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules($data_rules = [])
    {
        return [
            'page' => 'numeric|min:0',
            'from_date' => '',
            'end_date'   => 'after:from_date'
        ];
    }
}
