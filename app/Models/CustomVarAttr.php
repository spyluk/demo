<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class CustomVarAttr extends Model
{
    use BaseTrait;
    /**
     * @var array
     */
    protected $fillable = [
        'var_id',
        'value',
    ];
}
