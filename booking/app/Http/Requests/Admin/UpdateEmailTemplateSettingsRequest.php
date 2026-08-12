<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateSettingsRequest extends FormRequest
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
            'confirmation_subject' => ['required', 'string', 'max:255'],
            'confirmation_body' => ['required', 'string', 'max:10000'],
            'rejection_pending_subject' => ['required', 'string', 'max:255'],
            'rejection_pending_body' => ['required', 'string', 'max:10000'],
            'rejection_cancelled_subject' => ['required', 'string', 'max:255'],
            'rejection_cancelled_body' => ['required', 'string', 'max:10000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.required' => 'Dieses Feld ist erforderlich.',
            '*.max' => 'Der eingegebene Text ist zu lang.',
        ];
    }
}
