<?php

namespace App\Http\Middleware;

use App\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ini enaknya langsung pentalin ke landing - page atau ke login?
        // sementara ke landing - page langsung aja ya

        if(auth()->check()){
            $user = auth()->user();
            if($user->role == UserRole::OWNER && $user->role == UserRole::ADMIN){
                return $next($request);
            }
        }

        return redirect()->route('user.home');

    }
}
