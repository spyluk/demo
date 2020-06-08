<?php

namespace App\Http\Middleware;

use GraphQL\Upload\UploadMiddleware;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\DB;

class Debug extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        //не подкючен, для примера
        DB::connection()->enableQueryLog();
        $response = $next($request);
        print_r(DB::getQueryLog());exit;

        return $response;
    }
}
