@props([
    'items' => [],
])

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    @foreach ($items as $item)
        <details class="group rounded-xl border border-soft bg-white p-5 open:bg-wedding-off-white/70">
            <summary class="flex cursor-pointer list-none items-start justify-between gap-3 text-left">
                <span class="font-heading text-2xl text-wedding-black">{{ $item['q'] }}</span>
                <span class="material-symbols-outlined text-wedding-band transition group-open:rotate-45">add</span>
            </summary>
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-wedding-muted">{{ $item['a'] }}</p>
        </details>
    @endforeach
</div>
