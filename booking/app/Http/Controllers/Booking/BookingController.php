<?php

namespace App\Http\Controllers\Booking;

use App\Enums\AppointmentStatus;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Appointment;
use App\Models\Service;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private BookingService $bookingService,
    ) {}

    public function create(): View
    {
        return view('booking.create', [
            'services' => Service::query()->active()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $service = Service::query()->active()->findOrFail($request->validated('service_id'));
        $startsAt = Carbon::parse($request->validated('starts_at'));

        Appointment::query()->create([
            'service_id' => $service->id,
            'user_id' => null,
            'customer_name' => $request->validated('customer_name'),
            'customer_email' => $request->validated('customer_email'),
            'customer_phone' => null,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMinutes($service->duration_minutes),
            'status' => AppointmentStatus::Pending,
            'notes' => $request->validated('notes'),
            'confirmation_token' => Str::random(64),
        ]);

        return redirect()
            ->route('booking.success')
            ->with('booking_success', true);
    }

    public function success(): View|RedirectResponse
    {
        if (! session('booking_success')) {
            return redirect()->route('booking.create');
        }

        return view('booking.success');
    }

    public function slots(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $service = Service::query()->active()->findOrFail($validated['service_id']);
        $date = Carbon::parse($validated['date']);

        if ($this->bookingService->isDateBlocked($date)) {
            return response()->json(['slots' => []]);
        }

        $slots = $this->bookingService->getAvailableSlots($service, $date);

        return response()->json([
            'slots' => $slots->map(fn (Carbon $slot) => [
                'value' => $slot->format('Y-m-d H:i:s'),
                'label' => $slot->format('H:i').' Uhr',
            ])->values(),
        ]);
    }
}
