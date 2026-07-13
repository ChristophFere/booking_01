<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\BusinessHour;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function __invoke(): View
    {
        $upcomingAppointments = Appointment::query()
            ->with('service')
            ->where('starts_at', '>=', now())
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => [
                'pending' => Appointment::query()->where('status', AppointmentStatus::Pending)->count(),
                'today' => Appointment::query()
                    ->whereDate('starts_at', today())
                    ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])
                    ->count(),
                'bookableDays' => BusinessHour::query()->active()->count(),
                'blockedDates' => BlockedDate::query()->where('date', '>=', today())->count(),
            ],
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }
}
