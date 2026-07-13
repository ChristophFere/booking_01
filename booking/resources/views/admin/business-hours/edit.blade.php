@extends('layouts.admin')

@section('title', 'Öffnungszeiten')
@section('heading', 'Öffnungszeiten')
@section('subheading', 'Legen Sie fest, an welchen Tagen und zu welchen Uhrzeiten Termine buchbar sind')

@section('content')
    <form method="POST" action="{{ route('admin.business-hours.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden border-b border-slate-100 bg-slate-50 px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500 md:grid md:grid-cols-12 md:gap-4">
                <div class="md:col-span-3">Wochentag</div>
                <div class="md:col-span-2">Buchbar</div>
                <div class="md:col-span-3">Von</div>
                <div class="md:col-span-3">Bis</div>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ($schedule as $day)
                    @php
                        $dayName = $dayNames[$day['day_of_week']];
                    @endphp
                    <div class="grid gap-4 px-6 py-5 md:grid-cols-12 md:items-center md:gap-4">
                        <div class="md:col-span-3">
                            <p class="font-medium">{{ $dayName }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="inline-flex cursor-pointer items-center gap-2">
                                <input type="hidden" name="hours[{{ $day['day_of_week'] }}][is_active]" value="0">
                                <input
                                    type="checkbox"
                                    name="hours[{{ $day['day_of_week'] }}][is_active]"
                                    value="1"
                                    @checked((bool) old("hours.{$day['day_of_week']}.is_active", $day['is_active']))
                                    class="day-toggle rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    data-day="{{ $day['day_of_week'] }}"
                                >
                                <span class="text-sm text-slate-600">Aktiv</span>
                            </label>
                        </div>

                        <div class="md:col-span-3">
                            <label class="mb-1 block text-xs text-slate-500 md:hidden">Von</label>
                            <input
                                type="time"
                                name="hours[{{ $day['day_of_week'] }}][opens_at]"
                                value="{{ old("hours.{$day['day_of_week']}.opens_at", $day['opens_at']) }}"
                                class="time-input w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                data-day="{{ $day['day_of_week'] }}"
                            >
                        </div>

                        <div class="md:col-span-3">
                            <label class="mb-1 block text-xs text-slate-500 md:hidden">Bis</label>
                            <input
                                type="time"
                                name="hours[{{ $day['day_of_week'] }}][closes_at]"
                                value="{{ old("hours.{$day['day_of_week']}.closes_at", $day['closes_at']) }}"
                                class="time-input w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                data-day="{{ $day['day_of_week'] }}"
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-6 py-4">
            <p class="text-sm text-slate-600">
                Nur aktive Tage mit gültigem Zeitfenster sind für Kunden buchbar.
            </p>
            <button
                type="submit"
                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
            >
                Speichern
            </button>
        </div>
    </form>

    <script>
        document.querySelectorAll('.day-toggle').forEach(toggle => {
            const day = toggle.dataset.day;
            const inputs = document.querySelectorAll(`.time-input[data-day="${day}"]`);

            const sync = () => {
                inputs.forEach(input => {
                    input.readOnly = !toggle.checked;
                    input.classList.toggle('opacity-50', !toggle.checked);
                    input.classList.toggle('pointer-events-none', !toggle.checked);
                });
            };

            toggle.addEventListener('change', sync);
            sync();
        });
    </script>
@endsection
