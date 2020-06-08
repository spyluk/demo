<?php

namespace App\Http\Middleware;

use GraphQL\Upload\UploadMiddleware;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\DB;

class Upload extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
//        print_r($_FILES);
//        print_R($request->allFiles());exit;
//        $request['last_name'] = 'dsfsdf';
//        print_r($request->post());
//        exit('11');
//        print $request['first_name'];
//        DB::connection()->enableQueryLog();


        $response = $next($request);
//        print_r(DB::getQueryLog());exit;
//$request->allFiles()
//        $uploadMiddleware = new UploadMiddleware();
//        $request = $uploadMiddleware->processRequest($request);

        return $response;
    }
}
