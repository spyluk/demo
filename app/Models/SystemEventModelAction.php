<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemEventModel
 * @property int $id
 * @property int $system_event_model_id
 * @property int $system_event_action_id
 * @property int $order
 * @property string $data
 * @property int $created_at
 * @property int $updated_at
 *
 * @package App\Models
 */
class SystemEventModelAction extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'system_event_model_id',
        'system_event_action_id',
        'order',
        'data'
    ];
}
