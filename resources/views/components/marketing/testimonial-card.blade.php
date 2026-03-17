@props([
    'quote' => '',
    'name' => '',
    'role' => '',
])

<x-marketing.card {{ $attributes }}>
    <p class="text-sm leading-relaxed text-wedding-black">“{{ $quote }}”</p>
    <p class="mt-4 text-xs font-medium uppercase tracking-[0.14em] text-wedding-muted">{{ $name }}</p>
    <p class="mt-1 text-xs text-wedding-muted">{{ $role }}</p>
</x-marketing.card>
