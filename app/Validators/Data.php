<?php
/**
 * 
 * User: sergei
 * Date: 07.08.18
 * Time: 17:31
 */

namespace App\Validators;


class Data
{
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var null
     */
//    protected $model = null;

    /**
     * Data constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData($exclude = [])
    {
        if (!$exclude) {
            return $this->data;
        } else {
            $data = $this->data;
            foreach ($exclude as $key) {
                if (isset($data[$key])) {
                    unset($data[$key]);
                }
            }
            return $data;
        }
    }

    /**
     * @return null
     */
//    public function getModel()
//    {
//        return $this->model;
//    }

    /**
     * @param $model
     */
//    public function setModel($model)
//    {
//        $this->model = $model;
//    }

    /**
     * @param $key_or_array
     * @param null $value
     */
    public function addData($key_or_array, $value = null)
    {
        if (is_array($key_or_array)) {
            foreach ($key_or_array as $key => $value) {
                $this->data[$key] = $value;
            }
        } else {
            $this->data[$key_or_array] = $value;
        }
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !$this->errors;
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @param $field
     * @return mixed|null
     */
    public function __get($field)
    {
        if(isset($this->data[$field])) {
            return $this->data[$field];
        }

        return null;
    }
}