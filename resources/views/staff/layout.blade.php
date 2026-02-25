<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Staff Admin' }} | Wedding RSVP SaaS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell py-10 md:py-12">
    <header class="mb-8 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Platform Staff</p>
            <h1 class="mt-2 font-heading text-5xl">{{ $title ?? 'Staff Admin' }}</h1>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('staff.dashboard') }}" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em]"><span class="material-symbols-outlined btn-icon">dashboard</span>Dashboard</a>
            <a href="{{ route('staff.accounts.index') }}" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em]"><span class="material-symbols-outlined btn-icon">groups</span>Accounts</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button-dark inline-flex items-center gap-2 px-4 py-2"><span class="material-symbols-outlined btn-icon">logout</span>Logout</button>
            </form>
        </div>
    </header>

    @if (session('status'))
        <p class="mb-5 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
