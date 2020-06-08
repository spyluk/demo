<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class CategoryType extends Model
{
    use BaseTrait;
    /**
     *
     */
    const MODULE = 1;
    /**
     *
     */
    const VARS = 2;
    /**
     *
     */
    const TAGS = 3;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public static $types = [
        self::MODULE => 'Modules',
        self::VARS => 'Vars',
        self::TAGS => 'Tags'
    ];
}
