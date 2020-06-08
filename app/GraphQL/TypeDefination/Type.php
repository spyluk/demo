<?php

namespace App\GraphQL\TypeDefination;

/**
 * User: Sergei
 * Date: 14.07.19
 */

use GraphQL\Type\Definition\Type as TypeBase;

abstract class Type extends TypeBase
{
    /**
     * @api
     * @return ArrayType
     */
    public static function arr($name = 'array')
    {
        return new ArrayType($name);
    }
}