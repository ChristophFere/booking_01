<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBusinessHoursRequest extends FormRequest
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
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.is_active' => ['boolean'],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hours = $this->input('hours', []);
            $activeDays = collect($hours)->filter(fn (array $hour) => (bool) ($hour['is_active'] ?? false));

            if ($activeDays->isEmpty()) {
                $validator->errors()->add('hours', 'Mindestens ein Wochentag muss buchbar sein.');

                return;
            }

            foreach ($hours as $day => $hour) {
                if (! ($hour['is_active'] ?? false)) {
                    continue;
                }

                if (empty($hour['opens_at'])) {
                    $validator->errors()->add("hours.{$day}.opens_at", 'Bitte Startzeit angeben.');
                }

                if (empty($hour['closes_at'])) {
                    $validator->errors()->add("hours.{$day}.closes_at", 'Bitte Endzeit angeben.');
                }

                if (
                    ! empty($hour['opens_at'])
                    && ! empty($hour['closes_at'])
                    && $hour['closes_at'] <= $hour['opens_at']
                ) {
                    $validator->errors()->add("hours.{$day}.closes_at", 'Die Endzeit muss nach der Startzeit liegen.');
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hours.required' => 'Öffnungszeiten fehlen.',
            'hours.size' => 'Es müssen genau 7 Wochentage konfiguriert werden.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $hours = $this->input('hours', []);
        $normalized = [];

        foreach (range(0, 6) as $day) {
            $data = $hours[$day] ?? [];

            $normalized[$day] = [
                'is_active' => $this->isDayActive($data),
                'opens_at' => $data['opens_at'] ?? '09:00',
                'closes_at' => $data['closes_at'] ?? '17:00',
            ];
        }

        $this->merge(['hours' => $normalized]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function isDayActive(array $data): bool
    {
        $value = $data['is_active'] ?? false;

        if (is_array($value)) {
            return in_array('1', $value, true) || in_array(1, $value, true);
        }

        return $value === '1' || $value === 1 || $value === true;
    }
}
