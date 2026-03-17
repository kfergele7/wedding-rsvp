@extends('marketing.layout')

@section('content')
<x-marketing.container>
    <x-marketing.section :spaced="false" class="pt-4">
        <h1 class="font-heading text-5xl md:text-6xl">Frequently asked questions</h1>
        <p class="mt-4 max-w-3xl text-wedding-muted">
            Everything couples usually ask before they publish and collect RSVPs.
        </p>
    </x-marketing.section>

    <x-marketing.section class="mt-12" tone="white">
        <div class="grid gap-8">
            @foreach ($faqGroups as $group)
                <section>
                    <h2 class="font-heading text-3xl">{{ $group['title'] }}</h2>
                    <x-marketing.faq-accordion class="mt-4" :items="$group['items']" />
                </section>
            @endforeach
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-12 mb-10" tone="soft">
        <div class="grid gap-6 md:grid-cols-[1.15fr_0.85fr] md:items-center">
            <div>
                <h2 class="font-heading text-4xl">Still deciding?</h2>
                <p class="mt-4 max-w-2xl text-wedding-muted">
                    Start free, set everything up, and only subscribe when you are ready for guests to RSVP.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <x-marketing.button href="{{ route('register') }}" variant="primary">Start free</x-marketing.button>
                    <x-marketing.button href="{{ route('marketing.pricing') }}" variant="secondary">View pricing</x-marketing.button>
                </div>
            </div>
            <div class="overflow-hidden rounded-xl border border-soft bg-white p-2">
                <img loading="lazy" src="/images/wedding/uploads/story-image-20260224121353.jpg" alt="Wedding couple" class="h-60 w-full rounded-lg object-cover">
            </div>
        </div>
    </x-marketing.section>
</x-marketing.container>
@endsection
