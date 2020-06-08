<?php
/**
 *
 * User: sergei
 * Date: 04.10.18
 * Time: 13:50
 */

namespace App\Components\Database;


class StructuringResult
{
    /**
     * @param $data
     * @param $structure
     * @return array
     */
    public static function apply($data, $structure, $custom_methods = [])
    {
        $result = [];
        foreach($data as $item) {
            self::filter($result, $structure, (array)$item, $custom_methods);
        }

        return $result ? $result : $data;
    }

    /**
     * @param $result
     * @param $structure
     * @param $data
     * @return mixed
     */
    protected static function filter(&$result, $structure, $data, $custom_methods = [])
    {
        foreach ($structure as $skey => $sitem) {
            $custom_method_before = $custom_method_after = '';
            if (is_string($skey)) {
                if (strpos($skey, "$") !== false) {
                    $skey = str_replace("$", "", $skey);
                    $skey = isset($data[$skey]) ? $data[$skey] : $skey;
                } elseif (strpos($skey, "@") !== false) {
                    $skey = str_replace("@", "", $skey);
                    $custom_method_before = isset($custom_methods['before'][$skey]) ? $custom_methods['before'][$skey] : null;
                    $custom_method_after = isset($custom_methods['after'][$skey]) ? $custom_methods['after'][$skey] : null;
                }
            } else {
                if ($result) {
                    end($result);
                    $skey = key($result) + 1;
                } else {
                    $skey = 0;
                }
            }

            $can_handle = true;

            if (is_array($sitem)) {

                if ($custom_method_before) {
                    $can_handle = $custom_method_before($data, $skey, $sitem);
                }

                if ($can_handle) {
                    self::filter($result[$skey], $sitem, $data, $custom_methods);
                    if (!$result[$skey]) {
                        unset($result[$skey]);
                    } elseif ($custom_method_after) {
                        $result[$skey] = $custom_method_after($result[$skey], $skey, $data, $sitem);
                    }
                }
            } else {

                if(is_object($sitem)) {
                    $tmp_result = !isset($result[$skey]) ? '' : $result[$skey];
                    $res = $sitem($tmp_result, $skey, $data, $sitem);
                    /*@TODO проверить работу всех списков и деревьев, добавил проверку || is_array($res)*/
                    if($res || is_array($res)) {
                        $result[$skey] = $res;
                    }
                } elseif($sitem == '*') {
                    $result[$skey] = $data;
                } elseif (isset($data[$sitem])) {

                    if ($custom_method_before) {
                        $can_handle = $custom_method_before($data, $skey, $sitem);
                    }

                    if ($can_handle) {
                        $result[$skey] = $data[$sitem];
                        if ($custom_method_after) {
                            $result[$skey] = $custom_method_after($result[$skey], $skey, $data, $sitem);
                        }
                    }
                }
            }
        }

        return $result;
    }
}