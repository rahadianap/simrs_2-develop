<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CheckApiTokenAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // foreach (getallheaders() as $name => $value) {
        //     echo "$name: $value\n";
        // }
        if (!isset(getallheaders()['X-cons-id'])) {
            return response()->json(['success' => false, 'message' => 'API KEY NOT FOUND'], 401);
        }

        if (getallheaders()['X-cons-id'] !== ENV('APP_TOKEN_ACCESS')) {
            return response()->json(['success' => false, 'message' => 'API KEY NOT VALID'], 401);
        }
        return $next($request);
    }
}
