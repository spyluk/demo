<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class OtsMessageStatistic extends Model
{
    use BaseTrait;

    /**
     * @var string
     */
    protected $primaryKey = 'main_id';
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'main_id',
        'count',
        'last_id',
        'last_at',
    ];
}
