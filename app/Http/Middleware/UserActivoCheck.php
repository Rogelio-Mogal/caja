<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;

class UserActivoCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->activo == 1) {
            return $next($request);
        }
    
        Auth::guard('web')->logout();
     
        return redirect('/login')->with('activo', 'Tu cuenta está suspendida.');
        //return $next($request);
    }
}
