<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreBlockedDateRequest;
use App\Models\BlockedDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlockedDateController extends AdminController
{
    public function index(): View
    {
        return view('admin.blocked-dates.index', [
            'blockedDates' => BlockedDate::query()
                ->where('date', '>=', today())
                ->orderBy('date')
                ->paginate(15),
        ]);
    }

    public function store(StoreBlockedDateRequest $request): RedirectResponse
    {
        BlockedDate::query()->create($request->validated());

        return redirect()
            ->route('admin.blocked-dates.index')
            ->with('success', 'Tag wurde gesperrt.');
    }

    public function destroy(BlockedDate $blockedDate): RedirectResponse
    {
        $blockedDate->delete();

        return redirect()
            ->route('admin.blocked-dates.index')
            ->with('success', 'Sperrung wurde entfernt.');
    }
}
