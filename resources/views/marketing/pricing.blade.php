@extends('marketing.layout')

@section('content')
<x-marketing.container>
    <x-marketing.section :spaced="false" class="pt-4">
        <h1 class="font-heading text-5xl md:text-6xl">Simple pricing for wedding RSVPs</h1>
        <p class="mt-4 max-w-3xl text-wedding-muted">
            Build for free. Subscribe only when you want to publish and collect live RSVPs.
        </p>
    </x-marketing.section>

    <x-marketing.section class="mt-12" tone="white">
        <div class="grid gap-6 lg:grid-cols-2">
            <x-marketing.card class="p-8">
                <p class="text-xs uppercase tracking-[0.16em] text-wedding-muted">Free (draft mode)</p>
                <h2 class="mt-3 font-heading text-4xl">£0</h2>
                <ul class="mt-6 space-y-3 text-sm text-wedding-black">
                    @foreach ($freeIncludes as $line)
                        <li class="inline-flex items-start gap-2">
                            <span class="material-symbols-outlined mt-[1px] text-[16px] text-wedding-success">check_circle</span>
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-marketing.card>

            <x-marketing.pricing-card
                :name="$plan['name']"
                :price="$plan['price']"
                :interval="$plan['interval']"
                :includes="$plan['includes']"
            />
        </div>
    </x-marketing.section>

    <x-marketing.section class="mt-12" tone="soft">
        <h2 class="font-heading text-4xl">What paid unlocks</h2>
        <ul class="mt-6 grid gap-3 md:grid-cols-2">
            @foreach ($paidIncludes as $line)
                <li class="rounded-xl border border-soft bg-white px-5 py-4 text-sm text-wedding-black">{{ $line }}</li>
            @endforeach
        </ul>
    </x-marketing.section>

    <x-marketing.section class="mt-12 mb-10" tone="dark">
        <h2 class="font-heading text-4xl text-white">Cancellation is simple</h2>
        <p class="mt-4 max-w-3xl text-white/80">
            Cancel at any time. Your site remains live until the end of your current billing period, then moves back to non-public mode until reactivated.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
            <x-marketing.button href="{{ route('register') }}" variant="light" size="lg">Start free</x-marketing.button>
            <x-marketing.button href="{{ route('login') }}" variant="secondary" size="lg">Subscribe to publish</x-marketing.button>
        </div>
    </x-marketing.section>
</x-marketing.container>
@endsection
