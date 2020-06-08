<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class Timezone extends Model
{
    use BaseTrait;
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var null|array
     */
    public static $timezones = null;

    /**
     * @param $timeRegion
     * @return Timezone
     */
    public function getTimezoneByTimeRegion($timeRegion)
    {
        $return = self::where('name', $timeRegion)
            ->first();

        return $return;
    }

    /**
     * @param $timeRegion
     * @return mixed|null
     */
    public function getTimezoneIdByTimeRegion($timeRegion)
    {
        $region = $this->getTimezoneByTimeRegion($timeRegion);
        return $region ? $region->id : null;
    }

    /**
     * @return array
     */
    public function getTimezones()
    {
        if(is_null(static::$timezones))
        {
            static::$timezones = array_column(
                static::get()->toArray(),
                'name', 'id');

        }

        return static::$timezones;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getTimezoneById($id)
    {
        $timezones = $this->getTimezones();
        if(!isset($timezones[$id]))
        {
            throw new \Exception('Timezone '.$id.' not found!');
        }

        return $timezones[$id];
    }
}