<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\Site;
use App\Models\StaffAuditLog;
use App\Support\WeddingPalettes;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TemplateManagementController extends Controller
{
    public function index(): View
    {
        return view('staff.templates.index', [
            'fieldHelpDefinitions' => $this->fieldHelpDefinitions(),
            'sites' => Site::query()->orderBy('title')->get(['id', 'title', 'public_slug']),
            'selectedDemoSiteId' => $this->selectedDemoSiteId(),
            'baseColourPalettes' => WeddingPalettes::base(),
            'customColourPalettes' => WeddingPalettes::custom(),
        ]);
    }

    public function updateFieldHelp(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('platform_settings')) {
            return back()->with('status', 'Platform settings table not found yet. Run migrations first.');
        }

        $defaults = $this->fieldHelpDefaults();

        $validated = $request->validate([
            'field_help_texts' => ['required', 'array'],
            'field_help_texts.*' => ['nullable', 'string', 'max:500'],
        ]);

        $incoming = collect($validated['field_help_texts'])
            ->mapWithKeys(fn (mixed $value, mixed $key) => [(string) $key => is_string($value) ? trim($value) : ''])
            ->only(array_keys($defaults))
            ->all();

        $overrides = [];
        foreach ($incoming as $key => $value) {
            if ($value !== '' && $value !== ($defaults[$key] ?? '')) {
                $overrides[$key] = $value;
            }
        }

        $setting = PlatformSetting::query()->where('key', 'admin_field_help_texts')->first();
        $before = is_array($setting?->value) ? $setting->value : [];

        PlatformSetting::query()->updateOrCreate(
            ['key' => 'admin_field_help_texts'],
            ['value' => $overrides]
        );

        StaffAuditLog::query()->create([
            'staff_user_id' => $request->user()->id,
            'account_id' => null,
            'action' => 'staff.platform.field_help.updated',
            'payload' => [
                'before' => $before,
                'after' => $overrides,
            ],
        ]);

        return back()->with('status', 'Global content help text updated for all accounts.');
    }

    public function updateDemoSource(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('platform_settings')) {
            return back()->with('status', 'Platform settings table not found yet. Run migrations first.');
        }

        $validated = $request->validate([
            'demo_site_id' => ['required', 'integer', 'exists:sites,id'],
        ]);

        $beforeSetting = PlatformSetting::query()->where('key', 'demo_template_source')->first();
        $before = is_array($beforeSetting?->value) ? $beforeSetting->value : [];
        $after = ['site_id' => (int) $validated['demo_site_id']];

        PlatformSetting::query()->updateOrCreate(
            ['key' => 'demo_template_source'],
            ['value' => $after]
        );

        StaffAuditLog::query()->create([
            'staff_user_id' => $request->user()->id,
            'account_id' => null,
            'action' => 'staff.platform.demo_source.updated',
            'payload' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);

        return back()->with('status', 'Demo template source updated.');
    }

    public function updateColourPalettes(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('platform_settings')) {
            return back()->with('status', 'Platform settings table not found yet. Run migrations first.');
        }

        $before = $this->customColourPaletteSetting();
        $after = [];
        $reservedSlugs = WeddingPalettes::baseSlugs();

        foreach ((array) $request->input('custom_palettes', []) as $key => $paletteInput) {
            if (! is_array($paletteInput) || (bool) ($paletteInput['delete'] ?? false)) {
                continue;
            }

            $palette = $this->normaliseColourPaletteInput($paletteInput, (string) $key, $reservedSlugs, array_keys($after));
            $after[$palette['slug']] = [
                'name' => $palette['name'],
                'mood' => $palette['mood'],
                'colours' => $palette['colours'],
            ];
        }

        $newPaletteInput = (array) $request->input('new_palette', []);
        if ($this->hasPaletteInput($newPaletteInput)) {
            $palette = $this->normaliseColourPaletteInput($newPaletteInput, (string) ($newPaletteInput['name'] ?? ''), $reservedSlugs, array_keys($after));
            $after[$palette['slug']] = [
                'name' => $palette['name'],
                'mood' => $palette['mood'],
                'colours' => $palette['colours'],
            ];
        }

        PlatformSetting::query()->updateOrCreate(
            ['key' => WeddingPalettes::CUSTOM_SETTING_KEY],
            ['value' => $after]
        );

        StaffAuditLog::query()->create([
            'staff_user_id' => $request->user()->id,
            'account_id' => null,
            'action' => 'staff.platform.colour_palettes.updated',
            'payload' => [
                'before' => $before,
                'after' => $after,
            ],
        ]);

        return back()->with('status', 'Colour palettes updated for all accounts.');
    }

    private function fieldHelpDefinitions(): array
    {
        $definitions = config('wedding.admin_field_help_texts', []);
        $defaults = $this->fieldHelpDefaults();

        if (! Schema::hasTable('platform_settings')) {
            return collect($definitions)
                ->map(function (mixed $definition, mixed $key) use ($defaults) {
                    $stringKey = (string) $key;

                    return [
                        'key' => $stringKey,
                        'label' => (string) ($definition['label'] ?? $stringKey),
                        'default' => (string) ($defaults[$stringKey] ?? ''),
                        'value' => (string) ($defaults[$stringKey] ?? ''),
                    ];
                })
                ->values()
                ->all();
        }

        $saved = PlatformSetting::query()
            ->where('key', 'admin_field_help_texts')
            ->first();

        $overrides = collect(is_array($saved?->value) ? $saved->value : [])
            ->mapWithKeys(fn (mixed $value, mixed $key) => [(string) $key => is_string($value) ? trim($value) : ''])
            ->filter(fn (string $value) => $value !== '')
            ->all();

        return collect($definitions)
            ->map(function (mixed $definition, mixed $key) use ($defaults, $overrides) {
                $stringKey = (string) $key;

                return [
                    'key' => $stringKey,
                    'label' => (string) ($definition['label'] ?? $stringKey),
                    'default' => (string) ($defaults[$stringKey] ?? ''),
                    'value' => (string) ($overrides[$stringKey] ?? ($defaults[$stringKey] ?? '')),
                ];
            })
            ->values()
            ->all();
    }

    private function fieldHelpDefaults(): array
    {
        return collect(config('wedding.admin_field_help_texts', []))
            ->map(fn (mixed $definition) => (string) ($definition['default'] ?? ''))
            ->all();
    }

    private function selectedDemoSiteId(): ?int
    {
        if (! Schema::hasTable('platform_settings')) {
            return null;
        }

        $setting = PlatformSetting::query()
            ->where('key', 'demo_template_source')
            ->first();

        $siteId = is_array($setting?->value) ? ($setting->value['site_id'] ?? null) : null;

        return is_numeric($siteId) ? (int) $siteId : null;
    }

    private function customColourPaletteSetting(): array
    {
        if (! Schema::hasTable('platform_settings')) {
            return [];
        }

        $setting = PlatformSetting::query()
            ->where('key', WeddingPalettes::CUSTOM_SETTING_KEY)
            ->first();

        return is_array($setting?->value) ? $setting->value : [];
    }

    private function normaliseColourPaletteInput(array $input, string $fallbackSlug, array $reservedSlugs, array $existingSlugs): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $mood = trim((string) ($input['mood'] ?? ''));
        $slug = Str::slug(trim((string) ($input['slug'] ?? '')) ?: $fallbackSlug, '_');

        if ($name === '') {
            throw ValidationException::withMessages(['new_palette.name' => 'Palette name is required.']);
        }

        if ($slug === '') {
            throw ValidationException::withMessages(['new_palette.slug' => 'Palette slug is required.']);
        }

        if (in_array($slug, $reservedSlugs, true)) {
            throw ValidationException::withMessages(['new_palette.slug' => 'Palette slug is already used by a built-in palette.']);
        }

        if (in_array($slug, $existingSlugs, true)) {
            throw ValidationException::withMessages(['new_palette.slug' => 'Palette slug must be unique.']);
        }

        return [
            'slug' => $slug,
            'name' => Str::limit($name, 80, ''),
            'mood' => Str::limit($mood, 140, ''),
            'colours' => [
                'primary' => $this->normaliseHexColour($input['primary'] ?? null, 'Primary'),
                'secondary' => $this->normaliseHexColour($input['secondary'] ?? null, 'Secondary'),
                'dark' => $this->normaliseHexColour($input['dark'] ?? null, 'Dark'),
                'soft_background' => $this->normaliseHexColour($input['soft_background'] ?? null, 'Soft background'),
                'light' => $this->normaliseHexColour($input['light'] ?? null, 'Light'),
            ],
        ];
    }

    private function normaliseHexColour(mixed $value, string $label): string
    {
        $colour = strtoupper(trim((string) $value));

        if (! preg_match('/^#[0-9A-F]{6}$/', $colour)) {
            throw ValidationException::withMessages(['new_palette.colours' => "{$label} must be a valid hex colour, for example #22363A."]);
        }

        return $colour;
    }

    private function hasPaletteInput(array $input): bool
    {
        return collect($input)
            ->filter(fn (mixed $value) => trim((string) $value) !== '')
            ->isNotEmpty();
    }
}
