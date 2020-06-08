<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemEvent
 * @property int $id
 * @property string $model_type
 * @property boolean $active
 * @property int $created_at
 * @property int $updated_at
 *
 * @package App\Models
 */
class SystemEvent extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'model_type',
        'active'
    ];
}
