<?php

namespace App\Validators\Services;

use App\Validators\DefaultValidator;

class UserValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,site_id,'.$this->data['site_id'],
            'site_id' => 'required|integer',
            'language_id' => 'required|integer',
            'country_id' => 'sometimes|integer',
            'timezone_id' => 'sometimes|integer',
            'password' => 'required|min:6|max:50',
            'remember_token' => 'sometimes|string',
            'confirmation_code' => 'sometimes|string',
        ];
    }
}