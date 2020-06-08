<?php

namespace App\Models;

use App\Models\Traits\BaseTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SystemEventAction
 * @property int $id
 * @property string $model_type
 * @property int $created_at
 * @property int $updated_at
 *
 * @package App\Models
 */
class SystemEventAction extends Model
{
    use BaseTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'model_type',
    ];
}
