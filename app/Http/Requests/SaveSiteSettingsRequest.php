<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'array'],
            'content.theme' => ['nullable', 'array'],
            'content.theme.primary_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }

                    if (! $this->isDarkHexColor($value)) {
                        $fail('Primary section color must be a dark hex color for text contrast.');
                    }
                },
            ],
            'content.theme.button_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! is_string($value)) {
                        return;
                    }

                    if (! $this->isDarkHexColor($value)) {
                        $fail('Button color must be a dark hex color for text contrast.');
                    }
                },
            ],
            'rsvp_settings' => ['nullable', 'array'],
            'rsvp_settings.meal_mode' => ['nullable', Rule::in(['options', 'set_menu'])],
            'rsvp_settings.menu_heading' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_intro' => ['nullable', 'string', 'max:2000'],
            'rsvp_settings.set_menu_description' => ['nullable', 'string', 'max:2000'],
            'rsvp_settings.menu_note_title' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_note_text' => ['nullable', 'string', 'max:2000'],
            'rsvp_settings.meal_options' => ['nullable', 'array'],
            'rsvp_settings.meal_options.*' => ['string', 'min:1', 'max:60'],
            'rsvp_settings.menu_courses' => ['nullable', 'array'],
            'rsvp_settings.menu_courses.starter' => ['nullable', 'array'],
            'rsvp_settings.menu_courses.main' => ['nullable', 'array'],
            'rsvp_settings.menu_courses.dessert' => ['nullable', 'array'],
            'rsvp_settings.menu_courses.starter.*.title' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_courses.starter.*.description' => ['nullable', 'string', 'max:500'],
            'rsvp_settings.menu_courses.main.*.title' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_courses.main.*.description' => ['nullable', 'string', 'max:500'],
            'rsvp_settings.menu_courses.dessert.*.title' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_courses.dessert.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function isDarkHexColor(string $hex): bool
    {
        $normalized = ltrim($hex, '#');
        if (strlen($normalized) !== 6) {
            return false;
        }

        $red = hexdec(substr($normalized, 0, 2));
        $green = hexdec(substr($normalized, 2, 2));
        $blue = hexdec(substr($normalized, 4, 2));

        $luminance = (0.2126 * $red) + (0.7152 * $green) + (0.0722 * $blue);

        return $luminance <= 150;
    }
}
