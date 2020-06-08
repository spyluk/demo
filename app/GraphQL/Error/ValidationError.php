<?php

namespace App\GraphQL\Error;

use App\Validators\DefaultValidator;
use Rebing\GraphQL\Error\ValidationError as ErrorBase;

class ValidationError extends ErrorBase
{
    /**
     * @var string
     */
    public $message = 'validation';

    /**
     * @var array
     */
    public $errors = [];

    /**
     * ValidationError constructor.
     *
     * @param string $errors
     * @param null $validator
     */
    public function __construct(
        $errors,
        $validator = null
    )
    {
        $this->errors = $errors;
        $validator = $validator ? $validator : new DefaultValidator([]);

        parent::__construct($this->message, $validator);
    }
}
