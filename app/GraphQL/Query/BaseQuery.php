<?php
/**
 * User: Sergei
 * Date: 31.05.19
 */

namespace App\GraphQL\Query;

use App\GraphQL\Traits\BaseTrait;
use Rebing\GraphQL\Support\Query;

abstract class BaseQuery extends Query
{
    use BaseTrait;


    /**
     *
     */
    const DEFAULT_PER_PAGE = 20;
    /**
     *
     */
    const MAX_PER_PAGE = 100;

    /**
     * @var array
     */
    protected $data;

}