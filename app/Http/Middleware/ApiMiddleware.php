<?php

namespace App\Http\Middleware;

use App\ResponseData;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::guard('api')->check()){
            auth()->setUser(Auth::guard('api')->user());
            return $next($request);
        }

        $responseData = new ResponseData();

        return $responseData->create(
            'Unathorized',
            [
                'data' => [
                    auth()->check(),
                    auth()->user()
                ],
            ],
            status: 'warning',
            status_code: 401
        );
    }
}
