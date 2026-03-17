<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] ?? ($title ?? 'Wedding RSVP SaaS') }}</title>
    <meta name="description" content="{{ $meta['description'] ?? 'A beautiful wedding website and RSVP system you can set up in minutes.' }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['og_title'] ?? ($meta['title'] ?? ($title ?? 'Wedding RSVP SaaS')) }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? ($meta['description'] ?? '') }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? '/images/wedding/uploads/hero-image-20260224111916.jpeg' }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">

    @vite(['resources/css/app.css'])
    <style>
        .mk-container { max-width: 1180px; margin: 0 auto; padding-left: 1rem; padding-right: 1rem; }
        .mk-nav { position: sticky; top: 0; z-index: 60; border-bottom: 1px solid rgba(0,0,0,0.12); background: rgba(247,245,242,0.86); backdrop-filter: blur(6px); transition: backdrop-filter .2s ease, background .2s ease; }
        .mk-nav.is-scrolled { background: rgba(247,245,242,0.94); backdrop-filter: blur(10px); }
        .mk-nav-link { font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: #0f1b1d; opacity: .72; transition: opacity .2s ease, color .2s ease; }
        .mk-nav-link:hover { opacity: 1; color: #0f1b1d; }
        .mk-mobile-link { border: 1px solid rgba(0,0,0,0.12); background: #fff; padding: .85rem 1rem; font-size: 11px; text-transform: uppercase; letter-spacing: .16em; color: #0f1b1d; }
        .mk-mobile-link:hover { background: #f2ece3; }
    </style>
</head>
<body class="min-h-screen bg-wedding-bg text-wedding-black">
    <header id="mk-nav" class="mk-nav">
        <x-marketing.container class="flex items-center justify-between gap-4 py-4">
            <a href="{{ route('marketing.home') }}" class="inline-flex items-center">
                <img src="/images/brand/logo-dark.svg" alt="Wedding RSVP" class="h-8 w-auto md:h-9">
            </a>

            <nav class="hidden items-center gap-6 lg:flex">
                <a href="{{ route('marketing.features') }}" class="mk-nav-link">Features</a>
                <a href="{{ route('marketing.how') }}" class="mk-nav-link">How it works</a>
                <a href="{{ route('marketing.pricing') }}" class="mk-nav-link">Pricing</a>
                <a href="{{ route('marketing.faq') }}" class="mk-nav-link">FAQ</a>
            </nav>

            <div class="hidden items-center gap-2 lg:flex">
                <x-marketing.button href="{{ route('login') }}" variant="secondary" size="sm">Log in</x-marketing.button>
                <x-marketing.button href="{{ route('register') }}" variant="primary" size="sm">Start free</x-marketing.button>
            </div>

            <button id="mk-menu-open" class="inline-flex h-10 w-10 items-center justify-center border border-soft bg-white lg:hidden" aria-label="Open menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </x-marketing.container>
    </header>

    <div id="mk-mobile-menu" class="fixed inset-0 z-[90] hidden bg-black/45 lg:hidden">
        <div class="h-full w-full bg-wedding-bg">
            <x-marketing.container class="py-5">
                <div class="flex items-center justify-between border-b border-soft pb-4">
                    <img src="/images/brand/logo-dark.svg" alt="Wedding RSVP" class="h-8 w-auto">
                    <button id="mk-menu-close" class="inline-flex h-10 w-10 items-center justify-center border border-soft bg-white" aria-label="Close menu">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <nav class="mt-5 grid gap-2">
                    <a href="{{ route('marketing.features') }}" class="mk-mobile-link">Features</a>
                    <a href="{{ route('marketing.how') }}" class="mk-mobile-link">How it works</a>
                    <a href="{{ route('marketing.pricing') }}" class="mk-mobile-link">Pricing</a>
                    <a href="{{ route('marketing.faq') }}" class="mk-mobile-link">FAQ</a>
                    <a href="{{ route('login') }}" class="mk-mobile-link">Log in</a>
                    <a href="{{ route('register') }}" class="mk-mobile-link">Start free</a>
                </nav>
            </x-marketing.container>
        </div>
    </div>

    <main class="pb-20 pt-8 md:pt-10">
        @yield('content')
    </main>

    <footer class="border-t border-soft bg-white/75">
        <x-marketing.container class="grid gap-8 py-10 md:grid-cols-[1.1fr_0.9fr_0.9fr]">
            <div>
                <img src="/images/brand/logo-dark.svg" alt="Wedding RSVP" class="h-8 w-auto">
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-wedding-muted">
                    One elegant wedding website template with built-in RSVP operations for real guest management.
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">Product</p>
                <ul class="mt-4 space-y-2 text-sm text-wedding-black">
                    <li><a href="{{ route('marketing.features') }}" class="hover:underline">Features</a></li>
                    <li><a href="{{ route('marketing.how') }}" class="hover:underline">How it works</a></li>
                    <li><a href="{{ route('marketing.pricing') }}" class="hover:underline">Pricing</a></li>
                    <li><a href="{{ route('marketing.faq') }}" class="hover:underline">FAQ</a></li>
                </ul>
            </div>

            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">Support</p>
                <ul class="mt-4 space-y-2 text-sm text-wedding-black">
                    <li><a href="#" class="hover:underline">Terms</a></li>
                    <li><a href="#" class="hover:underline">Privacy</a></li>
                    <li><a href="mailto:support@example.com" class="hover:underline">support@example.com</a></li>
                </ul>
            </div>
        </x-marketing.container>
    </footer>

    <script>
        (() => {
            const nav = document.getElementById('mk-nav');
            const openBtn = document.getElementById('mk-menu-open');
            const closeBtn = document.getElementById('mk-menu-close');
            const menu = document.getElementById('mk-mobile-menu');

            const updateNav = () => {
                if (!nav) return;
                nav.classList.toggle('is-scrolled', window.scrollY > 8);
            };

            updateNav();
            window.addEventListener('scroll', updateNav, { passive: true });

            openBtn?.addEventListener('click', () => menu?.classList.remove('hidden'));
            closeBtn?.addEventListener('click', () => menu?.classList.add('hidden'));
            menu?.addEventListener('click', (event) => {
                if (event.target === menu) menu.classList.add('hidden');
            });
        })();
    </script>
</body>
</html>
