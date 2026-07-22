<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Requests\Admin\CancelAppointmentRequest;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class AppointmentController extends AdminController
{
    /** @var list<AppointmentStatus> */
    private const FILTER_STATUSES = [
        AppointmentStatus::Pending,
        AppointmentStatus::Confirmed,
        AppointmentStatus::Cancelled,
    ];

    public function __construct(
        private AppointmentMailService $appointmentMailService,
    ) {}

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

        $confirmed = false;

        try {
            DB::transaction(function () use ($appointment, &$confirmed): void {
                $locked = Appointment::query()
                    ->lockForUpdate()
                    ->findOrFail($appointment->id);

                if (! $locked->isPending()) {
                    return;
                }

                $locked->fill([
                    'status' => AppointmentStatus::Confirmed,
                    'confirmed_at' => now(),
                ]);
                $locked->save();

                $confirmed = true;
            });
        } catch (Throwable) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', 'Der Termin konnte nicht bestätigt werden.');
        }

        if (! $confirmed) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', 'Nur ausstehende Termine können bestätigt werden.');
        }

        $appointment->refresh();

        $mailSent = false;

        try {
            $this->appointmentMailService->sendConfirmationMail($appointment);
            $mailSent = true;
        } catch (Throwable) {
            //
        }

        if ($mailSent) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('success', 'Termin wurde bestätigt. Die Bestätigungsmail wurde versendet.');
        }

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('warning', 'Termin wurde bestätigt. Die Bestätigungsmail konnte nicht versendet werden.');
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
        $cancelled = false;

        try {
            DB::transaction(function () use ($request, $appointment, &$cancelled): void {
                $locked = Appointment::query()
                    ->lockForUpdate()
                    ->findOrFail($appointment->id);

                if ($locked->isCancelled()) {
                    return;
                }

                $locked->fill([
                    'status' => AppointmentStatus::Cancelled,
                    'admin_notes' => $request->validated('admin_notes'),
                    'cancelled_at' => $locked->cancelled_at ?? now(),
                ]);
                $locked->save();

                $cancelled = true;
            });
        } catch (Throwable) {
            $errorMessage = $wasConfirmed
                ? 'Der Termin konnte nicht storniert werden.'
                : 'Der Termin konnte nicht abgelehnt werden.';

            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', $errorMessage);
        }

        if (! $cancelled) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('error', 'Dieser Termin wurde bereits abgelehnt oder storniert.');
        }

        $appointment->refresh();

        $mailSent = false;

        try {
            $this->appointmentMailService->sendRejectionMail($appointment);
            $mailSent = true;
        } catch (Throwable) {
            //
        }

        $baseMessage = $wasConfirmed
            ? 'Termin wurde storniert.'
            : 'Termin wurde abgelehnt.';

        if ($mailSent) {
            return redirect()
                ->route('admin.appointments.show', $appointment)
                ->with('success', $baseMessage.' Die Absagemail wurde versendet.');
        }

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('warning', $baseMessage.' Die Absagemail konnte nicht versendet werden.');
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
