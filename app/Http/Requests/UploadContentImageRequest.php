<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadContentImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $galleryFields = collect(range(0, 7))
            ->map(fn ($index) => "gallery.items.{$index}.image")
            ->all();

        return [
            'field' => [
                'required',
                'string',
                Rule::in([
                    'hero.image',
                    'welcome.image',
                    'story.image',
                    'details.image',
                    ...$galleryFields,
                ]),
            ],
            'image_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
        ];
    }
}
