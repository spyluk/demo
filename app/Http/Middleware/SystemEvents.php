<?php

namespace App\Http\Middleware;

use App\Services\SystemEventService;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class SystemEvents extends Middleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param array ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        Event::listen('App\\Events*', function ($eventName, $data) {
            (new SystemEventService())->handle($eventName, $data[0], site('id'), user()->getCurrentProject('id'));
        });

        $response = $next($request);
        return $response;
    }
}
