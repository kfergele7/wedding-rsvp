<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Staff Admin' }} | Wedding RSVP SaaS</title>
    @vite(['resources/css/app.css'])
    <style>
        .staff-shell { max-width: 1200px; margin: 0 auto; padding-left: 1rem; padding-right: 1rem; }
        .staff-top { border-bottom: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.92); }
        .staff-nav { position: sticky; top: 0; z-index: 40; border-top: 1px solid rgba(0,0,0,0.12); border-bottom: 1px solid rgba(0,0,0,0.12); background: rgba(255,255,255,0.95); backdrop-filter: blur(6px); }
        .staff-tab { border: 1px solid #22363a; background: #22363a; color: #fff; }
        .staff-tab:hover { background: #466369; border-color: #466369; color: #fff; }
        .staff-tab-active { background: #F2ECE3; color: #0f1b1d; border-color: #22363a; border-bottom-width: 2px; pointer-events: none; }
        .staff-logout { border: 1px solid #e66363 !important; background: #e66363 !important; color: #fff !important; }
        .staff-logout:hover { border-color: #b93f3f !important; background: #b93f3f !important; color: #fff !important; }
    </style>
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="pb-12">
    <header class="staff-top">
        <div class="staff-shell py-6">
            <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Platform Staff</p>
            <h1 class="font-heading text-4xl">{{ $title ?? 'Staff Admin' }}</h1>
        </div>
    </header>

    <div class="staff-nav">
        <div class="staff-shell flex flex-wrap items-center justify-between gap-3 py-3">
            <nav class="flex flex-wrap gap-2">
                <a href="{{ route('staff.dashboard') }}" class="staff-tab inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em] {{ ($activeTab ?? '') === 'dashboard' ? 'staff-tab-active' : '' }}">
                    <span class="material-symbols-outlined btn-icon">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('staff.accounts.index') }}" class="staff-tab inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em] {{ ($activeTab ?? '') === 'accounts' ? 'staff-tab-active' : '' }}">
                    <span class="material-symbols-outlined btn-icon">groups</span>
                    Accounts
                </a>
                <a href="{{ route('staff.templates.index') }}" class="staff-tab inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em] {{ ($activeTab ?? '') === 'templates' ? 'staff-tab-active' : '' }}">
                    <span class="material-symbols-outlined btn-icon">tune</span>
                    Template Management
                </a>
            </nav>

            <div class="flex items-center gap-2 border-l border-soft pl-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-btn staff-logout inline-flex items-center gap-2 px-4 py-3 text-xs uppercase tracking-[0.12em]">
                        <span class="material-symbols-outlined btn-icon">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="staff-shell py-10">
    @if (session('status'))
        <p class="mt-6 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @yield('content')
    </div>
</main>
</body>
</html>
