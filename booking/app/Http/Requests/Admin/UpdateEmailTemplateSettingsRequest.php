<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->boolean('bodies_encoded')) {
            return;
        }

        $this->merge([
            'confirmation_body' => $this->decodeBody($this->input('confirmation_body')),
            'rejection_pending_body' => $this->decodeBody($this->input('rejection_pending_body')),
            'rejection_cancelled_body' => $this->decodeBody($this->input('rejection_cancelled_body')),
        ]);
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

    private function decodeBody(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $decoded = base64_decode($value, true);

        return $decoded !== false ? $decoded : $value;
    }
}
