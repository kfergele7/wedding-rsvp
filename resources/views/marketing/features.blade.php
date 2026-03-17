@extends('marketing.layout')

@section('content')
<x-marketing.container>
    <x-marketing.section :spaced="false" class="pt-4">
        <h1 class="font-heading text-5xl md:text-6xl">Purpose-built features for wedding operations</h1>
        <p class="mt-4 max-w-3xl text-wedding-muted">
            This is not a generic website builder. It is one elegant template with operational tools for guests, RSVPs, and planning logistics.
        </p>
    </x-marketing.section>

    <x-marketing.section class="mt-12">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <x-marketing.feature-card icon="web" title="Form-based content editor" description="Update names, dates, timeline, travel, menu, and FAQs from clean forms." />
            <x-marketing.feature-card icon="key" title="Invite-code RSVP flow" description="Guests RSVP with invitation codes, with no guest account creation required." />
            <x-marketing.feature-card icon="qr_code_2" title="QR-ready sharing" description="Use printed QR codes and links on invitations for direct RSVP access." />
            <x-marketing.feature-card icon="group" title="Household and party management" description="Structure guests by party with seats, notes, and named attendees." />
            <x-marketing.feature-card icon="restaurant" title="Meal and dietary capture" description="Collect meal choices and dietary requirements during RSVP." />
            <x-marketing.feature-card icon="download" title="CSV exports" description="Export guest and RSVP data for venue coordination and seating plans." />
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-12" tone="white">
        <div class="grid gap-8 md:grid-cols-[1fr_1fr] md:items-center">
            <div>
                <h2 class="font-heading text-4xl">For older guests</h2>
                <p class="mt-4 text-wedding-muted">
                    Keep RSVP simple with invite codes and manual support workflows. If guests call in, you can enter or edit responses on their behalf.
                </p>
            </div>
            <div class="overflow-hidden rounded-xl border border-soft bg-wedding-off-white p-2">
                <img loading="lazy" src="/images/wedding/uploads/welcome-image-20260224121344.jpg" alt="Support-focused RSVP management" class="h-64 w-full rounded-lg object-cover">
            </div>
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-12 mb-10" tone="soft">
        <div class="grid gap-8 md:grid-cols-[1fr_1fr] md:items-center">
            <div class="order-2 md:order-1 overflow-hidden rounded-xl border border-soft bg-white p-2">
                <img loading="lazy" src="/images/wedding/uploads/details-image-20260224121400.jpg" alt="Private RSVP controls" class="h-64 w-full rounded-lg object-cover">
            </div>
            <div class="order-1 md:order-2">
                <h2 class="font-heading text-4xl">Privacy-first RSVPs</h2>
                <p class="mt-4 text-wedding-muted">
                    RSVP lookups are invite-code based and tenant-scoped. Couples control visibility with draft and publish states.
                </p>
                <div class="mt-6">
                    <x-marketing.button href="{{ route('register') }}" variant="primary">Start free</x-marketing.button>
                </div>
            </div>
        </div>
    </x-marketing.section>
</x-marketing.container>
@endsection
