<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class OtsMessageUserTag extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'message_id',
        'user_id',
        'tag_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
