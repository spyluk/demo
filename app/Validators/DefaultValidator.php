<?php
/**
 * 
 * User: sergei
 * Date: 07.08.18
 * Time: 16:43
 */

namespace App\Validators;

use App\GraphQL\Error\ValidationError;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;

class DefaultValidator implements ValidatorInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * DefaultValidator constructor.
     *
     * @param $data
     * @param array $rules
     */
    public function __construct($data, $rules = [], $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validator = Validator::make($data, $this->rules(), $messages);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function validated()
    {
        return $this->validator->validated();
    }

    /**
     * @param array|string $attribute
     * @param array|string $rules
     * @param callable $callback
     * @return $this
     */
    public function sometimes($attribute, $rules, callable $callback)
    {
        return $this->validator->sometimes($attribute, $rules, $callback);
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->validator->fails();
    }

    /**
     * @return array
     * @throws ValidationError
     */
    public function validate()
    {
        return $this->validator->validate();
    }

    /**
     * @return array
     */
    public function failed()
    {
        return $this->validator->failed();
    }

    /**
     * @param callable|string $callback
     * @return $this
     */
    public function after($callback)
    {
        return $this->validator->after($callback);
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->validator->errors();
    }

    /**
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag()
    {
        return $this->validator->getMessageBag();
    }

    /**
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function addMessage()
    {
        $this->validator->addFailure($attribute, $rule, $parameters = []);
        return $this->validator->getMessageBag();
    }

    public function __get($key)
    {
        $this->validator->$key;
    }
}