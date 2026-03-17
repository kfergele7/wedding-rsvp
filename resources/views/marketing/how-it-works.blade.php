@extends('marketing.layout')

@section('content')
<x-marketing.container>
    <x-marketing.section :spaced="false" class="pt-4">
        <h1 class="font-heading text-5xl md:text-6xl">How it works</h1>
        <p class="mt-4 max-w-3xl text-wedding-muted">
            Build your wedding website and guest list for free, then publish when you are ready to collect RSVPs.
        </p>
    </x-marketing.section>

    <x-marketing.section class="mt-12" tone="white">
        <div class="grid gap-8 md:grid-cols-3">
            <x-marketing.card class="h-full">
                <span class="material-symbols-outlined text-wedding-band">edit_note</span>
                <p class="mt-3 text-xs uppercase tracking-[0.16em] text-wedding-muted">Step 1</p>
                <h2 class="mt-2 font-heading text-3xl">Create your site and guest list</h2>
                <p class="mt-3 text-sm leading-relaxed text-wedding-muted">
                    Add your couple details, timeline, venue information, FAQs, and menu content using simple forms.
                    Set up parties and guests in draft mode at no cost.
                </p>
            </x-marketing.card>

            <x-marketing.card class="h-full">
                <span class="material-symbols-outlined text-wedding-band">qr_code_2</span>
                <p class="mt-3 text-xs uppercase tracking-[0.16em] text-wedding-muted">Step 2</p>
                <h2 class="mt-2 font-heading text-3xl">Share QR and invite links</h2>
                <p class="mt-3 text-sm leading-relaxed text-wedding-muted">
                    Each party receives an invite code. Guests scan your QR code or open your website link,
                    enter their code, and RSVP without creating an account.
                </p>
            </x-marketing.card>

            <x-marketing.card class="h-full">
                <span class="material-symbols-outlined text-wedding-band">publish</span>
                <p class="mt-3 text-xs uppercase tracking-[0.16em] text-wedding-muted">Step 3</p>
                <h2 class="mt-2 font-heading text-3xl">Publish and collect RSVPs</h2>
                <p class="mt-3 text-sm leading-relaxed text-wedding-muted">
                    Subscribe when you are ready to go live. Track attendance, meals, dietary requirements,
                    and export everything as CSV for planning.
                </p>
            </x-marketing.card>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-12" tone="soft">
        <div class="grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div>
                <h2 class="font-heading text-4xl">Invite code and QR flow explained</h2>
                <p class="mt-4 text-sm leading-relaxed text-wedding-muted">
                    Your invitation includes a QR code and a short invite code for each party. Guests can RSVP on any device.
                    This keeps responses private and avoids guest account creation.
                </p>
                <ul class="mt-5 space-y-3 text-sm text-wedding-black">
                    <li class="inline-flex items-start gap-2">
                        <span class="material-symbols-outlined mt-[1px] text-[16px] text-wedding-success">check_circle</span>
                        <span>Code lookup works for older guests and non-technical relatives.</span>
                    </li>
                    <li class="inline-flex items-start gap-2">
                        <span class="material-symbols-outlined mt-[1px] text-[16px] text-wedding-success">check_circle</span>
                        <span>Manual RSVP entry lets you record phone call-ins instantly.</span>
                    </li>
                    <li class="inline-flex items-start gap-2">
                        <span class="material-symbols-outlined mt-[1px] text-[16px] text-wedding-success">check_circle</span>
                        <span>Household-level guest controls avoid duplicate or conflicting responses.</span>
                    </li>
                </ul>
            </div>

            <div class="overflow-hidden rounded-xl border border-soft bg-white p-3 shadow-soft">
                <img
                    loading="lazy"
                    src="/images/wedding/uploads/details-image-20260224121400.jpg"
                    alt="Invite code and RSVP workflow preview"
                    class="h-72 w-full rounded-lg object-cover"
                >
            </div>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-12 mb-10" tone="dark">
        <h2 class="font-heading text-4xl text-white">Start free and build at your own pace</h2>
        <p class="mt-4 max-w-3xl text-white/80">
            You can prepare your full site and guest operations before paying.
            Subscribe only when you are ready to publish and collect responses.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
            <x-marketing.button href="{{ route('register') }}" variant="light">Start free</x-marketing.button>
            <x-marketing.button href="{{ route('marketing.pricing') }}" variant="secondary">View pricing</x-marketing.button>
        </div>
    </x-marketing.section>
</x-marketing.container>
@endsection
