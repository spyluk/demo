<?php

namespace App\Vsm\Models;

/**
 * User: Sergei
 * Date: 19.06.19
 */
class View
{
    /**
     * @var null|int
     */
    public $module_id = null;
    /**
     * @var string
     */
    public $content = '';
    /**
     * @var string
     */
    public $code = '';
    /**
     * @var null|int
     */
    public $updated_at = null;
    /**
     * @var array
     */
    public $attr = [];
    /**
     * @var array
     */
    public $var = [];

    /**
     * View constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        if($data) {
            foreach ($data as $key => $item) {
                $this->$key = in_array($key, ['attr', 'var']) ? json_decode($item, true) : $item;
            }
        }
    }
}