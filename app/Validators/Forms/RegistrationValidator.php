<?php

namespace App\Validators\Forms;

use App\Validators\Services\UserValidator;

class RegistrationValidator extends UserValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
                'password'  => 'required|confirmed|min:6|max:50',
                'email' => 'required|email|unique:users,email,NULL,id,site_id,'. $this->data['site_id'],
        ] + parent::rules();
    }
}