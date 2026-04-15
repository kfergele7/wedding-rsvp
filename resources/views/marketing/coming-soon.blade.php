<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $meta['title'] ?? 'Magic Invitation is coming soon' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? 'Magic Invitation is preparing to launch.' }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#F7F5F2] text-[#0F1B1D] antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
        <div class="absolute inset-x-0 top-0 h-2 bg-[#22363A]"></div>
        <div class="absolute -left-20 top-20 hidden h-72 w-72 rounded-full bg-[#F2ECE3] blur-3xl md:block"></div>
        <div class="absolute -right-20 bottom-20 hidden h-72 w-72 rounded-full bg-[#466369]/20 blur-3xl md:block"></div>

        <section class="relative mx-auto max-w-3xl border border-[#22363A]/15 bg-white px-8 py-12 text-center shadow-[0_24px_80px_rgba(15,27,29,0.10)] md:px-16 md:py-16">
            <p class="mb-5 text-xs font-semibold uppercase tracking-[0.35em] text-[#848484]">Magic Invitation</p>
            <h1 class="font-serif text-5xl leading-tight text-[#0F1B1D] md:text-7xl">Coming soon</h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-[#466369] md:text-xl">
                We are getting Magic Invitation ready for launch. Existing customers can still log in, manage their wedding, and published wedding websites remain available.
            </p>

            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center justify-center bg-[#22363A] px-7 py-4 text-sm font-semibold uppercase tracking-[0.22em] text-white shadow-[0_12px_28px_rgba(15,27,29,0.18)] transition hover:bg-[#466369]"
                >
                    Customer login
                </a>
                <a
                    href="mailto:hello@magicinvitation.com"
                    class="inline-flex items-center justify-center bg-[#F2ECE3] px-7 py-4 text-sm font-semibold uppercase tracking-[0.22em] text-[#0F1B1D] transition hover:bg-[#F7F7F7]"
                >
                    Contact us
                </a>
            </div>
        </section>
    </main>
</body>
</html>
