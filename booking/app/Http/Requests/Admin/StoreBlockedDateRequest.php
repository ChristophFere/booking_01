<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlockedDateRequest extends FormRequest
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
            'date' => ['required', 'date', 'after_or_equal:today', 'unique:blocked_dates,date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Bitte wählen Sie ein Datum.',
            'date.unique' => 'Dieses Datum ist bereits gesperrt.',
            'date.after_or_equal' => 'Das Datum darf nicht in der Vergangenheit liegen.',
        ];
    }
}
