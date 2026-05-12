<?php

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Schema;

class WeddingPalettes
{
    public const DEFAULT = 'magic_classic';
    public const CUSTOM_SETTING_KEY = 'custom_colour_palettes';

    public static function all(): array
    {
        return collect(self::rawPalettes())
            ->map(fn (array $palette, string $slug) => self::normalisePalette($slug, $palette))
            ->values()
            ->all();
    }

    public static function base(): array
    {
        return collect(self::basePalettes())
            ->map(fn (array $palette, string $slug) => self::normalisePalette($slug, $palette))
            ->values()
            ->all();
    }

    public static function custom(): array
    {
        return collect(self::customPalettes())
            ->map(fn (array $palette, string $slug) => self::normalisePalette($slug, $palette))
            ->values()
            ->all();
    }

    public static function slugs(): array
    {
        return array_keys(self::rawPalettes());
    }

    public static function baseSlugs(): array
    {
        return array_keys(self::basePalettes());
    }

    public static function find(?string $slug): array
    {
        $resolvedSlug = self::resolveSlug($slug);
        $palette = self::rawPalettes()[$resolvedSlug] ?? self::basePalettes()[self::DEFAULT] ?? [];

        return self::normalisePalette($resolvedSlug, $palette);
    }

    public static function resolveSlug(?string $slug): string
    {
        $normalised = str($slug ?: self::DEFAULT)
            ->lower()
            ->replace([' ', '-'], '_')
            ->toString();

        $normalised = [
            'blush_morning' => 'lavender_haze',
        ][$normalised] ?? $normalised;

        return array_key_exists($normalised, self::rawPalettes())
            ? $normalised
            : self::DEFAULT;
    }

    public static function applyToContent(array $content): array
    {
        $theme = $content['theme'] ?? [];
        $palette = self::find((string) ($theme['palette'] ?? self::DEFAULT));

        $content['theme'] = array_replace($theme, [
            'palette' => $palette['slug'],
            'palette_colours' => $palette['colours'],
        ]);

        return $content;
    }

    public static function stripDerivedThemeData(array $content): array
    {
        unset($content['theme']['palette_colours']);

        return $content;
    }

    private static function normalisePalette(string $slug, array $palette): array
    {
        $fallback = self::basePalettes()[self::DEFAULT]['colours'] ?? [];
        $colours = array_replace($fallback, $palette['colours'] ?? []);

        return [
            'slug' => $slug,
            'name' => (string) ($palette['name'] ?? str($slug)->replace('_', ' ')->title()),
            'mood' => (string) ($palette['mood'] ?? ''),
            'colours' => [
                'primary' => self::normaliseHex($colours['primary'] ?? '#22363A', '#22363A'),
                'secondary' => self::normaliseHex($colours['secondary'] ?? '#466369', '#466369'),
                'dark' => self::normaliseHex($colours['dark'] ?? '#0F1B1D', '#0F1B1D'),
                'soft_background' => self::normaliseHex($colours['soft_background'] ?? '#F7F5F2', '#F7F5F2'),
                'light' => self::normaliseHex($colours['light'] ?? '#FFFFFF', '#FFFFFF'),
            ],
        ];
    }

    private static function rawPalettes(): array
    {
        return array_replace(self::basePalettes(), self::customPalettes());
    }

    private static function basePalettes(): array
    {
        return config('wedding.colour_palettes', []);
    }

    private static function customPalettes(): array
    {
        if (! Schema::hasTable('platform_settings')) {
            return [];
        }

        $setting = PlatformSetting::query()
            ->where('key', self::CUSTOM_SETTING_KEY)
            ->first();

        if (! is_array($setting?->value)) {
            return [];
        }

        return collect($setting->value)
            ->filter(fn (mixed $palette, mixed $slug) => is_string($slug) && is_array($palette))
            ->mapWithKeys(fn (array $palette, string $slug) => [$slug => $palette])
            ->all();
    }

    private static function normaliseHex(string $colour, string $fallback): string
    {
        $colour = strtoupper(trim($colour));

        return preg_match('/^#[0-9A-F]{6}$/', $colour) ? $colour : $fallback;
    }
}
