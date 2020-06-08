<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Laravel\Passport\PersonalAccessClient;

class OPersonalAccessClient extends PersonalAccessClient
{
    use BaseTrait;
}
