<?php

namespace App\Http\Requests;

use App\Models\Rsvp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Rsvp::STATUS_ATTENDING, Rsvp::STATUS_NOT_ATTENDING])],
            'attending_count' => ['required', 'integer', 'min:0', 'max:20'],
            'attending_guest_ids' => ['nullable', 'array'],
            'attending_guest_ids.*' => ['integer'],
            'meal_choices' => ['nullable', 'array'],
            'meal_choices.*.guest_id' => ['nullable', 'integer'],
            'meal_choices.*.guest_name' => ['required_with:meal_choices', 'string', 'max:120'],
            'meal_choices.*.meal' => ['required_with:meal_choices', 'string', 'max:60'],
            'meal_choices.*.selections' => ['nullable', 'array'],
            'meal_choices.*.selections.*' => ['nullable', 'string', 'max:120'],
            'dietary_restrictions' => ['nullable', 'string', 'max:1200'],
            'message' => ['nullable', 'string', 'max:1200'],
        ];
    }
}
