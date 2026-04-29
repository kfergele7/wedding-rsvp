<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $meta['title'] ?? 'Magic Invitation is coming soon' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? 'Magic Invitation is preparing to launch.' }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col bg-[#F7F5F2] text-[#0F1B1D] antialiased">
    <main class="relative flex flex-1 items-center justify-center overflow-hidden px-6 py-16">
        <div class="absolute inset-x-0 top-0 h-2 bg-[#22363A]"></div>
        <div class="absolute -left-20 top-20 hidden h-72 w-72 rounded-full bg-[#F2ECE3] blur-3xl md:block"></div>
        <div class="absolute -right-20 bottom-20 hidden h-72 w-72 rounded-full bg-[#466369]/20 blur-3xl md:block"></div>

        <section class="relative mx-auto w-full max-w-3xl border border-[#22363A]/15 bg-white px-8 py-12 text-center shadow-[0_24px_80px_rgba(15,27,29,0.10)] md:px-16 md:py-16">
            <img src="/images/brand/logo-dark.svg" alt="Magic Invitation" class="mx-auto h-10 w-auto">
            <p class="mt-8 text-xs font-semibold uppercase tracking-[0.35em] text-[#848484]">Launching soon</p>
            <h1 class="mx-auto mt-5 max-w-2xl font-heading text-4xl leading-tight text-[#0F1B1D] md:text-6xl">
                We are getting Magic Invitation ready for launch.
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-[#466369]">
                If you wish to stay up to date with our launch then please, sign up to our mailing list.
            </p>

            <form method="POST" action="{{ route('newsletter.signup') }}" class="mx-auto mt-8 max-w-xl">
                @csrf
                <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                    <label class="sr-only" for="newsletter-email">Email address</label>
                    <input
                        id="newsletter-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="Email address"
                        class="h-14 w-full border border-[#22363A]/20 bg-[#F7F7F7] px-4 text-base text-[#0F1B1D] outline-none transition focus:border-[#22363A] focus:bg-white"
                    >
                    <button
                        type="submit"
                        class="inline-flex h-14 items-center justify-center bg-[#22363A] px-6 text-xs font-semibold uppercase tracking-[0.2em] text-white shadow-[0_12px_28px_rgba(15,27,29,0.18)] transition hover:bg-[#466369]"
                    >
                        Sign up
                    </button>
                </div>

                @if (session('newsletter_status'))
                    <p class="mt-4 border border-[#21C177]/30 bg-[#21C177]/10 px-4 py-3 text-sm text-[#1aa267]">
                        {{ session('newsletter_status') }}
                    </p>
                @endif

                @error('email')
                    <p class="mt-4 border border-[#E66363]/30 bg-[#E66363]/10 px-4 py-3 text-sm text-[#B93F3F]">
                        {{ $message }}
                    </p>
                @enderror
            </form>

            <div class="mt-10 border-t border-[#22363A]/10 pt-8">
                <p class="text-sm text-[#848484]">Feel free to follow us on social media.</p>
                <div class="mt-4 flex items-center justify-center gap-3">
                    <a
                        href="https://www.facebook.com/profile.php?id=61568796141730"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Follow Magic Invitation on Facebook"
                        class="inline-flex h-11 w-11 items-center justify-center bg-[#22363A] text-white transition hover:bg-[#466369]"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                            <path d="M14.2 8.1V6.7c0-.7.2-1.1 1.2-1.1h1.5V3.1c-.7-.1-1.5-.1-2.2-.1-2.2 0-3.8 1.4-3.8 3.9v1.2H8.4v2.8h2.5V21h3.1V10.9h2.5l.4-2.8h-2.7z"/>
                        </svg>
                    </a>
                    <a
                        href="https://www.instagram.com/_magicinvitation_/"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Follow Magic Invitation on Instagram"
                        class="inline-flex h-11 w-11 items-center justify-center bg-[#22363A] text-white transition hover:bg-[#466369]"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="4" y="4" width="16" height="16" rx="4"></rect>
                            <circle cx="12" cy="12" r="3.4"></circle>
                            <circle cx="17.2" cy="6.8" r="0.8" fill="currentColor" stroke="none"></circle>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('login') }}" class="text-xs font-semibold uppercase tracking-[0.18em] text-[#22363A] underline underline-offset-4 transition hover:text-[#466369]">
                    Customer login
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-[#0F1B1D] py-[5px] text-white/85">
        <div class="mx-auto flex w-full max-w-[1180px] flex-col gap-1 px-4 text-[10px] uppercase tracking-[0.12em] sm:flex-row sm:items-center sm:justify-between">
            <span>&copy; Copyright Magic Invitation {{ now()->year }}</span>
            <span>
                Built by
                <a href="https://elementseven.co/" target="_blank" rel="noopener noreferrer" class="ml-1 underline-offset-2 hover:underline">
                    Element Seven
                </a>
            </span>
        </div>
    </footer>
</body>
</html>
