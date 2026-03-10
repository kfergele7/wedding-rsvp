<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\Site;
use App\Models\StaffAuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TemplateManagementController extends Controller
{
    public function index(): View
    {
        return view('staff.templates.index', [
            'fieldHelpDefinitions' => $this->fieldHelpDefinitions(),
            'sites' => Site::query()->orderBy('title')->get(['id', 'title', 'public_slug']),
            'selectedDemoSiteId' => $this->selectedDemoSiteId(),
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
}
