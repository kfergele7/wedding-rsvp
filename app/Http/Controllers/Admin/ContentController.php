<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveSiteSettingsRequest;
use App\Http\Requests\UploadContentImageRequest;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    public function show(): JsonResponse
    {
        $contentSetting = SiteSetting::query()->where('key', 'homepage_content')->first();

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
        $courses = $rsvpSettings['menu_courses'] ?? [];
        $rsvpSettings['menu_courses'] = [
            'starter' => $this->normalizeCourseItems($courses['starter'] ?? []),
            'main' => $this->normalizeCourseItems($courses['main'] ?? []),
            'dessert' => $this->normalizeCourseItems($courses['dessert'] ?? []),
        ];
        $rsvpSettings['meal_options'] = collect($rsvpSettings['meal_options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter(fn ($option) => $option !== '')
            ->values()
            ->all();

        // Keep RSVP meal options in sync with the Main course titles when option mode is used.
        if ($rsvpSettings['meal_mode'] === 'options' && count($rsvpSettings['menu_courses']['main']) > 0) {
            $rsvpSettings['meal_options'] = collect($rsvpSettings['menu_courses']['main'])
                ->pluck('title')
                ->map(fn ($title) => trim((string) $title))
                ->filter(fn ($title) => $title !== '')
                ->values()
                ->all();
        }

        $contentSetting = SiteSetting::query()->updateOrCreate(
            ['key' => 'homepage_content'],
            ['value' => $content]
        );
        SiteSetting::query()->updateOrCreate(
            ['key' => 'rsvp_settings'],
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
            ['key' => 'homepage_content'],
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
        $saved = SiteSetting::query()->where('key', 'homepage_content')->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $saved->value);
    }

    private function resolvedRsvpSettings(): array
    {
        $fallback = config('wedding.rsvp_settings', []);
        $saved = SiteSetting::query()->where('key', 'rsvp_settings')->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $saved->value);
    }

    private function normalizeCourseItems(array $items): array
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
}
