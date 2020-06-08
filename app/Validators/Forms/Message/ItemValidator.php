<?php

namespace App\Validators\Forms\Message;

use App\Validators\DefaultValidator;

class ItemValidator extends DefaultValidator
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|numeric|min:0',
            'page' => 'numeric|min:0',
        ];
    }
}
