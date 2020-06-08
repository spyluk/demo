<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class OtsMessageEntity extends Model
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
        'main_id',
        'entity_id',
        'pk_id',
    ];
}
