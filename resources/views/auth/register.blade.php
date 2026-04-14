<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account | Wedding RSVP SaaS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell py-12 md:py-16">
    <div class="mb-6 flex justify-between text-xs uppercase tracking-[0.12em] text-wedding-muted">
        <a href="{{ route('marketing.home') }}">← Back to site</a>
        <a href="{{ route('marketing.pricing') }}">Pricing</a>
    </div>

    <section class="mx-auto grid max-w-5xl gap-6 md:grid-cols-[1.2fr_0.8fr]">
        <article class="card-frame bg-white">
            <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Start Free in Draft Mode</p>
            <h1 class="mt-3 font-heading text-5xl">Create your wedding account</h1>
            <p class="mt-3 text-wedding-muted">Create your account to get started. You will be signed in immediately, then verify your email before publishing.</p>

            <form class="mt-8 space-y-5" method="POST" action="{{ route('register') }}">
                @csrf

                <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                    Full Name
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
                </label>
                @error('name') <p class="text-sm text-red-700">{{ $message }}</p> @enderror

                <label class="block text-sm uppercase tracking-[0.15em] text-wedding-text">
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full border border-soft bg-white px-4 py-3 text-base">
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

                <button type="submit" class="button-dark w-full">Create Account</button>
            </form>

            <p class="mt-6 text-sm text-wedding-muted">
                Already registered?
                <a href="{{ route('login') }}" class="font-medium text-wedding-text underline">Sign in</a>
            </p>
        </article>

        <aside class="card-frame bg-white">
            <h2 class="font-heading text-3xl">What happens next</h2>
            <ol class="mt-4 space-y-3 text-sm text-wedding-muted">
                <li>1. Create account and sign in</li>
                <li>2. Verify your email</li>
                <li>3. Subscribe monthly</li>
                <li>4. Publish your unique /{slug} site</li>
            </ol>
        </aside>
    </section>
</main>
</body>
</html>
