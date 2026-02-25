<?php

namespace App\Http\Requests;

use App\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $code = $this->input('code');

        $this->merge([
            'code' => $code ? strtoupper(trim((string) $code)) : null,
        ]);
    }

    public function rules(): array
    {
        $siteId = app(TenantContext::class)->siteId();

        return [
            'display_name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'alpha',
                'min:3',
                'max:10',
                Rule::unique('parties', 'code')->where(fn ($query) => $query->where('site_id', $siteId)),
            ],
            'max_guests' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
