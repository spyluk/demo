<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Laravel\Passport\Token;

class OToken extends Token
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'scopes',
        'revoked',
        'created_at',
        'updated_at',
        'expires_at'
        ];
}
