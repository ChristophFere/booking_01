<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Bitte geben Sie Ihr aktuelles Passwort ein.',
            'current_password.current_password' => 'Das aktuelle Passwort ist falsch.',
            'password.required' => 'Bitte geben Sie ein neues Passwort ein.',
            'password.confirmed' => 'Die Passwort-Bestätigung stimmt nicht überein.',
        ];
    }
}
