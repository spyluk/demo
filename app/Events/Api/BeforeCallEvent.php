<?php

namespace App\Events\Api;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use \Illuminate\Http\Request;

class BeforeCallEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var string
     */
    public $method;
    /**
     * @var array;
     */
    public $requestArray;

    /**
     * @param string $method
     * @param array $requestArray
     */
    public function __construct($method, $requestArray)
    {
        $this->method = $method;
        $this->request = $requestArray;
    }
}
