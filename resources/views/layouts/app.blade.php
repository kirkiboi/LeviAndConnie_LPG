<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-wrapper">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div class="sidebar-brand-text">
                <div class="sidebar-brand-name">L&C Smart Retail</div>
                <div class="sidebar-brand-sub">LPG Center System</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                Point of Sale
            </a>

            <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                Inventory
            </a>

            @if(session('employee_role') === 'owner')
            <div class="nav-section-label" style="margin-top:10px;">Owner Only</div>

            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                Menu & Pricing
            </a>

            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Employees
            </a>

            <a href="{{ route('employees.sessions') }}" class="nav-link {{ request()->routeIs('employees.sessions') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    <path d="M8 7h8M8 12h8M8 17h5"/>
                </svg>
                Attendance
            </a>

            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/>
                </svg>
                Sales Reports
            </a>
            @endif

            <div class="nav-section-label" style="margin-top:10px;">Account</div>

            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                My Profile
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-ghost btn-block" style="justify-content:flex-start;gap:10px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="main-content">
        {{-- Top header --}}
        <header class="top-header">
            <span class="header-title">@yield('header-title', 'Dashboard')</span>
            <div class="header-right">
                <div class="time-badge" id="live-clock">--:-- --</div>
                <div class="employee-badge">
                    <div class="employee-avatar">{{ strtoupper(substr(session('employee_name', 'U'), 0, 1)) }}</div>
                    <div class="employee-info">
                        <div class="employee-name">{{ session('employee_name', 'Employee') }}</div>
                        <div class="employee-role">{{ session('employee_role', 'employee') }}</div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div style="padding: 0 24px; margin-top: 16px;">
            @if(session('success'))
                <div class="alert alert-success" id="flash-alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                    <button class="alert-close" onclick="this.closest('.alert').remove()">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" id="flash-alert">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                    <button class="alert-close" onclick="this.closest('.alert').remove()">✕</button>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>

<script>
// Live clock
function updateClock() {
    const now = new Date();
    const h = now.getHours().toString().padStart(2,'0');
    const m = now.getMinutes().toString().padStart(2,'0');
    const s = now.getSeconds().toString().padStart(2,'0');
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12 = (h % 12 || 12).toString().padStart(2,'0');
    document.getElementById('live-clock').textContent = `${h12}:${m}:${s} ${ampm}`;
}
setInterval(updateClock, 1000);
updateClock();

// Auto-logout after 30 min inactivity
let inactivityTimer;
const INACTIVE_MS = 30 * 60 * 1000; // 30 minutes

function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        document.getElementById('auto-logout-form').submit();
    }, INACTIVE_MS);
}

['mousemove','keydown','click','scroll','touchstart'].forEach(e =>
    document.addEventListener(e, resetTimer, { passive: true })
);
resetTimer();

// Auto-dismiss flash messages
const flashAlert = document.getElementById('flash-alert');
if (flashAlert) {
    setTimeout(() => {
        flashAlert.style.transition = 'opacity 0.5s';
        flashAlert.style.opacity = '0';
        setTimeout(() => flashAlert.remove(), 500);
    }, 4500);
}
</script>

{{-- Hidden auto-logout form --}}
<form id="auto-logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

@stack('scripts')
</body>
</html>
