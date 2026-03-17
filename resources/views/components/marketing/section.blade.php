@props([
    'tone' => 'default',
    'spaced' => true,
])

@php
    $toneClasses = [
        'default' => 'bg-transparent',
        'soft' => 'bg-wedding-secondary-light/65 border border-soft rounded-2xl',
        'white' => 'bg-white border border-soft rounded-2xl shadow-soft',
        'offwhite' => 'bg-wedding-off-white border border-soft rounded-2xl',
        'dark' => 'bg-wedding-band text-white rounded-2xl border border-white/20',
    ][$tone] ?? 'bg-transparent';

    $spaceClass = $spaced ? 'py-14 md:py-20' : '';
@endphp

<section {{ $attributes->merge(['class' => trim($spaceClass.' '.$toneClasses)]) }}>
    {{ $slot }}
</section>
