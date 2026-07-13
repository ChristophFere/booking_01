<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends AdminController
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $appointments = Appointment::query()
            ->with('service')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'statuses' => AppointmentStatus::cases(),
            'currentStatus' => $status,
        ]);
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load('service');

        return view('admin.appointments.show', [
            'appointment' => $appointment,
            'statuses' => AppointmentStatus::cases(),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();
        $status = AppointmentStatus::from($data['status']);

        $appointment->fill([
            'status' => $status,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($status === AppointmentStatus::Confirmed && ! $appointment->confirmed_at) {
            $appointment->confirmed_at = now();
        }

        if ($status === AppointmentStatus::Cancelled && ! $appointment->cancelled_at) {
            $appointment->cancelled_at = now();
        }

        $appointment->save();

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Termin wurde aktualisiert.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Termin wurde gelöscht.');
    }
}
