<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employee;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $employeeId = session('employee_id');
        if (!$employeeId) {
            return redirect()->route('login');
        }
        $employee = Employee::find($employeeId);
        if (!$employee || $employee->role !== 'owner') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Owner privileges required.');
        }
        return $next($request);
    }
}
