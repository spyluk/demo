<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BaseTrait;

class Country extends Model
{
    use BaseTrait;
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param $code
     *
     * @return Country
     */
    public function getCountryByIso2($code)
    {
        $return = self::where('alpha2', $code)
            ->first();

        return $return;
    }

    /**
     * @return Country
     */
    public function getDefaultCountry()
    {
        return self::first();
    }
}
