@extends('staff.layout', ['title' => 'Template Management', 'activeTab' => 'templates'])

@section('content')
<section class="mb-6 card-frame bg-white">
    <h2 class="font-heading text-2xl">Demo Website Source</h2>
    <p class="mt-2 text-sm text-wedding-muted">
        Select what powers the global demo link at <span class="font-medium text-wedding-black">/demo</span>.
        Use the Magic Invitation default demo, or select a customer site if you want the demo to use that account's content.
    </p>
    <form method="POST" action="{{ route('staff.templates.demo-source.update') }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_auto] md:items-end">
        @csrf
        @method('PUT')
        <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
            Demo Source
            <select name="demo_site_id" class="mt-1 w-full border border-soft bg-white px-3 py-2 normal-case tracking-normal text-wedding-black" required>
                <option value="default" @selected(! $selectedDemoSiteId)>Magic Invitation default demo</option>
                @if ($sites->count())
                    <option value="" disabled>Customer demo sites</option>
                @endif
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" @selected((int) $selectedDemoSiteId === (int) $site->id)>
                        {{ $site->title }} ({{ $site->public_slug }})
                    </option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="button-dark px-4 py-2 text-xs uppercase tracking-[0.12em]">Save Demo Source</button>
    </form>
</section>

<section class="mb-6 card-frame bg-white">
    <h2 class="font-heading text-2xl">Colour Palette Management</h2>
    <p class="mt-2 text-sm text-wedding-muted">
        Add custom palettes for the customer website editor. Built-in palettes are protected, while custom palettes can be edited or removed here.
    </p>

    <div class="mt-5">
        <h3 class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Built-in palettes</h3>
        <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($baseColourPalettes as $palette)
                <article class="border border-soft bg-[#F7F7F7] p-4">
                    <p class="font-heading text-xl text-wedding-black">{{ $palette['name'] }}</p>
                    <p class="mt-1 min-h-10 text-sm leading-relaxed text-wedding-muted">{{ $palette['mood'] }}</p>
                    <div class="mt-3 flex flex-nowrap gap-1.5" aria-label="{{ $palette['name'] }} colour swatches">
                        @foreach ($palette['colours'] as $colour)
                            <span class="inline-flex h-7 w-7 flex-none rounded-full border border-soft" style="background-color: {{ $colour }}"></span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('staff.templates.colour-palettes.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <h3 class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Custom palettes</h3>
            <div class="mt-3 space-y-4">
                @forelse ($customColourPalettes as $palette)
                    <article class="border border-soft bg-wedding-light p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-heading text-xl text-wedding-black">{{ $palette['name'] }}</p>
                                <p class="mt-1 text-sm text-wedding-muted">Slug: {{ $palette['slug'] }}</p>
                            </div>
                            <label class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.12em] text-wedding-danger">
                                <input type="checkbox" name="custom_palettes[{{ $palette['slug'] }}][delete]" value="1" class="h-4 w-4">
                                Remove
                            </label>
                        </div>
                        <input type="hidden" name="custom_palettes[{{ $palette['slug'] }}][slug]" value="{{ $palette['slug'] }}">
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                Palette Name
                                <input name="custom_palettes[{{ $palette['slug'] }}][name]" value="{{ old("custom_palettes.{$palette['slug']}.name", $palette['name']) }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 normal-case tracking-normal text-wedding-black" required>
                            </label>
                            <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                Mood
                                <input name="custom_palettes[{{ $palette['slug'] }}][mood]" value="{{ old("custom_palettes.{$palette['slug']}.mood", $palette['mood']) }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 normal-case tracking-normal text-wedding-black" required>
                            </label>
                        </div>
                        <div class="mt-3 grid gap-3 md:grid-cols-5">
                            @foreach (['primary' => 'Primary', 'secondary' => 'Secondary', 'dark' => 'Dark', 'soft_background' => 'Soft Background', 'light' => 'Light'] as $key => $label)
                                <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                                    {{ $label }}
                                    <input name="custom_palettes[{{ $palette['slug'] }}][{{ $key }}]" value="{{ old("custom_palettes.{$palette['slug']}.{$key}", $palette['colours'][$key] ?? '') }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 font-mono text-sm normal-case tracking-normal text-wedding-black" placeholder="#22363A" required>
                                </label>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <p class="border border-dashed border-soft bg-[#F7F7F7] p-4 text-sm text-wedding-muted">No custom palettes have been added yet.</p>
                @endforelse
            </div>
        </div>

        <div class="border-t border-soft pt-6">
            <h3 class="text-xs uppercase tracking-[0.14em] text-wedding-muted">Add new palette</h3>
            <div class="mt-3 border border-soft bg-white p-4">
                <div class="grid gap-3 md:grid-cols-3">
                    <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Palette Name
                        <input name="new_palette[name]" value="{{ old('new_palette.name') }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 normal-case tracking-normal text-wedding-black" placeholder="e.g. Winter Pearl">
                    </label>
                    <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Mood
                        <input name="new_palette[mood]" value="{{ old('new_palette.mood') }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 normal-case tracking-normal text-wedding-black" placeholder="Calm, crisp, elegant">
                    </label>
                    <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                        Slug <span class="normal-case tracking-normal text-wedding-muted">(optional)</span>
                        <input name="new_palette[slug]" value="{{ old('new_palette.slug') }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 font-mono text-sm normal-case tracking-normal text-wedding-black" placeholder="winter_pearl">
                    </label>
                </div>
                <div class="mt-3 grid gap-3 md:grid-cols-5">
                    @foreach (['primary' => 'Primary', 'secondary' => 'Secondary', 'dark' => 'Dark', 'soft_background' => 'Soft Background', 'light' => 'Light'] as $key => $label)
                        <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                            {{ $label }}
                            <input name="new_palette[{{ $key }}]" value="{{ old("new_palette.{$key}") }}" class="mt-1 w-full border border-soft bg-white px-3 py-2 font-mono text-sm normal-case tracking-normal text-wedding-black" placeholder="#22363A">
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="button-dark px-4 py-2 text-xs uppercase tracking-[0.12em]">Save Colour Palettes</button>
    </form>
</section>

<section class="card-frame bg-white">
    <h2 class="font-heading text-2xl">Info Icon Text Management</h2>
    <p class="mt-2 text-sm text-wedding-muted">
        Update these once and they apply to all customer content admin areas.
    </p>
    <form method="POST" action="{{ route('staff.templates.field-help.update') }}" class="mt-4 space-y-3">
        @csrf
        @method('PUT')
        <div class="grid gap-3 md:grid-cols-2">
            @foreach ($fieldHelpDefinitions as $helpField)
                <label class="block text-xs uppercase tracking-[0.12em] text-wedding-muted">
                    {{ $helpField['label'] }}
                    <textarea
                        name="field_help_texts[{{ $helpField['key'] }}]"
                        rows="2"
                        class="mt-1 w-full border border-soft bg-white px-3 py-2 text-sm normal-case tracking-normal text-wedding-black"
                    >{{ old("field_help_texts.{$helpField['key']}", $helpField['value']) }}</textarea>
                    <span class="mt-1 block normal-case tracking-normal text-[11px] text-wedding-muted">Default: {{ $helpField['default'] }}</span>
                </label>
            @endforeach
        </div>
        <button type="submit" class="button-dark px-4 py-2 text-xs uppercase tracking-[0.12em]">Save Global Help Text</button>
    </form>
</section>
@endsection
