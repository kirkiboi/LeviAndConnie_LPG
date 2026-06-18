<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('employee_id')) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }
        return $next($request);
    }
}
