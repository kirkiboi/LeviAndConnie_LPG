@extends('layouts.app')
@section('title', 'Employees')
@section('header-title', 'Employee Management')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Employees</div>
        <div class="page-subtitle">Manage staff accounts, PINs, and salaries</div>
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Employee
    </button>
</div>

{{-- Summary cards --}}
<div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $employees->count() }}</div>
            <div class="stat-label">Total Employees</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $employees->where('isActive', true)->count() }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">₱{{ number_format($employees->where('isActive',true)->sum('daily_salary'), 2) }}</div>
            <div class="stat-label">Daily Wage Total</div>
        </div>
    </div>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Daily Salary</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
            <tr>
                <td class="td-muted">{{ $emp->id }}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:34px;height:34px;border-radius:50%;background:{{ $emp->isOwner() ? 'var(--accent)' : 'var(--info)' }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">
                            {{ strtoupper(substr($emp->firstName, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600; color:var(--text-primary);">{{ $emp->full_name }}</div>
                            @if($emp->id === session('employee_id'))
                                <div style="font-size:11px; color:var(--accent);">← You</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="td-muted">{{ $emp->phone ?? '—' }}</td>
                <td>
                    @if($emp->isOwner())
                        <span class="badge badge-orange" style="display:inline-flex; align-items:center; gap:5px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7z"/><path d="M3 20h18"/></svg>Owner</span>
                    @else
                        <span class="badge badge-info" style="display:inline-flex; align-items:center; gap:5px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Employee</span>
                    @endif
                </td>
                <td class="font-semibold">₱{{ number_format($emp->daily_salary, 2) }}</td>
                <td>
                    <span class="badge {{ $emp->isActive ? 'badge-success' : 'badge-muted' }}">
                        {{ $emp->isActive ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td style="text-align:right;">
                    <div class="flex gap-2" style="justify-content:flex-end;">
                        <button class="btn btn-ghost btn-sm" onclick='openEditModal(@json($emp))'>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </button>
                        @if($emp->id !== session('employee_id'))
                        <form action="{{ route('employees.toggle', $emp) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $emp->isActive ? 'btn-ghost' : 'btn-secondary' }}"
                                style="{{ $emp->isActive ? 'color:var(--danger);' : '' }}"
                                onclick="return confirm('{{ $emp->isActive ? 'Deactivate' : 'Activate' }} {{ $emp->full_name }}?')">
                                {{ $emp->isActive ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center td-muted" style="padding:48px;">
                    No employees yet. Add the first employee above.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── Add Employee Modal ── --}}
<div class="modal-overlay" id="add-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Add New Employee</div>
            <button class="modal-close" onclick="document.getElementById('add-modal').classList.remove('open')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="firstName" class="form-control" required placeholder="Juan">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="lastName" class="form-control" required placeholder="Dela Cruz">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middleName" class="form-control" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Daily Salary (₱) *</label>
                        <input type="number" name="daily_salary" class="form-control" step="0.01" min="0" value="0" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="09XX-XXX-XXXX">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-control" required>
                            <option value="employee">Employee</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('add-modal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Employee</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Employee Modal ── --}}
<div class="modal-overlay" id="edit-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Edit Employee</div>
            <button class="modal-close" onclick="document.getElementById('edit-modal').classList.remove('open')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="firstName" id="edit-firstName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="lastName" id="edit-lastName" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middleName" id="edit-middleName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Daily Salary (₱) *</label>
                        <input type="number" name="daily_salary" id="edit-salary" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" id="edit-phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <select name="role" id="edit-role" class="form-control" required>
                            <option value="employee">Employee</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-modal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddModal() {
    document.getElementById('add-modal').classList.add('open');
}

function openEditModal(emp) {
    document.getElementById('edit-form').action = `/employees/${emp.id}`;
    document.getElementById('edit-firstName').value  = emp.firstName;
    document.getElementById('edit-lastName').value   = emp.lastName;
    document.getElementById('edit-middleName').value = emp.middleName || '';
    document.getElementById('edit-phone').value      = emp.phone || '';
    document.getElementById('edit-role').value       = emp.role;
    document.getElementById('edit-salary').value     = emp.daily_salary;
    document.getElementById('edit-username').value   = emp.username;
    document.getElementById('edit-modal').classList.add('open');
}
</script>
@endpush