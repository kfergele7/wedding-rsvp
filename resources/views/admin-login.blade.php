<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login | Wedding RSVP</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
    <main class="site-shell flex min-h-screen items-center justify-center py-20">
        <section class="card-frame w-full max-w-md bg-white">
            <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Wedding Admin</p>
            <h1 class="mt-3 font-heading text-5xl">Sign In</h1>

            @if (session('error'))
                <p class="mt-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
            @endif

            <form class="mt-8 space-y-4" method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <label class="block text-sm uppercase tracking-[0.15em] text-wedding-muted">
                    Admin Password
                    <input
                        type="password"
                        name="password"
                        class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base"
                        required
                        autofocus
                    >
                </label>

                @error('password')
                    <p class="text-sm text-red-700">{{ $message }}</p>
                @enderror

                <button type="submit" class="button-dark w-full transition hover:!border-gray-500 hover:!bg-gray-500 hover:!text-white">Enter Admin</button>
            </form>
        </section>
    </main>
</body>
</html>
