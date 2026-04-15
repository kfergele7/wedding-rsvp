<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RSVP | Wedding RSVP</title>
    @include('partials.favicons')
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-wedding-bg">
    <main class="site-shell flex min-h-screen items-center justify-center py-20">
        <section class="card-frame w-full max-w-2xl text-center">
            <p class="text-xs uppercase tracking-[0.22em] text-wedding-muted">Phase 2</p>
            <h1 class="mt-4 font-heading text-5xl">RSVP Opens Soon</h1>
            <p class="mx-auto mt-6 max-w-lg leading-relaxed text-wedding-muted">
                The code-based RSVP experience is next and will be available shortly. Please return for wedding details.
            </p>
            <a href="/" class="button-dark mt-8">Back</a>
        </section>
    </main>
</body>
</html>
