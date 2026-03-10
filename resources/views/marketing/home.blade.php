@extends('marketing.layout', ['title' => 'Wedding RSVP SaaS'])

@section('content')
<section class="card-frame bg-white">
    <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Wedding Website + RSVP Platform</p>
    <h1 class="mt-4 font-heading text-6xl leading-tight">Launch your wedding site and RSVP management in minutes.</h1>
    <p class="mt-4 max-w-3xl text-wedding-muted">Create a beautiful wedding page, manage guest lists and guests, collect RSVPs with invitation codes, and keep everything in one place.</p>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('register') }}" class="button-dark">Start Now</a>
        <a href="{{ route('marketing.pricing') }}" class="admin-btn border border-soft bg-white px-6 py-4 text-xs uppercase tracking-[0.14em]">See Pricing</a>
    </div>
</section>

<section class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <article class="card-frame bg-white">
        <h2 class="font-heading text-2xl">Custom Wedding Page</h2>
        <p class="mt-2 text-sm text-wedding-muted">Edit hero, timeline, story, venue details, travel info, FAQs, and RSVP CTA.</p>
    </article>
    <article class="card-frame bg-white">
        <h2 class="font-heading text-2xl">Guest + Party Manager</h2>
        <p class="mt-2 text-sm text-wedding-muted">Organize guest lists, guest names, max seats, notes, and unique RSVP codes.</p>
    </article>
    <article class="card-frame bg-white">
        <h2 class="font-heading text-2xl">RSVP Operations</h2>
        <p class="mt-2 text-sm text-wedding-muted">Track attending/not attending/no response, dietary info, and manual call-in RSVPs.</p>
    </article>
    <article class="card-frame bg-white">
        <h2 class="font-heading text-2xl">Billing + Publishing</h2>
        <p class="mt-2 text-sm text-wedding-muted">Subscription controls publication state. Manage billing anytime in Stripe portal.</p>
    </article>
</section>

<section class="mt-8 card-frame bg-white">
    <h2 class="font-heading text-4xl">How It Works</h2>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <article class="rounded border border-soft p-4">
            <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Step 1</p>
            <h3 class="mt-2 font-heading text-2xl">Create Account</h3>
            <p class="mt-2 text-sm text-wedding-muted">Sign up and verify your email. Your account and default wedding site are created automatically.</p>
        </article>
        <article class="rounded border border-soft p-4">
            <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Step 2</p>
            <h3 class="mt-2 font-heading text-2xl">Subscribe + Configure</h3>
            <p class="mt-2 text-sm text-wedding-muted">Choose the monthly plan, then edit content and upload your guest list.</p>
        </article>
        <article class="rounded border border-soft p-4">
            <p class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Step 3</p>
            <h3 class="mt-2 font-heading text-2xl">Publish + Collect RSVPs</h3>
            <p class="mt-2 text-sm text-wedding-muted">Go live at your `/w/{slug}` page and collect/manage RSVP responses in your dashboard.</p>
        </article>
    </div>
</section>

<section class="mt-8 card-frame bg-white">
    <h2 class="font-heading text-4xl">Simple Pricing</h2>
    <p class="mt-3 text-wedding-muted">{{ $plan['name'] }} · {{ $plan['price'] }}{{ $plan['interval'] }} · Cancel anytime.</p>
    <div class="mt-5">
        <a href="{{ route('marketing.pricing') }}" class="button-dark">View Plan Details</a>
    </div>
</section>
@endsection
