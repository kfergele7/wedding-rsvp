<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In | Wedding RSVP SaaS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell flex min-h-screen items-center justify-center py-16">
    <section class="card-frame w-full max-w-md bg-white">
        <div class="mb-4 flex justify-between text-xs uppercase tracking-[0.12em] text-wedding-muted">
            <a href="{{ route('marketing.home') }}">← Back to site</a>
            <a href="{{ route('register') }}">Create account</a>
        </div>

        <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Customer Login</p>
        <h1 class="mt-3 font-heading text-5xl">Sign In</h1>

        @if (session('status'))
            <p class="mt-4 rounded border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('status') }}</p>
        @endif

        <form class="mt-8 space-y-5" method="POST" action="{{ route('login') }}">
            @csrf

            <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
            </label>
            @error('email') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

            <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                Password
                <input type="password" name="password" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
            </label>
            @error('password') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

            <label class="flex items-center gap-2 text-sm text-wedding-muted">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 border-soft">
                Remember me
            </label>

            <button type="submit" class="button-dark w-full">Sign In</button>
        </form>

        <div class="mt-6 flex items-center justify-between text-sm">
            <a href="{{ route('password.request') }}" class="text-wedding-text underline">Forgot password?</a>
            <a href="{{ route('marketing.pricing') }}" class="text-wedding-text underline">View pricing</a>
        </div>
    </section>
</main>
</body>
</html>
