<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveSiteSettingsRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Models\SiteSetting;
use App\Support\InvitationTiming;
use App\Support\WeddingPalettes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function show(): JsonResponse
    {
        $contentSetting = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'homepage_content')
            ->first();

        $content = $this->resolvedContent();

        return response()->json([
            'content' => WeddingPalettes::applyToContent($content),
            'rsvp_settings' => $this->resolvedRsvpSettings(),
            'colour_palettes' => WeddingPalettes::all(),
            'image_library' => $this->imageLibrary($content),
            'last_saved_at' => $contentSetting?->updated_at?->toISOString(),
        ]);
    }

    public function update(SaveSiteSettingsRequest $request): JsonResponse
    {
        $incomingContent = (array) $request->input('content', []);
        $existingContent = $this->resolvedContent();
        $content = $this->mergeReplacingLists($existingContent, $incomingContent);
        $content = $this->normalizeHomepageContent($content, $incomingContent);
        $content = WeddingPalettes::stripDerivedThemeData($content);
        $incomingRsvpSettings = (array) $request->input('rsvp_settings', []);
        $rsvpSettings = $incomingRsvpSettings === []
            ? $this->resolvedRsvpSettings()
            : $this->mergeReplacingLists($this->resolvedRsvpSettings(), $incomingRsvpSettings);

        // Replace list-like RSVP fields directly so removed course/items do not get merged back.
        if (array_key_exists('menu_courses', $incomingRsvpSettings)) {
            $rsvpSettings['menu_courses'] = $incomingRsvpSettings['menu_courses'];
        }
        if (array_key_exists('meal_options', $incomingRsvpSettings)) {
            $rsvpSettings['meal_options'] = $incomingRsvpSettings['meal_options'];
        }
        if (array_key_exists('kids_menu_items', $incomingRsvpSettings)) {
            $rsvpSettings['kids_menu_items'] = $incomingRsvpSettings['kids_menu_items'];
        }

        $rsvpSettings['meal_mode'] = in_array(($rsvpSettings['meal_mode'] ?? 'set_menu'), ['options', 'set_menu'], true)
            ? $rsvpSettings['meal_mode']
            : 'set_menu';
        $rsvpSettings['kids_menu_enabled'] = (bool) ($rsvpSettings['kids_menu_enabled'] ?? false);
        $rsvpSettings['menu_courses'] = $this->normalizeCourseSections($rsvpSettings['menu_courses'] ?? []);
        $rsvpSettings['kids_menu_items'] = $this->normalizeMenuItems($rsvpSettings['kids_menu_items'] ?? []);
        $rsvpSettings['meal_options'] = collect($rsvpSettings['meal_options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter(fn ($option) => $option !== '')
            ->values()
            ->all();

        // Keep RSVP meal options in sync with all available selectable menu items.
        if ($rsvpSettings['meal_mode'] === 'options') {
            $mealOptionItems = $this->resolveMealOptionItems(
                $rsvpSettings['menu_courses'],
                $rsvpSettings['kids_menu_enabled'] ? $rsvpSettings['kids_menu_items'] : []
            );
            $rsvpSettings['meal_options'] = collect($mealOptionItems)
                ->pluck('title')
                ->map(fn ($title) => trim((string) $title))
                ->filter(fn ($title) => $title !== '')
                ->values()
                ->all();
        }

        $contentSetting = SiteSetting::query()->updateOrCreate(
            ['site_id' => $this->currentSiteId(), 'key' => 'homepage_content'],
            ['value' => $content]
        );
        SiteSetting::query()->updateOrCreate(
            ['site_id' => $this->currentSiteId(), 'key' => 'rsvp_settings'],
            ['value' => $rsvpSettings]
        );

        return response()->json([
            'message' => 'Content updated.',
            'content' => WeddingPalettes::applyToContent($content),
            'rsvp_settings' => $rsvpSettings,
            'colour_palettes' => WeddingPalettes::all(),
            'last_saved_at' => $contentSetting->updated_at?->toISOString(),
        ]);
    }

    public function uploadImage(UploadContentImageRequest $request): JsonResponse
    {
        $field = $request->validated('field');
        $uploadedFile = $request->file('image_file');

        $filename = sprintf(
            '%s-%s.%s',
            str_replace('.', '-', $field),
            now()->format('YmdHis'),
            $uploadedFile->getClientOriginalExtension()
        );

        $uploadDirectory = public_path('images/wedding/uploads');
        if (! is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $uploadedFile->move($uploadDirectory, $filename);

        $relativePath = '/images/wedding/uploads/'.$filename;

        $content = $this->resolvedContent();
        data_set($content, $field, $relativePath);
        $content = $this->normalizeHomepageContent($content, $content);
        $content = WeddingPalettes::stripDerivedThemeData($content);

        SiteSetting::query()->updateOrCreate(
            ['site_id' => $this->currentSiteId(), 'key' => 'homepage_content'],
            ['value' => $content]
        );

        $resolvedContent = WeddingPalettes::applyToContent($content);

        return response()->json([
            'message' => 'Image uploaded and content updated.',
            'path' => $relativePath,
            'content' => $resolvedContent,
            'image_library' => $this->imageLibrary($resolvedContent),
        ]);
    }

    private function resolvedContent(): array
    {
        $fallback = config('wedding.homepage_content', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'homepage_content')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return $this->mergeReplacingLists($fallback, $saved->value);
    }

    private function normalizeHomepageContent(array $content, array $incomingContent): array
    {
        $incomingVenue = data_get($incomingContent, 'details.venue');

        if (is_array($incomingVenue)) {
            data_set($content, 'details.venue.name', trim((string) ($incomingVenue['name'] ?? data_get($content, 'details.venue.name', ''))));
            data_set($content, 'details.venue.address', trim((string) ($incomingVenue['address'] ?? data_get($content, 'details.venue.address', ''))));
            data_set($content, 'details.venue.blurb', (string) ($incomingVenue['blurb'] ?? data_get($content, 'details.venue.blurb', '')));
        }

        $galleryItems = data_get($content, 'gallery.items', []);
        data_set($content, 'gallery.heading', trim((string) data_get($content, 'gallery.heading', "Photo's of us across the years")));
        data_set($content, 'gallery.items', $this->normalizeGalleryItems(is_array($galleryItems) ? $galleryItems : []));
        data_set(
            $content,
            'guest_list.responseDeadline',
            trim((string) data_get($content, 'guest_list.responseDeadline', '2026-08-15'))
        );
        $eveningArrivalTime = InvitationTiming::normalizeEveningArrivalTimeForStorage(
            data_get($content, 'guest_list.evening_arrival_time')
        );
        data_set($content, 'guest_list.evening_arrival_time', $eveningArrivalTime);
        $layout = data_get($content, 'theme.layout', 'classic');
        $layout = $layout === 'editorial' ? 'modern' : $layout;
        data_set($content, 'theme.layout', in_array($layout, ['classic', 'modern'], true) ? $layout : 'classic');
        data_set($content, 'theme.palette', WeddingPalettes::resolveSlug(data_get($content, 'theme.palette')));

        return $content;
    }

    private function normalizeGalleryItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                $image = trim((string) ($item['image'] ?? ''));

                return [
                    'image' => $image,
                    'imageFocusX' => $this->normalizeFocusValue($item['imageFocusX'] ?? 50),
                    'imageFocusY' => $this->normalizeFocusValue($item['imageFocusY'] ?? 50),
                ];
            })
            ->filter(fn ($item) => $item['image'] !== '')
            ->take(8)
            ->values()
            ->all();
    }

    private function normalizeFocusValue(mixed $value): int
    {
        if (! is_numeric($value)) {
            return 50;
        }

        return max(0, min(100, (int) $value));
    }

    private function resolvedRsvpSettings(): array
    {
        $fallback = config('wedding.rsvp_settings', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'rsvp_settings')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            $fallback['kids_menu_enabled'] = (bool) ($fallback['kids_menu_enabled'] ?? false);
            $fallback['menu_courses'] = $this->normalizeCourseSections($fallback['menu_courses'] ?? []);
            $fallback['kids_menu_items'] = $this->normalizeMenuItems($fallback['kids_menu_items'] ?? []);
            return $fallback;
        }

        $merged = $this->mergeReplacingLists($fallback, $saved->value);
        $merged['kids_menu_enabled'] = (bool) ($merged['kids_menu_enabled'] ?? false);
        $merged['menu_courses'] = $this->normalizeCourseSections($saved->value['menu_courses'] ?? ($fallback['menu_courses'] ?? []));
        $merged['kids_menu_items'] = $this->normalizeMenuItems($saved->value['kids_menu_items'] ?? ($fallback['kids_menu_items'] ?? []));

        return $merged;
    }

    private function normalizeCourseSections(array $sections): array
    {
        $defaultSections = $this->defaultCourseSections();

        if ($this->isLegacyCourseMap($sections)) {
            $legacyKeys = ['starter', 'main', 'dessert'];
            $sections = collect($legacyKeys)
                ->filter(fn ($key) => array_key_exists($key, $sections))
                ->map(fn ($key) => [
                    'id' => $key,
                    'name' => ucfirst((string) $key),
                    'items' => $sections[$key] ?? [],
                ])
                ->values()
                ->all();
        }

        $seenDefaultIds = [];
        $seenIds = [];
        $normalized = collect($sections)
            ->map(function ($section, $index) {
                $id = trim((string) ($section['id'] ?? ''));
                $name = trim((string) ($section['name'] ?? ''));
                $items = $this->normalizeMenuItems($section['items'] ?? []);

                if ($id === '' && in_array(strtolower($name), ['starter', 'main', 'dessert'], true)) {
                    $id = strtolower($name);
                }

                if ($id === '') {
                    $id = 'course-'.($index + 1);
                }

                return [
                    'id' => Str::slug($id),
                    'name' => $name,
                    'items' => $items,
                ];
            })
            ->filter(fn ($section) => $section['name'] !== '' || count($section['items']) > 0)
            ->filter(function ($section) use (&$seenDefaultIds, &$seenIds) {
                $id = strtolower(trim((string) ($section['id'] ?? '')));
                $name = strtolower(trim((string) ($section['name'] ?? '')));
                $defaultId = in_array($id, ['starter', 'main', 'dessert'], true)
                    ? $id
                    : (in_array($name, ['starter', 'main', 'dessert'], true) ? $name : null);

                if ($defaultId !== null) {
                    if (in_array($defaultId, $seenDefaultIds, true)) {
                        return false;
                    }
                    $seenDefaultIds[] = $defaultId;
                }

                if ($id !== '') {
                    if (in_array($id, $seenIds, true)) {
                        return false;
                    }
                    $seenIds[] = $id;
                }

                return true;
            })
            ->map(function ($section) {
                $id = strtolower(trim((string) ($section['id'] ?? '')));
                $name = trim((string) ($section['name'] ?? ''));

                if ($name === '' && in_array($id, ['starter', 'main', 'dessert'], true)) {
                    $name = ucfirst($id);
                }

                return [
                    'id' => $id !== '' ? $id : 'course-'.Str::random(6),
                    'name' => $name !== '' ? $name : 'Course',
                    'items' => $section['items'] ?? [],
                ];
            })
            ->values()
            ->all();

        if (count($normalized) === 0) {
            return $defaultSections;
        }

        return $normalized;
    }

    private function normalizeMenuItems(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                return [
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(fn ($item) => $item['title'] !== '')
            ->values()
            ->all();
    }

    private function resolveMealOptionItems(array $sections, array $kidsMenuItems = []): array
    {
        return collect($sections)
            ->pluck('items')
            ->flatten(1)
            ->merge($kidsMenuItems)
            ->filter(fn ($item) => is_array($item) && trim((string) ($item['title'] ?? '')) !== '')
            ->values()
            ->all();
    }

    private function isLegacyCourseMap(array $sections): bool
    {
        if (array_is_list($sections)) {
            return false;
        }

        return collect($sections)->keys()->contains(function ($key) {
            return in_array($key, ['starter', 'main', 'dessert'], true);
        });
    }

    private function defaultCourseSections(): array
    {
        return [
            ['id' => 'starter', 'name' => 'Starter', 'items' => []],
            ['id' => 'main', 'name' => 'Main', 'items' => []],
            ['id' => 'dessert', 'name' => 'Dessert', 'items' => []],
        ];
    }

    private function imageLibrary(array $content): array
    {
        $paths = [
            data_get($content, 'hero.image'),
            data_get($content, 'welcome.image'),
            data_get($content, 'story.image'),
            data_get($content, 'details.image'),
            ...collect(data_get($content, 'gallery.items', []))
                ->pluck('image')
                ->all(),
        ];

        return collect($paths)
            ->map(fn ($path) => trim((string) $path))
            ->filter(fn ($path) => str_starts_with($path, '/images/wedding/uploads/'))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Merge nested associative arrays while replacing list arrays outright.
     *
     * This prevents deleted list items (timeline rows, FAQs, menu items, etc.)
     * from reappearing due to recursive index-based array merges.
     */
    private function mergeReplacingLists(array $base, array $incoming): array
    {
        if (array_is_list($incoming)) {
            return $incoming;
        }

        $merged = $base;

        foreach ($incoming as $key => $value) {
            if (
                array_key_exists($key, $base)
                && is_array($base[$key])
                && is_array($value)
                && ! array_is_list($value)
            ) {
                $merged[$key] = $this->mergeReplacingLists($base[$key], $value);
                continue;
            }

            $merged[$key] = $value;
        }

        return $merged;
    }
}
