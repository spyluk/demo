<?php

namespace App\Validators\Forms;

use App\Validators\DefaultValidator;

class UsersValidator extends DefaultValidator
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
        ];
    }
}
