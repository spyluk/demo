<?php
/**
 *
 * User: sergei
 * Date: 02.10.18
 * Time: 14:05
 */

namespace App\Components\Database\Schema;

use Illuminate\Database\Schema\Blueprint as ParentBlueprint;

class Blueprint extends ParentBlueprint
{

    public function timestamp($column, $precision = 0)
    {
        return $this->integer($column)->default(0);
    }
}