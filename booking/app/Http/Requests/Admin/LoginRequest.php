<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'password.required' => 'Bitte geben Sie Ihr Passwort ein.',
        ];
    }
}
