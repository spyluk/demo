<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class CustomVarCode extends Model
{
    use BaseTrait;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'var_id',
        'code',
        'category_id'
    ];
}
