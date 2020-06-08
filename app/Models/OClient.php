<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Laravel\Passport\Client;

class OClient extends Client
{
    use BaseTrait;
    /**
     *
     */
    const INSTITUTIOR_PERSONAL_ACCESS_CLIENT = 1;
    /**
     *
     */
    const INSTITUTIOR_PASSWORD_GRANT_CLIENT = 2;
}
