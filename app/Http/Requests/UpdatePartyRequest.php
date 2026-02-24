<?php

namespace App\Http\Requests;

use App\Models\Party;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartyRequest extends FormRequest
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
        /** @var Party $party */
        $party = $this->route('party');

        return [
            'display_name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'alpha', 'min:3', 'max:10', Rule::unique('parties', 'code')->ignore($party->id)],
            'max_guests' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
