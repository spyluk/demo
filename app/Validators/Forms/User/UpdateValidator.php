<?php

namespace App\Validators\Forms\User;

use App\Validators\DefaultValidator;

class UpdateValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'avatar' => 'sometimes|image|mimes:jpg,png,gif|max:2000',
            'password'  => 'required|sometimes|confirmed|min:6|max:50',
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
        ];
    }
}