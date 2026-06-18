<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSession;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('lastName')->get();
        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName'    => 'required|string|max:100',
            'middleName'   => 'nullable|string|max:100',
            'lastName'     => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'username'     => 'required|string|unique:employees,username',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:owner,employee',
            'daily_salary' => 'required|numeric|min:0',
        ]);

        Employee::create([
            'firstName'    => $request->firstName,
            'middleName'   => $request->middleName,
            'lastName'     => $request->lastName,
            'phone'        => $request->phone,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'daily_salary' => $request->daily_salary,
            'isActive'     => true,
        ]);

        return back()->with('success', 'Employee added successfully.');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'firstName'    => 'required|string|max:100',
            'middleName'   => 'nullable|string|max:100',
            'lastName'     => 'required|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'username'     => 'required|string|unique:employees,username,' . $employee->id,
            'password'     => 'nullable|string|min:6',
            'role'         => 'required|in:owner,employee',
            'daily_salary' => 'required|numeric|min:0',
        ]);

        $data = [
            'firstName'    => $request->firstName,
            'middleName'   => $request->middleName,
            'lastName'     => $request->lastName,
            'phone'        => $request->phone,
            'username'     => $request->username,
            'role'         => $request->role,
            'daily_salary' => $request->daily_salary,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);
        return back()->with('success', 'Employee updated successfully.');
    }

    public function toggleActive(Employee $employee)
    {
        if ($employee->id === session('employee_id')) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }
        $employee->update(['isActive' => !$employee->isActive]);
        $status = $employee->isActive ? 'deactivated' : 'activated';
        return back()->with('success', "Employee {$status}.");
    }

    public function profile()
    {
        $employee = Employee::find(session('employee_id'));
        $sessions = EmployeeSession::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->take(15)
            ->get();
        return view('employees.profile', compact('employee', 'sessions'));
    }

    public function timeout(Request $request)
    {
        $employeeId = session('employee_id');
        $now = Carbon::now('Asia/Manila');

        $session = EmployeeSession::where('employee_id', $employeeId)
            ->where('date', $now->toDateString())
            ->whereNull('time_out')
            ->latest('time_in')
            ->first();

        if (!$session) {
            return back()->with('error', 'No active session found to time out.');
        }

        $session->update(['time_out' => $now->format('H:i:s')]);

        session()->flush();
        session()->regenerate();

        return redirect()->route('login')->with('success', 'Your shift has ended. You have been timed out and logged out successfully.');
    }

    public function sessions()
    {
        $sessions = EmployeeSession::with('employee')
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->get();

        return view('employees.sessions', compact('sessions'));
    }
}