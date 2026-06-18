@extends('layouts.app')
@section('title', 'Profile')
@section('header-title', 'My Profile')

@section('content')
<div style="display:grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start;">

    {{-- ── Profile card ── --}}
    <div>
        <div class="card">
            <div class="card-body" style="text-align:center; padding:30px 20px;">
                <div style="width:80px;height:80px;border-radius:50%;background:{{ $employee->isOwner() ? 'var(--accent)' : 'var(--info)' }};display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:800;color:#fff;margin:0 auto 16px;box-shadow:{{ $employee->isOwner() ? 'var(--accent-glow)' : 'none' }}">
                    {{ strtoupper(substr($employee->firstName, 0, 1)) }}
                </div>
                <div style="font-size:20px; font-weight:800; color:var(--text-primary); margin-bottom:4px;">
                    {{ $employee->full_name }}
                </div>
                @if($employee->isOwner())
                    <span class="badge badge-orange" style="font-size:13px; padding:6px 14px; display:inline-flex; align-items:center; gap:5px;"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7z"/><path d="M3 20h18"/>Owner</span>
                @else
                    <span class="badge badge-info" style="font-size:13px; padding:6px 14px; display:inline-flex; align-items:center; gap:5px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Employee</span>
                @endif
            </div>

            <div class="divider" style="margin:0;"></div>

            <div class="card-body">
                @php
                    $todaySession = $sessions->first();
                @endphp

                <div style="display:flex;flex-direction:column;gap:12px; font-size:13.5px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-muted">Phone</span>
                        <span style="color:var(--text-primary);">{{ $employee->phone ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-muted">Daily Salary</span>
                        <span style="color:var(--success); font-weight:700;">₱{{ number_format($employee->daily_salary, 2) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="text-muted">Status</span>
                        <span class="badge badge-success">Active</span>
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Today's session --}}
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">Today's Session</div>

                @if($todaySession && $todaySession->date == now()->toDateString())
                    <div style="background:var(--bg-input); border:1px solid var(--border); border-radius:var(--radius-sm); padding:14px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px;">
                            <span class="text-muted">Time In</span>
                            <span class="text-success font-semibold">{{ \Carbon\Carbon::createFromFormat('H:i:s', $todaySession->time_in, 'Asia/Manila')->format('h:i A') }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px;">
                            <span class="text-muted">Time Out</span>
                            @if($todaySession->time_out)
                                <span style="color:var(--text-primary);">{{ \Carbon\Carbon::createFromFormat('H:i:s', $todaySession->time_out, 'Asia/Manila')->format('h:i A') }}</span>
                            @else
                                <span class="badge badge-orange" style="font-size:11px;">Still Active</span>
                            @endif
                        </div>
                        @if(!$todaySession->time_out && session('employee_role') !== 'owner')
                            <form method="POST" action="{{ route('session.timeout') }}" style="margin-top: 16px; text-align:center;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-block" style="max-width:240px; margin: 0 auto;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    Time Out Now
                                </button>
                            </form>
                        @endif
                        @if($todaySession->hours_worked)
                            <div style="text-align:center; margin-top:10px; padding-top:10px; border-top:1px solid var(--border);">
                                <div style="font-size:22px; font-weight:800; color:var(--accent);">{{ $todaySession->hours_worked }}h</div>
                                <div style="font-size:11px; color:var(--text-muted);">Hours Worked</div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center td-muted" style="padding:16px; font-size:13px;">
                        No session recorded today.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Session history ── --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Work Session History
                </div>
                <span class="text-muted text-sm">Last 15 sessions</span>
            </div>
            <div class="table-wrapper" style="border:none; border-radius:0;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours Worked</th>
                            <th>Day's Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td>
                                <div style="font-weight:600; color:var(--text-primary);">{{ \Carbon\Carbon::parse($session->date)->format('M d, Y') }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</div>
                            </td>
                            <td class="text-success font-semibold">{{ \Carbon\Carbon::createFromFormat('H:i:s', $session->time_in, 'Asia/Manila')->format('h:i A') }}</td>
                            <td>
                                @if($session->time_out)
                                    <span style="color:var(--text-primary);">{{ \Carbon\Carbon::createFromFormat('H:i:s', $session->time_out, 'Asia/Manila')->format('h:i A') }}</span>
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
                            <td class="font-semibold" style="color:var(--success);">
                                ₱{{ number_format($employee->daily_salary, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center td-muted" style="padding:32px;">
                                No session history found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection