<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class Entity extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];
    /**
     *
     */
    const MODULE = 1;

    /**
     * @var array
     */
    public static $list = [
        self::MODULE => 'Module',
    ];
}
