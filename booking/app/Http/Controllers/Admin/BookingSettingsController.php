<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateBookingSettingsRequest;
use App\Services\BookingSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingSettingsController extends AdminController
{
    public function __construct(
        private BookingSettingsService $bookingSettings,
    ) {}

    public function index(): View
    {
        return view('admin.booking-settings.index', [
            'settings' => $this->bookingSettings->all(),
        ]);
    }

    public function update(UpdateBookingSettingsRequest $request): RedirectResponse
    {
        $this->bookingSettings->save($request->validated());

        return redirect()
            ->route('admin.booking-settings.index')
            ->with('success', 'Buchungszeitraum wurde gespeichert.');
    }
}
