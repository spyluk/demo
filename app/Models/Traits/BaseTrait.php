<?php
/**
 * 
 * User: sergei
 * Date: 29.07.18
 * Time: 11:06
 */

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use App\Components\Database\Query\Builder as QueryBuilder;
use App\Components\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;

trait BaseTrait
{
    /**
     * @var array
     */
    protected $use_timestamp_for = [];

    /**
     * @param $date
     * @return mixed
     */
    public function getCreatedAtAttribute($date)
    {
        return $date;
    }

    /**
     * @param $date
     * @return mixed
     */
    public function getUpdatedAtAttribute($date)
    {
        return $date;
    }

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @return string
     */
    public function fromDateTime($value)
    {
        return empty($value) ? $value : $this->asDateTime($value)->getTimestamp();
    }

    /**
     * @param array $data
     * @return null
     * @throws \Exception
     */
    public function create(array $data)
    {
        $return = null;
        if(!($data = $this->prepareData($data)) || !($return = parent::create($data))) {
            throw new \Exception('Error inserting in ' . static::class);
        }

        return $return;
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return null
     * @throws \Exception
     */
    public function updateOrCreate(array $attributes, array $values = array())
    {
        $return = null;
        if($data = $this->prepareData($values)) {
            $instance = self::firstOrNew($attributes);
            $instance->fill($data)->save();
            $return = $instance;
        }

        if(!$return) {
            throw new \Exception('Error inserting/updating ' . static::class);
        }

        return $return;
    }

    /**
     * @param array $attributes
     * @param array $values
     * @return null|Model
     */
    public function updateOnExists(array $attributes, array $values = array())
    {
        $return = null;
        if($data = $this->prepareData($values)) {
            $instance = $this->getInitDbByFields($attributes)->first();

            if($instance) {
                $instance->fill($data)->save();
                $return = $instance;
            }
        }

        return $return;
    }

    /**
     * @param $query
     * @return EloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }

    /**
     * @param array $fields
     * @param array $additional_filter_data
     * @return QueryBuilder
     */
    public function getInitDbByFields(array $fields = [], $additional_filter_data = [])
    {
        $query = self::from($this->getTable() . ' as x');
        foreach ($fields as $key => $value) {
            $decorator = static::createFilterDecorator($key);
            if (static::isValidDecorator($decorator)) {
                $query = $decorator::apply($query, $value, $additional_filter_data[$key] ?? []);
            } elseif(is_array($value)) {
                $query->whereIn($key, $value);
            } elseif(is_null($value)) {
                $query->whereNull($key);
            } else {
                $query->where($key, '=', $this->quote($value));
            }
        }

        return $query;
    }

    /**
     * @param $name
     * @return string
     */
    private static function createFilterDecorator($name)
    {
        $class_name = substr(strrchr(__CLASS__, "\\"), 1);
        $namespace = str_replace($class_name, '', __CLASS__);
        return $namespace . 'Filters\\' . $class_name . '\\' .
        str_replace(' ', '',
            ucwords(str_replace('_', ' ', $name . 'Filter')));
    }

    /**
     * @param $decorator
     * @return bool
     */
    private static function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $fields = $this->getFillable();
        foreach ($data as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($data[$key]);
//            } elseif(strpos($key, '_at', -3) !== false) {
//                $data[$key] = (new Carbon($data[$key]))->getTimestamp();
            }
        }

        return $data;
    }

    /**
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder(
            $connection, $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    protected function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        if (! is_null(static::UPDATED_AT) &&
            ! $this->isDirty(static::UPDATED_AT) &&
            (empty($this->use_timestamp_for) || in_array(static::UPDATED_AT, $this->use_timestamp_for))) {
            $this->setUpdatedAt($time);
        }

        if (! $this->exists && ! is_null(static::CREATED_AT) &&
            ! $this->isDirty(static::CREATED_AT) &&
            (empty($this->use_timestamp_for) || in_array(static::CREATED_AT, $this->use_timestamp_for))) {
            $this->setCreatedAt($time);
        }
    }

    /**
     * @param $value
     * @return \Illuminate\Database\Query\Expression
     */
    public function quote($value)
    {
        return DB::Raw(DB::connection()->getPdo()->quote($value));
    }

    /**
     * Add the date attributes to the attributes array.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function addDateAttributesToArray(array $attributes)
    {

//        foreach ($this->getDates() as $key) {
//            if (! isset($attributes[$key])) {
//                continue;
//            }
//            print_r($this->asDateTime($attributes[$key]));exit;
//            $attributes[$key] = $this->serializeDate(
//                $this->asDateTime($attributes[$key])
//            );
//        }

        return $attributes;
    }

    /**
     * @param array $fields
     * @param array $joins
     * @return Model|null|object|static
     */
    public function getItemByFields(array $fields)
    {
        return $this->getInitDbByFields($fields)->first();
    }

    /**
     * @param array $fields
     * @return \Illuminate\Support\Collection
     */
    public function getItemsByFields(array $fields)
    {
        return $this->getInitDbByFields($fields)->get();
    }

    /**
     * @param int $id
     * @param bool $as_array
     * @return DefaultEloquent|array|Model|null|object
     */
    public function getById($id)
    {
        return $this->getItemByFields([$this->getKeyName() => $id]);
    }

    /**
     * @param array $fields
     * @return mixed
     */
    public function deleteByFields(array $fields)
    {
        $db = static::query();

        foreach ($fields as $key => $value) {
            if(is_array($value)) {
                $db->whereIn($key, $value);
            } else {
                $db->where($key, '=', $this->quote($value));
            }
        }

        return $db->forceDelete();
    }
}