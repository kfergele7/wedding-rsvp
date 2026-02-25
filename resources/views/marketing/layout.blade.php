<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Wedding RSVP SaaS' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
    <header class="border-b border-soft bg-white/90">
        <div class="site-shell flex flex-wrap items-center justify-between gap-3 py-5">
            <a href="{{ route('marketing.home') }}" class="font-heading text-3xl">Wedding RSVP SaaS</a>
            <nav class="flex flex-wrap items-center gap-2">
                <a href="{{ route('marketing.features') }}" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em]"><span class="material-symbols-outlined btn-icon">grid_view</span>Features</a>
                <a href="{{ route('marketing.pricing') }}" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em]"><span class="material-symbols-outlined btn-icon">sell</span>Pricing</a>
                @auth
                    @if (auth()->user()->is_staff)
                        <a href="{{ route('staff.dashboard') }}" class="button-dark inline-flex items-center gap-2 px-4 py-2"><span class="material-symbols-outlined btn-icon">admin_panel_settings</span>Staff</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="button-dark inline-flex items-center gap-2 px-4 py-2"><span class="material-symbols-outlined btn-icon">dashboard</span>Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="admin-btn inline-flex items-center gap-2 border border-soft bg-white px-4 py-2 text-xs uppercase tracking-[0.12em]"><span class="material-symbols-outlined btn-icon">login</span>Login</a>
                    <a href="{{ route('register') }}" class="button-dark inline-flex items-center gap-2 px-4 py-2"><span class="material-symbols-outlined btn-icon">rocket_launch</span>Start Now</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="site-shell py-12 md:py-16">
        @yield('content')
    </main>

    <footer class="border-t border-soft bg-white/70">
        <div class="site-shell py-5 text-xs uppercase tracking-[0.12em] text-wedding-muted">
            Wedding RSVP SaaS · Cancel anytime
        </div>
    </footer>
</body>
</html>
