<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set New Password | Wedding RSVP SaaS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell flex min-h-screen items-center justify-center py-16">
    <section class="card-frame w-full max-w-lg bg-white">
        <h1 class="font-heading text-5xl">Set new password</h1>

        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                Email
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
            </label>
            @error('email') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

            <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                Password
                <input type="password" name="password" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
            </label>
            @error('password') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

            <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                Confirm Password
                <input type="password" name="password_confirmation" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
            </label>

            <button type="submit" class="button-dark w-full">Reset Password</button>
        </form>
    </section>
</main>
</body>
</html>
