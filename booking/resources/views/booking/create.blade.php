@extends('layouts.public')

@section('title', 'Termin buchen')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold">Termin buchen</h1>
        <p class="mt-2 text-sm text-slate-500">Wählen Sie eine Leistung und einen verfügbaren Termin im Kalender. Keine Registrierung erforderlich.</p>

        @if ($services->isEmpty())
            <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Derzeit sind keine Leistungen buchbar.
            </p>
        @else
            <form method="POST" action="{{ route('booking.store') }}" class="mt-8 space-y-6" id="booking-form">
                @csrf

                <div>
                    <label for="service_id" class="mb-1.5 block text-sm font-medium text-slate-700">Leistung</label>
                    <select
                        name="service_id"
                        id="service_id"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                        <option value="">Bitte wählen …</option>
                        @foreach ($services as $service)
                            <option
                                value="{{ $service->id }}"
                                @selected(old('service_id') == $service->id)
                            >
                                {{ $service->name }} ({{ $service->duration_minutes }} Min.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="calendar-section" class="hidden">
                    <label class="mb-3 block text-sm font-medium text-slate-700">Datum wählen</label>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <button type="button" id="prev-month" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-100" aria-label="Vorheriger Monat">&larr;</button>
                            <h2 id="calendar-title" class="text-sm font-semibold text-slate-800"></h2>
                            <button type="button" id="next-month" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-100" aria-label="Nächster Monat">&rarr;</button>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-500">
                            <div>Mo</div><div>Di</div><div>Mi</div><div>Do</div><div>Fr</div><div>Sa</div><div>So</div>
                        </div>

                        <div id="calendar-grid" class="mt-2 grid grid-cols-7 gap-1"></div>

                        <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500">
                            <span class="inline-flex items-center gap-1"><span class="h-3 w-3 rounded bg-indigo-600"></span> Verfügbar</span>
                            <span class="inline-flex items-center gap-1"><span class="h-3 w-3 rounded bg-white ring-1 ring-slate-200"></span> Nicht verfügbar</span>
                        </div>
                    </div>

                    <div id="slots-section" class="mt-4 hidden">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Uhrzeit wählen</label>
                        <p id="selected-date-label" class="mb-3 text-sm text-slate-500"></p>
                        <div id="slots-grid" class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6"></div>
                    </div>

                    <input type="hidden" name="starts_at" id="starts_at" value="{{ old('starts_at') }}" required>
                    <p id="slot-error" class="mt-2 hidden text-sm text-red-600">Bitte wählen Sie einen Termin im Kalender.</p>
                </div>

                <div id="calendar-placeholder" class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Bitte wählen Sie zuerst eine Leistung, um den Kalender anzuzeigen.
                </div>

                <div>
                    <label for="customer_name" class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                    <input
                        type="text"
                        name="customer_name"
                        id="customer_name"
                        value="{{ old('customer_name') }}"
                        required
                        autocomplete="name"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="customer_email" class="mb-1.5 block text-sm font-medium text-slate-700">E-Mail</label>
                    <input
                        type="email"
                        name="customer_email"
                        id="customer_email"
                        value="{{ old('customer_email') }}"
                        required
                        autocomplete="email"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="notes" class="mb-1.5 block text-sm font-medium text-slate-700">Notiz <span class="text-slate-400">(optional)</span></label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >{{ old('notes') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Termin anfragen
                </button>
            </form>
        @endif
    </div>
@endsection

@push('head')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const serviceSelect = document.getElementById('service_id');
        const calendarSection = document.getElementById('calendar-section');
        const calendarPlaceholder = document.getElementById('calendar-placeholder');
        const calendarGrid = document.getElementById('calendar-grid');
        const calendarTitle = document.getElementById('calendar-title');
        const slotsSection = document.getElementById('slots-section');
        const slotsGrid = document.getElementById('slots-grid');
        const selectedDateLabel = document.getElementById('selected-date-label');
        const startsAtInput = document.getElementById('starts_at');
        const slotError = document.getElementById('slot-error');
        const bookingForm = document.getElementById('booking-form');

        const calendarUrl = @json(route('booking.calendar'));
        const slotsUrl = @json(route('booking.slots'));
        const oldSlot = @json(old('starts_at'));

        const monthNames = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

        let currentMonth = new Date();
        currentMonth.setDate(1);
        let monthData = {};
        let selectedDate = oldSlot ? oldSlot.substring(0, 10) : null;
        let selectedSlotValue = oldSlot || null;

        if (oldSlot) {
            const d = new Date(oldSlot);
            if (!isNaN(d)) {
                currentMonth = new Date(d.getFullYear(), d.getMonth(), 1);
            }
        }

        const formatDateKey = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        const formatGermanDate = (dateKey) => {
            const [y, m, d] = dateKey.split('-');
            return `${d}.${m}.${y}`;
        };

        const renderCalendar = () => {
            calendarTitle.textContent = `${monthNames[currentMonth.getMonth()]} ${currentMonth.getFullYear()}`;
            calendarGrid.innerHTML = '';

            const year = currentMonth.getFullYear();
            const month = currentMonth.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);

            let startPad = firstDay.getDay() - 1;
            if (startPad < 0) startPad = 6;

            for (let i = 0; i < startPad; i++) {
                calendarGrid.appendChild(document.createElement('div'));
            }

            const todayKey = formatDateKey(new Date());

            for (let day = 1; day <= lastDay.getDate(); day++) {
                const date = new Date(year, month, day);
                const dateKey = formatDateKey(date);
                const info = monthData[dateKey] || { available: false, slots_count: 0 };

                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = day;
                button.dataset.date = dateKey;
                button.className = 'aspect-square rounded-lg text-sm font-medium transition';

                if (info.available) {
                    button.classList.add('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                } else {
                    button.classList.add('cursor-not-allowed', 'bg-white', 'text-slate-300', 'ring-1', 'ring-slate-100');
                    button.disabled = true;
                }

                if (dateKey === todayKey && info.available) {
                    button.classList.add('ring-2', 'ring-indigo-300', 'ring-offset-1');
                }

                if (dateKey === selectedDate) {
                    button.classList.add('ring-2', 'ring-slate-900', 'ring-offset-2');
                }

                if (info.available) {
                    button.addEventListener('click', () => selectDate(dateKey));
                }

                calendarGrid.appendChild(button);
            }
        };

        const loadMonth = async () => {
            const serviceId = serviceSelect.value;
            if (!serviceId) return;

            calendarGrid.innerHTML = '<div class="col-span-7 py-6 text-center text-sm text-slate-500">Kalender wird geladen …</div>';

            try {
                const response = await fetch(
                    `${calendarUrl}?service_id=${serviceId}&year=${currentMonth.getFullYear()}&month=${currentMonth.getMonth() + 1}`
                );
                const data = await response.json();
                monthData = data.days || {};
                renderCalendar();

                if (selectedDate && monthData[selectedDate]?.available) {
                    await loadSlots(selectedDate);
                } else {
                    slotsSection.classList.add('hidden');
                    if (!selectedSlotValue) startsAtInput.value = '';
                }
            } catch {
                calendarGrid.innerHTML = '<div class="col-span-7 py-6 text-center text-sm text-red-600">Kalender konnte nicht geladen werden.</div>';
            }
        };

        const loadSlots = async (dateKey) => {
            selectedDate = dateKey;
            renderCalendar();

            slotsGrid.innerHTML = '<div class="col-span-full py-4 text-center text-sm text-slate-500">Lade Zeiten …</div>';
            slotsSection.classList.remove('hidden');
            selectedDateLabel.textContent = formatGermanDate(dateKey);

            try {
                const response = await fetch(`${slotsUrl}?service_id=${serviceSelect.value}&date=${dateKey}`);
                const data = await response.json();

                slotsGrid.innerHTML = '';

                if (!data.slots.length) {
                    slotsGrid.innerHTML = '<p class="col-span-full text-sm text-slate-500">Keine Termine verfügbar.</p>';
                    startsAtInput.value = '';
                    selectedSlotValue = null;
                    return;
                }

                data.slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = slot.label;
                    btn.dataset.value = slot.value;
                    btn.className = 'rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-indigo-300 hover:bg-indigo-50';

                    if (selectedSlotValue === slot.value) {
                        btn.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700', 'ring-2', 'ring-indigo-500');
                    }

                    btn.addEventListener('click', () => selectSlot(slot.value, btn));
                    slotsGrid.appendChild(btn);
                });
            } catch {
                slotsGrid.innerHTML = '<p class="col-span-full text-sm text-red-600">Zeiten konnten nicht geladen werden.</p>';
            }
        };

        const selectDate = (dateKey) => {
            selectedSlotValue = null;
            startsAtInput.value = '';
            loadSlots(dateKey);
        };

        const selectSlot = (value, button) => {
            selectedSlotValue = value;
            startsAtInput.value = value;
            slotError.classList.add('hidden');

            slotsGrid.querySelectorAll('button').forEach(b => {
                b.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-700', 'ring-2', 'ring-indigo-500');
            });
            button.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700', 'ring-2', 'ring-indigo-500');
        };

        const toggleCalendar = () => {
            if (serviceSelect.value) {
                calendarSection.classList.remove('hidden');
                calendarPlaceholder.classList.add('hidden');
                loadMonth();
            } else {
                calendarSection.classList.add('hidden');
                calendarPlaceholder.classList.remove('hidden');
                slotsSection.classList.add('hidden');
                startsAtInput.value = '';
                selectedDate = null;
                selectedSlotValue = null;
            }
        };

        serviceSelect.addEventListener('change', () => {
            selectedDate = null;
            selectedSlotValue = null;
            startsAtInput.value = '';
            toggleCalendar();
        });

        document.getElementById('prev-month').addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            loadMonth();
        });

        document.getElementById('next-month').addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            loadMonth();
        });

        bookingForm.addEventListener('submit', (e) => {
            if (!startsAtInput.value) {
                e.preventDefault();
                slotError.classList.remove('hidden');
                calendarSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        toggleCalendar();
    });
</script>
@endpush
