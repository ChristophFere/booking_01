<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Requests\Admin\CancelAppointmentRequest;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends AdminController
{
    /** @var list<AppointmentStatus> */
    private const FILTER_STATUSES = [
        AppointmentStatus::Pending,
        AppointmentStatus::Confirmed,
        AppointmentStatus::Cancelled,
    ];

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $appointments = Appointment::query()
            ->with('service')
            ->when(
                $status,
                fn ($query) => $query->where('status', $status),
                fn ($query) => $query->whereIn('status', array_map(
                    fn (AppointmentStatus $status) => $status->value,
                    self::FILTER_STATUSES,
                )),
            )
            ->orderByDesc('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'statuses' => self::FILTER_STATUSES,
            'currentStatus' => $status,
        ]);
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load('service');

        return view('admin.appointments.show', [
            'appointment' => $appointment,
        ]);
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        if (! $appointment->isPending()) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', 'Nur ausstehende Termine können bestätigt werden.');
        }

        $appointment->fill([
            'status' => AppointmentStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
        $appointment->save();

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Termin wurde bestätigt.');
    }

    public function cancel(CancelAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        if ($appointment->isCancelled()) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', 'Dieser Termin wurde bereits abgelehnt oder storniert.');
        }

        if (! $appointment->isPending() && ! $appointment->isConfirmed()) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', 'Dieser Termin kann nicht abgelehnt werden.');
        }

        $wasConfirmed = $appointment->isConfirmed();

        $appointment->fill([
            'status' => AppointmentStatus::Cancelled,
            'admin_notes' => $request->validated('admin_notes'),
            'cancelled_at' => $appointment->cancelled_at ?? now(),
        ]);
        $appointment->save();

        $message = $wasConfirmed
            ? 'Termin wurde storniert.'
            : 'Termin wurde abgelehnt.';

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', $message);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update([
            'admin_notes' => $request->validated('admin_notes'),
        ]);

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Interne Notiz wurde gespeichert.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Termin wurde gelöscht.');
    }
}
