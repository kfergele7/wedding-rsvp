@extends('marketing.layout')

@section('content')
<x-marketing.container>
    <x-marketing.section :spaced="false" class="pt-4">
        <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Wedding website and RSVP platform</p>
                <h1 class="mt-4 font-heading text-4xl leading-tight md:text-6xl">
                    A beautiful wedding website and RSVP system you set up in minutes.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-wedding-muted">
                    One elegant template. Edit with simple forms. Subscribe only when you are ready to go live.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <x-marketing.button href="{{ route('register') }}" variant="primary" size="lg">Start free</x-marketing.button>
                    <x-marketing.button href="{{ url('/demo') }}" variant="secondary" size="lg">View demo</x-marketing.button>
                </div>
                <p class="mt-4 text-sm text-wedding-muted">No card required to start. Cancel anytime.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-soft bg-white p-3 shadow-soft">
                <img
                    src="/images/wedding/uploads/hero-image-20260224111916.jpeg"
                    alt="Wedding website preview"
                    class="h-[330px] w-full rounded-xl object-cover md:h-[420px]"
                    fetchpriority="high"
                >
                <div class="mt-3 grid grid-cols-3 gap-2">
                    <div class="rounded border border-soft bg-wedding-off-white p-3 text-[11px] uppercase tracking-[0.12em] text-wedding-muted">Template</div>
                    <div class="rounded border border-soft bg-wedding-off-white p-3 text-[11px] uppercase tracking-[0.12em] text-wedding-muted">RSVP Codes</div>
                    <div class="rounded border border-soft bg-wedding-off-white p-3 text-[11px] uppercase tracking-[0.12em] text-wedding-muted">Guest Lists</div>
                </div>
            </div>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-14" tone="white">
        <div class="grid gap-4 md:grid-cols-3">
            <x-marketing.testimonial-card
                quote="Our families found it incredibly easy to RSVP with codes. We were set up in one evening."
                name="Isla & Nathan"
                role="Bride & Groom"
            />
            <x-marketing.testimonial-card
                quote="The manual RSVP updates for call-ins made guest support far easier than spreadsheets."
                name="Harriet Cole"
                role="Wedding Planner"
            />
            <x-marketing.testimonial-card
                quote="The single-template setup kept design decisions simple and still looked premium."
                name="Leah & Marcus"
                role="Bride & Groom"
            />
        </div>
        <div class="mt-8 grid gap-4 border-t border-soft pt-8 text-center md:grid-cols-3">
            <div>
                <p class="font-heading text-4xl">10 mins</p>
                <p class="mt-2 text-xs uppercase tracking-[0.14em] text-wedding-muted">Typical setup time</p>
            </div>
            <div>
                <p class="font-heading text-4xl">Invite-code-only</p>
                <p class="mt-2 text-xs uppercase tracking-[0.14em] text-wedding-muted">Private RSVP flow</p>
            </div>
            <div>
                <p class="font-heading text-4xl">CSV ready</p>
                <p class="mt-2 text-xs uppercase tracking-[0.14em] text-wedding-muted">Export guest data anytime</p>
            </div>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-16" tone="soft">
        <h2 class="font-heading text-4xl">How it works</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-3">
            <x-marketing.card class="h-full">
                <span class="material-symbols-outlined text-wedding-band">edit_note</span>
                <p class="mt-3 text-xs uppercase tracking-[0.16em] text-wedding-muted">Step 1</p>
                <h3 class="mt-1 font-heading text-2xl">Create your site and guest list</h3>
                <p class="mt-2 text-sm text-wedding-muted">Build everything in draft mode for free, including parties and invite codes.</p>
            </x-marketing.card>
            <x-marketing.card class="h-full">
                <span class="material-symbols-outlined text-wedding-band">qr_code_2</span>
                <p class="mt-3 text-xs uppercase tracking-[0.16em] text-wedding-muted">Step 2</p>
                <h3 class="mt-1 font-heading text-2xl">Share QR and invite links</h3>
                <p class="mt-2 text-sm text-wedding-muted">Guests RSVP privately with their code. No accounts required for guests.</p>
            </x-marketing.card>
            <x-marketing.card class="h-full">
                <span class="material-symbols-outlined text-wedding-band">publish</span>
                <p class="mt-3 text-xs uppercase tracking-[0.16em] text-wedding-muted">Step 3</p>
                <h3 class="mt-1 font-heading text-2xl">Publish and collect RSVPs</h3>
                <p class="mt-2 text-sm text-wedding-muted">Subscribe when ready, go live, track responses, and export for planning.</p>
            </x-marketing.card>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-16">
        <h2 class="font-heading text-4xl">Feature highlights</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <x-marketing.feature-card icon="web" title="Form-based editor" description="Update content with simple fields while keeping the design consistently elegant." />
            <x-marketing.feature-card icon="key" title="QR + invite code RSVP" description="Privacy-first RSVP flow designed for all guests, including older relatives." />
            <x-marketing.feature-card icon="group" title="Household guest lists" description="Manage parties, seats, and named guests from one clear admin view." />
            <x-marketing.feature-card icon="restaurant" title="Meals and dietary needs" description="Capture meal choices and dietary requirements with RSVP responses." />
            <x-marketing.feature-card icon="support_agent" title="Manual RSVP entry" description="Record responses on behalf of guests who prefer phone or offline support." />
            <x-marketing.feature-card icon="download" title="CSV exports" description="Export guest and RSVP data for venue planning and seating workflows." />
            <x-marketing.feature-card icon="shield_locked" title="Privacy-first controls" description="Tenant-scoped data and invite-code lookups keep responses protected." />
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-16" tone="white">
        <h2 class="font-heading text-4xl">One premium template, designed so you cannot break it</h2>
        <p class="mt-4 max-w-3xl text-wedding-muted">
            You are not building from scratch. You are filling a carefully designed template that always stays refined across mobile and desktop.
        </p>
        <div class="mt-8 grid gap-4 md:grid-cols-3">
            <div class="overflow-hidden rounded-xl border border-soft bg-white p-3">
                <img loading="lazy" src="/images/wedding/uploads/hero-image-20260224111916.jpeg" alt="Template hero example" class="h-56 w-full rounded-lg object-cover">
                <p class="mt-3 text-xs uppercase tracking-[0.14em] text-wedding-muted">Hero section</p>
            </div>
            <div class="overflow-hidden rounded-xl border border-soft bg-white p-3">
                <img loading="lazy" src="/images/wedding/uploads/welcome-image-20260224121344.jpg" alt="Timeline section example" class="h-56 w-full rounded-lg object-cover">
                <p class="mt-3 text-xs uppercase tracking-[0.14em] text-wedding-muted">Timeline band</p>
            </div>
            <div class="overflow-hidden rounded-xl border border-soft bg-white p-3">
                <img loading="lazy" src="/images/wedding/uploads/details-image-20260224121400.jpg" alt="Details card example" class="h-56 w-full rounded-lg object-cover">
                <p class="mt-3 text-xs uppercase tracking-[0.14em] text-wedding-muted">Details cards</p>
            </div>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-16" tone="dark">
        <h2 class="font-heading text-4xl text-white">Free to build. Subscribe when you are ready to go live.</h2>
        <p class="mt-4 max-w-3xl text-white/80">Set up your website, guest list, and RSVP settings before paying. Publish only when you are ready.</p>
        <div class="mt-7">
            <x-marketing.button href="{{ route('marketing.pricing') }}" variant="light">View pricing</x-marketing.button>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-16">
        <h2 class="font-heading text-4xl">Frequently asked questions</h2>
        <x-marketing.faq-accordion class="mt-8" :items="[
            ['q' => 'Is it free to start?', 'a' => 'Yes. You can build your site, add guests, and preview everything in draft mode for free.'],
            ['q' => 'What happens if I cancel?', 'a' => 'Your site stays live until the end of your current billing period, then returns to non-public mode.'],
            ['q' => 'Do guests need accounts?', 'a' => 'No. Guests RSVP with an invite code and do not need to create accounts.'],
            ['q' => 'Can I RSVP for someone manually?', 'a' => 'Yes. You can update RSVP records on behalf of guests who call or need support.'],
            ['q' => 'Can I export my guest list?', 'a' => 'Yes. CSV exports are available for guest and RSVP data.'],
            ['q' => 'Can I hide the site until I am ready?', 'a' => 'Yes. Keep it in draft mode and preview privately before publishing.'],
            ['q' => 'Can I change my URL?', 'a' => 'Support can help with URL changes if needed.'],
        ]" />
    </x-marketing.section>

    <x-marketing.section class="mt-16 mb-10" tone="white">
        <div class="grid gap-6 md:grid-cols-[1.2fr_0.8fr] md:items-center">
            <div>
                <h2 class="font-heading text-5xl">Ready to launch your wedding website?</h2>
                <p class="mt-4 max-w-xl text-wedding-muted">You can build everything before paying.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <x-marketing.button href="{{ route('register') }}" variant="primary" size="lg">Start free</x-marketing.button>
                    <x-marketing.button href="{{ route('marketing.pricing') }}" variant="secondary" size="lg">View pricing</x-marketing.button>
                </div>
            </div>
            <div class="overflow-hidden rounded-xl border border-soft bg-wedding-off-white p-2">
                <img loading="lazy" src="/images/wedding/uploads/story-image-20260224121353.jpg" alt="Happy couple" class="h-64 w-full rounded-lg object-cover">
            </div>
        </div>
    </x-marketing.section>
</x-marketing.container>
@endsection
