<?php

namespace App\Http\Requests;

use App\Support\WeddingPalettes;
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
            ],
            'content.theme.button_color' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'content.theme.palette' => ['nullable', Rule::in(WeddingPalettes::slugs())],
            'content.theme.layout' => ['nullable', Rule::in(['classic', 'modern', 'editorial'])],
            'content.guest_list' => ['nullable', 'array'],
            'content.guest_list.responseDeadline' => ['nullable', 'date'],
            'content.guest_list.evening_arrival_time' => ['nullable', 'date_format:H:i'],
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
            'rsvp_settings.menu_courses.*.name' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_courses.*.items' => ['nullable', 'array'],
            'rsvp_settings.menu_courses.*.items.*.title' => ['nullable', 'string', 'max:120'],
            'rsvp_settings.menu_courses.*.items.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }

}
