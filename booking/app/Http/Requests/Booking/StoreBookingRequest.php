<?php

namespace App\Http\Requests\Booking;

use App\Models\Service;
use App\Services\BookingService;
use App\Services\BookingSettingsService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
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
            'service_id' => ['required', 'exists:services,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'starts_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $service = Service::query()->active()->find($this->input('service_id'));

            if (! $service) {
                $validator->errors()->add('service_id', 'Die gewählte Leistung ist nicht verfügbar.');

                return;
            }

            $startsAt = Carbon::parse($this->input('starts_at'));

            if (! app(BookingSettingsService::class)->isDateBookable($startsAt)) {
                $validator->errors()->add('starts_at', 'Der gewählte Termin liegt außerhalb des Buchungszeitraums.');

                return;
            }

            if (! app(BookingService::class)->isSlotAvailable($service, $startsAt)) {
                $validator->errors()->add('starts_at', 'Der gewählte Termin ist nicht verfügbar.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => 'Bitte wählen Sie eine Leistung.',
            'customer_name.required' => 'Bitte geben Sie Ihren Namen ein.',
            'customer_email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'customer_email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'starts_at.required' => 'Bitte wählen Sie einen Termin.',
            'starts_at.after' => 'Der Termin muss in der Zukunft liegen.',
        ];
    }
}
