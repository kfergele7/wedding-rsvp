@extends('staff.layout', ['title' => 'Template Management'])

@section('content')
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

