<?php

namespace App\Http\Requests;

use App\Models\Party;
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
            'guest_type' => ['nullable', Rule::in(Party::guestTypes())],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'alpha',
                'min:3',
                'max:10',
                Rule::unique('parties', 'code')->where(fn ($query) => $query->where('site_id', $siteId)),
            ],
            'max_guests' => ['nullable', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'guests' => ['nullable', 'array', 'max:20'],
            'guests.*.first_name' => ['required_with:guests', 'string', 'max:120'],
            'guests.*.last_name' => ['required_with:guests', 'string', 'max:120'],
            'guests.*.is_child' => ['nullable', 'boolean'],
            'guests.*.allow_plus_one' => ['nullable', 'boolean'],
        ];
    }
}
