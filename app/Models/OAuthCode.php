<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Laravel\Passport\AuthCode;

class OAuthCode extends AuthCode
{
    use BaseTrait;
}
