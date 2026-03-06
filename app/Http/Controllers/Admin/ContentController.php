<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveSiteSettingsRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Models\SiteSetting;
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

        return response()->json([
            'content' => $this->resolvedContent(),
            'rsvp_settings' => $this->resolvedRsvpSettings(),
            'last_saved_at' => $contentSetting?->updated_at?->toISOString(),
        ]);
    }

    public function update(SaveSiteSettingsRequest $request): JsonResponse
    {
        $incomingContent = (array) $request->input('content', []);
        $existingContent = $this->resolvedContent();
        $content = array_replace_recursive($existingContent, $incomingContent);
        $incomingRsvpSettings = (array) $request->input('rsvp_settings', []);
        $rsvpSettings = array_replace_recursive($this->resolvedRsvpSettings(), $incomingRsvpSettings);
        $rsvpSettings['meal_mode'] = in_array(($rsvpSettings['meal_mode'] ?? 'options'), ['options', 'set_menu'], true)
            ? $rsvpSettings['meal_mode']
            : 'options';
        $rsvpSettings['menu_courses'] = $this->normalizeCourseSections($rsvpSettings['menu_courses'] ?? []);
        $rsvpSettings['meal_options'] = collect($rsvpSettings['meal_options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter(fn ($option) => $option !== '')
            ->values()
            ->all();

        // Keep RSVP meal options in sync with main-like course titles when option mode is used.
        if ($rsvpSettings['meal_mode'] === 'options') {
            $mealOptionItems = $this->resolveMealOptionItems($rsvpSettings['menu_courses']);
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
            'content' => $content,
            'rsvp_settings' => $rsvpSettings,
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

        SiteSetting::query()->updateOrCreate(
            ['site_id' => $this->currentSiteId(), 'key' => 'homepage_content'],
            ['value' => $content]
        );

        return response()->json([
            'message' => 'Image uploaded and content updated.',
            'path' => $relativePath,
            'content' => $content,
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

        return array_replace_recursive($fallback, $saved->value);
    }

    private function resolvedRsvpSettings(): array
    {
        $fallback = config('wedding.rsvp_settings', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'rsvp_settings')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            $fallback['menu_courses'] = $this->normalizeCourseSections($fallback['menu_courses'] ?? []);
            return $fallback;
        }

        $merged = array_replace_recursive($fallback, $saved->value);
        $merged['menu_courses'] = $this->normalizeCourseSections($saved->value['menu_courses'] ?? ($fallback['menu_courses'] ?? []));

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
                $items = collect($section['items'] ?? [])
                    ->map(function ($item) {
                        return [
                            'title' => trim((string) ($item['title'] ?? '')),
                            'description' => trim((string) ($item['description'] ?? '')),
                        ];
                    })
                    ->filter(fn ($item) => $item['title'] !== '')
                    ->values()
                    ->all();

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

    private function resolveMealOptionItems(array $sections): array
    {
        $mainLike = collect($sections)
            ->first(function ($section) {
                $name = strtolower(trim((string) ($section['name'] ?? '')));
                return str_contains($name, 'main') || str_contains($name, 'entree');
            });

        if (is_array($mainLike) && count($mainLike['items'] ?? []) > 0) {
            return $mainLike['items'];
        }

        $second = $sections[1] ?? null;
        if (is_array($second) && count($second['items'] ?? []) > 0) {
            return $second['items'];
        }

        $first = $sections[0] ?? null;
        if (is_array($first) && count($first['items'] ?? []) > 0) {
            return $first['items'];
        }

        return [];
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
}
