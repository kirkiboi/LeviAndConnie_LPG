<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-page">
    <div class="login-bg-blob login-bg-blob-1"></div>
    <div class="login-bg-blob login-bg-blob-2"></div>

    <div class="login-card">
        <div class="login-logo">
            <div class="login-title">L&C Smart Retail</div>
            <div class="login-subtitle">LPG Center Management System</div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success mb-4">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST" id="login-form">
            @csrf

            {{-- Username input --}}
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control"
                    placeholder="Enter your username" value="{{ old('username') }}"
                    autocomplete="username" required>
            </div>

            {{-- Password input --}}
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Enter your password"
                    autocomplete="current-password" required>
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary btn-block btn-lg" id="submit-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Log In
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>