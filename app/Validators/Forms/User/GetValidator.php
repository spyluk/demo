<?php

namespace App\Validators\Forms\User;

use App\Validators\DefaultValidator;
use App\Models\UserType;

class GetValidator extends DefaultValidator
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public static function rules($data_rules = [])
    {
        return [
            'user_id'  => 'filled',
        ];
    }
}