<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSession;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (session('employee_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $employee = Employee::where('username', $request->username)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return back()
                ->with('error', 'Incorrect username or password. Please try again.')
                ->withInput(['username' => $request->username]);
        }

        if (!$employee->isActive) {
            return back()->with('error', 'Account is inactive. Please contact the owner.');
        }

        $now = Carbon::now('Asia/Manila');

        $todaySession = EmployeeSession::where('employee_id', $employee->id)
            ->where('date', $now->toDateString())
            ->first();

        if (!$todaySession) {
            EmployeeSession::create([
                'employee_id' => $employee->id,
                'date'        => $now->toDateString(),
                'time_in'     => $now->format('H:i:s'),
            ]);
        }

        session()->regenerate();
        session([
            'employee_id'   => $employee->id,
            'employee_name' => $employee->full_name,
            'employee_role' => $employee->role,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        session()->flush();
        session()->regenerate();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}