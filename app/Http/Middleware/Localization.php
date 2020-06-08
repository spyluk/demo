<?php

namespace App\Http\Middleware;

use App\GraphQL\Traits\BaseTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\App;

class Localization extends Middleware
{
    use BaseTrait;

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param array ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        App::setLocale(
            $this->language('code')
        );

        return $next($request);
    }
}
