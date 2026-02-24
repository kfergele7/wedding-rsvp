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
        return [
            'field' => ['required', 'string', Rule::in(['hero.image', 'welcome.image', 'story.image', 'details.image'])],
            'image_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
        ];
    }
}
