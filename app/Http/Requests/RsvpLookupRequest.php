<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RsvpLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->input('code'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'alpha', 'min:3', 'max:10'],
        ];
    }
}
