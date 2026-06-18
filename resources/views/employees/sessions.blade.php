@extends('layouts.app')
@section('title', 'Employee Attendance')
@section('header-title', 'Employee Attendance')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Employee Attendance</div>
        <div class="page-subtitle">Review login and logout times for staff</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M3 10h18"/><path d="M8 4v6"/><path d="M16 4v6"/></svg>
            All Sessions
        </div>
        <span class="text-muted text-sm">Showing recent employee logins and logouts</span>
    </div>
    <div class="table-wrapper" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours Worked</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr>
                    <td class="td-muted">{{ $session->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">
                                {{ strtoupper(substr($session->employee->firstName ?? 'E', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; color:var(--text-primary);">{{ $session->employee->full_name ?? 'Unknown' }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted);">{{ $session->employee->role ?? 'employee' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);">{{ \Carbon\Carbon::parse($session->date)->format('M d, Y') }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</div>
                    </td>
                    <td class="text-success font-semibold">{{ \Carbon\Carbon::parse($session->time_in)->format('h:i A') }}</td>
                    <td>
                        @if($session->time_out)
                            <span style="color:var(--text-primary);">{{ \Carbon\Carbon::parse($session->time_out)->format('h:i A') }}</span>
                        @else
                            <span class="badge badge-orange">Active</span>
                        @endif
                    </td>
                    <td>
                        @if($session->hours_worked !== null)
                            <span class="font-semibold">{{ $session->hours_worked }} hrs</span>
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($session->time_out)
                            <span class="badge badge-success">Completed</span>
                        @else
                            <span class="badge badge-orange">Active</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center td-muted" style="padding:48px;">
                        No session records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection