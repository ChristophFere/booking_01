<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UpdateBusinessHoursRequest;
use App\Models\BusinessHour;
use App\Services\BusinessHourService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessHourController extends AdminController
{
    public function __construct(
        private BusinessHourService $businessHourService,
    ) {}

    public function edit(): View
    {
        return view('admin.business-hours.edit', [
            'schedule' => $this->businessHourService->getWeeklySchedule(),
            'dayNames' => BusinessHour::dayNames(),
        ]);
    }

    public function update(UpdateBusinessHoursRequest $request): RedirectResponse
    {
        $this->businessHourService->syncWeeklySchedule($request->validated('hours'));

        return redirect()
            ->route('admin.business-hours.edit')
            ->with('success', 'Öffnungszeiten wurden gespeichert.');
    }
}
