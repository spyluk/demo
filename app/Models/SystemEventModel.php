<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemEventModel
 * @property int $id
 * @property int $system_event_id
 * @property string $model_type
 * @property int $model_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @package App\Models
 */
class SystemEventModel extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'system_event_id',
        'model_type',
        'model_id'
    ];
}
