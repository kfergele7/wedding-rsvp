<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $site->title }} | Unavailable</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
<main class="site-shell flex min-h-screen items-center justify-center py-16">
    <section class="card-frame w-full max-w-2xl bg-white text-center">
        <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Wedding Website</p>
        <h1 class="mt-4 font-heading text-5xl">Site Unavailable</h1>
        <p class="mx-auto mt-5 max-w-xl text-wedding-muted">This wedding site is currently unavailable. Please check back later.</p>
    </section>
</main>
</body>
</html>
