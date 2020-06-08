<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Eloquent\SoftDeletes;
use App\Models\Traits\BaseTrait;

class AclModelHasRole extends Model
{
    use BaseTrait, SoftDeletes;

}
