<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email | Wedding RSVP SaaS</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell flex min-h-screen items-center justify-center py-16">
    <section class="card-frame w-full max-w-2xl bg-white">
        <h1 class="font-heading text-5xl">Verify your email</h1>
        <p class="mt-4 text-wedding-muted">Check your inbox for the verification link. Your account and wedding site are already created; verification unlocks dashboard access.</p>

        @if (session('status') === 'verification-link-sent')
            <p class="mt-4 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">A new verification email has been sent.</p>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="button-dark">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center border border-red-400 bg-white px-8 py-4 text-xs font-medium uppercase tracking-[0.2em] text-red-700 transition hover:bg-red-50">Logout</button>
            </form>
        </div>

        <p class="mt-5 text-sm text-wedding-muted">After verification: subscribe on your dashboard, then publish your site at your unique <code>/{slug}</code> URL.</p>
    </section>
</main>
</body>
</html>
