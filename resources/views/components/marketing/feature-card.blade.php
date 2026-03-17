@props([
    'icon' => 'auto_awesome',
    'title' => '',
    'description' => '',
])

<x-marketing.card {{ $attributes->merge(['class' => 'h-full']) }}>
    <div class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-soft bg-wedding-off-white text-wedding-band">
        <span class="material-symbols-outlined" style="font-size:18px;">{{ $icon }}</span>
    </div>
    <h3 class="mt-4 font-heading text-2xl text-wedding-black">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-wedding-muted">{{ $description }}</p>
</x-marketing.card>
