@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $sizeClasses = [
        'sm' => 'px-4 py-2 text-[11px] tracking-[0.14em]',
        'md' => 'px-6 py-3 text-xs tracking-[0.16em]',
        'lg' => 'px-8 py-4 text-xs tracking-[0.2em]',
    ][$size] ?? 'px-6 py-3 text-xs tracking-[0.16em]';

    $variantClasses = [
        'primary' => 'border border-wedding-band bg-wedding-band text-white hover:bg-wedding-primarygreen hover:border-wedding-primarygreen',
        'secondary' => 'border border-wedding-band bg-white text-wedding-band hover:bg-wedding-light',
        'light' => 'border border-white bg-white text-wedding-black hover:bg-wedding-secondary-light hover:border-wedding-secondary-light',
    ][$variant] ?? 'border border-wedding-band bg-wedding-band text-white hover:bg-wedding-primarygreen hover:border-wedding-primarygreen';

    $classes = trim('inline-flex items-center justify-center uppercase font-medium transition '.$sizeClasses.' '.$variantClasses);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
