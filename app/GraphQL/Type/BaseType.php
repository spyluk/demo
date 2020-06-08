<?php

namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Type;

class BaseType extends Type
{
    /**
     * Acl example
     *
     * @param $fields array
     * @param $default_available boolean
     * @return array
     */
    protected function accessFilter($fields, $default_available = true)
    {
        $return = [];

        foreach($fields as $key => $field) {
            if((!isset($field['available']) && $default_available) || $field['available']) {
                $return[$key] = $field;
            }
        }

        return $return;
    }

    /**
     * @param $array
     * @param string $separate
     * @return string
     */
    protected function getKeyValueSeparateString($array, $separate = ', ')
    {
        return implode($separate, array_map(function($item, $key){
            return $key . ' - ' . $item;
        }, $array, array_keys($array)));
    }
}