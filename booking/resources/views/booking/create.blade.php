@extends('layouts.public')

@section('title', 'Termin buchen')

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h1 class="text-2xl font-bold">Termin buchen</h1>
        <p class="mt-2 text-sm text-slate-500">Wählen Sie eine Leistung und einen verfügbaren Termin. Keine Registrierung erforderlich.</p>

        @if ($services->isEmpty())
            <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Derzeit sind keine Leistungen buchbar.
            </p>
        @else
            <form method="POST" action="{{ route('booking.store') }}" class="mt-8 space-y-6">
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
                                data-duration="{{ $service->duration_minutes }}"
                                @selected(old('service_id') == $service->id)
                            >
                                {{ $service->name }} ({{ $service->duration_minutes }} Min.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="date" class="mb-1.5 block text-sm font-medium text-slate-700">Datum</label>
                        <input
                            type="date"
                            id="date"
                            min="{{ today()->format('Y-m-d') }}"
                            value="{{ old('starts_at') ? \Carbon\Carbon::parse(old('starts_at'))->format('Y-m-d') : '' }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="starts_at" class="mb-1.5 block text-sm font-medium text-slate-700">Uhrzeit</label>
                        <select
                            name="starts_at"
                            id="starts_at"
                            required
                            disabled
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:bg-slate-100"
                        >
                            <option value="">Zuerst Leistung und Datum wählen …</option>
                        </select>
                    </div>
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
        const dateInput = document.getElementById('date');
        const slotSelect = document.getElementById('starts_at');
        const slotsUrl = @json(route('booking.slots'));
        const oldSlot = @json(old('starts_at'));

        const loadSlots = async () => {
            const serviceId = serviceSelect.value;
            const date = dateInput.value;

            slotSelect.innerHTML = '<option value="">Lade Zeiten …</option>';
            slotSelect.disabled = true;

            if (!serviceId || !date) {
                slotSelect.innerHTML = '<option value="">Zuerst Leistung und Datum wählen …</option>';
                return;
            }

            try {
                const response = await fetch(`${slotsUrl}?service_id=${serviceId}&date=${date}`);
                const data = await response.json();

                if (!data.slots.length) {
                    slotSelect.innerHTML = '<option value="">Keine Termine verfügbar</option>';
                    return;
                }

                slotSelect.innerHTML = '<option value="">Bitte wählen …</option>';
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.value;
                    option.textContent = slot.label;
                    if (oldSlot === slot.value) {
                        option.selected = true;
                    }
                    slotSelect.appendChild(option);
                });
                slotSelect.disabled = false;
            } catch {
                slotSelect.innerHTML = '<option value="">Fehler beim Laden</option>';
            }
        };

        serviceSelect.addEventListener('change', loadSlots);
        dateInput.addEventListener('change', loadSlots);

        if (serviceSelect.value && dateInput.value) {
            loadSlots();
        }
    });
</script>
@endpush
