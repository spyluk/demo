<?php

namespace App\Validators\Forms;

use App\Validators\DefaultValidator;

class LoginValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6|max:50',
            'remember' => ''
        ];
    }
}
