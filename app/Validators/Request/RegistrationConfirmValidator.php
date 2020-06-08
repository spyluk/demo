<?php

namespace App\Validators\Forms;

use App\Validators\DefaultValidator;

class RegistrationConfirmValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'password'  => 'required|confirmed|min:6|max:50',
            'user_name' => 'required|string',
            'email'     => 'required|email',
        ];
    }
}