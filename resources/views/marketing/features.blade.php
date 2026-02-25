@extends('marketing.layout', ['title' => 'Features'])

@section('content')
<section class="card-frame bg-white">
    <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Features</p>
    <h1 class="mt-4 font-heading text-6xl">Everything centered on wedding operations.</h1>
</section>

<section class="mt-8 grid gap-4 md:grid-cols-2">
    <article class="card-frame bg-white">
        <h2 class="font-heading text-3xl">Wedding Content CMS</h2>
        <p class="mt-2 text-wedding-muted">Manage names, dates, timeline, story, venue details, travel info, menu details, and FAQs from one admin area.</p>
    </article>
    <article class="card-frame bg-white">
        <h2 class="font-heading text-3xl">Households + RSVP Codes</h2>
        <p class="mt-2 text-wedding-muted">Generate and manage invitation codes per household. Guests can RSVP without account sign-up.</p>
    </article>
    <article class="card-frame bg-white">
        <h2 class="font-heading text-3xl">Support-Friendly RSVP Tools</h2>
        <p class="mt-2 text-wedding-muted">Update RSVPs manually for phone call responses and export data for planning and coordination.</p>
    </article>
    <article class="card-frame bg-white">
        <h2 class="font-heading text-3xl">Tenant + Billing Safety</h2>
        <p class="mt-2 text-wedding-muted">Account/site-scoped data model with Stripe subscription controls and webhook-based status sync.</p>
    </article>
</section>
@endsection
