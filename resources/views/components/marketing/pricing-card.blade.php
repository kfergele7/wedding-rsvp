@props([
    'name' => '',
    'price' => '',
    'interval' => '',
    'includes' => [],
])

<x-marketing.card {{ $attributes->merge(['class' => 'p-8 md:p-10']) }}>
    <p class="text-xs uppercase tracking-[0.2em] text-wedding-muted">Plan</p>
    <h3 class="mt-3 font-heading text-4xl text-wedding-black">{{ $name }}</h3>
    <p class="mt-2 text-3xl text-wedding-black">{{ $price }}<span class="text-base text-wedding-muted">{{ $interval }}</span></p>

    <ul class="mt-6 space-y-3 text-sm text-wedding-black">
        @foreach ($includes as $line)
            <li class="inline-flex items-start gap-2">
                <span class="material-symbols-outlined mt-[1px] text-[16px] text-wedding-success">check_circle</span>
                <span>{{ $line }}</span>
            </li>
        @endforeach
    </ul>

    <div class="mt-7 flex flex-wrap gap-3">
        <x-marketing.button href="{{ route('register') }}" variant="primary">Start free</x-marketing.button>
        <x-marketing.button href="{{ route('login') }}" variant="secondary">Log in</x-marketing.button>
    </div>
</x-marketing.card>
