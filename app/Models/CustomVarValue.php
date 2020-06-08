<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class CustomVarValue extends Model
{
    use BaseTrait;
    /**
     * @var array
     */
    protected $fillable = [
        'language_id',
        'var_id',
        'value',
    ];
}
