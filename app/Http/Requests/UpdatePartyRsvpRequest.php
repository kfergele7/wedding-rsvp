<?php

namespace App\Http\Requests;

use App\Models\Rsvp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartyRsvpRequest extends FormRequest
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
            'meal_choices' => ['nullable', 'array'],
            'meal_choices.*.guest_name' => ['required_with:meal_choices', 'string', 'max:120'],
            'meal_choices.*.meal' => ['required_with:meal_choices', 'string', 'max:60'],
            'dietary_restrictions' => ['nullable', 'string', 'max:1200'],
            'message' => ['nullable', 'string', 'max:1200'],
        ];
    }
}
