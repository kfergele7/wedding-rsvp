<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password | Wedding RSVP SaaS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell flex min-h-screen items-center justify-center py-16">
    <section class="card-frame w-full max-w-lg bg-white">
        <div class="mb-4 text-xs uppercase tracking-[0.12em] text-wedding-muted">
            <a href="{{ route('login') }}">← Back to login</a>
        </div>
        <h1 class="font-heading text-5xl">Reset password</h1>
        <p class="mt-4 text-wedding-muted">Enter your account email and we’ll send a secure reset link.</p>

        @if (session('status'))
            <p class="mt-4 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</p>
        @endif

        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.email') }}">
            @csrf
            <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                Email
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
            </label>
            @error('email') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

            <button type="submit" class="button-dark w-full">Send Reset Link</button>
        </form>
    </section>
</main>
</body>
</html>
