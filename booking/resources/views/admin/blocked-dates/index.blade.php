@extends('layouts.admin')

@section('title', 'Gesperrte Tage')
@section('heading', 'Gesperrte Tage')
@section('subheading', 'Einzelne Tage von der Buchung ausschließen (z. B. Feiertage, Urlaub)')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
            <h2 class="font-semibold">Tag sperren</h2>
            <form method="POST" action="{{ route('admin.blocked-dates.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="date" class="mb-1.5 block text-sm font-medium text-slate-700">Datum</label>
                    <input
                        type="date"
                        name="date"
                        id="date"
                        value="{{ old('date') }}"
                        min="{{ today()->format('Y-m-d') }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="reason" class="mb-1.5 block text-sm font-medium text-slate-700">Grund (optional)</label>
                    <input
                        type="text"
                        name="reason"
                        id="reason"
                        value="{{ old('reason') }}"
                        placeholder="z. B. Betriebsferien"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Tag sperren
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-semibold">Aktuelle Sperrungen</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Datum</th>
                            <th class="px-6 py-3">Grund</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($blockedDates as $blockedDate)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium">
                                    {{ $blockedDate->date->format('d.m.Y') }}
                                    <span class="ml-2 text-slate-400">({{ \App\Models\BusinessHour::dayNames()[$blockedDate->date->dayOfWeek] }})</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $blockedDate->reason ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.blocked-dates.destroy', $blockedDate) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-700" onclick="return confirm('Sperrung wirklich entfernen?')">
                                            Entfernen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">
                                    Keine gesperrten Tage vorhanden.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($blockedDates->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $blockedDates->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
