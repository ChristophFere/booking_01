<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date', 'after_or_equal:available_from'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'available_from.required' => 'Bitte geben Sie ein Startdatum an.',
            'available_until.required' => 'Bitte geben Sie ein Enddatum an.',
            'available_until.after_or_equal' => 'Das Enddatum muss am oder nach dem Startdatum liegen.',
        ];
    }
}
