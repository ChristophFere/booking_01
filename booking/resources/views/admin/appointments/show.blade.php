@extends('layouts.admin')

@section('title', 'Termin #' . $appointment->id)
@section('heading', 'Termin #' . $appointment->id)
@section('subheading', $appointment->customer_name . ' – ' . $appointment->starts_at->format('d.m.Y H:i') . ' Uhr')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.appointments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">&larr; Zurück zur Übersicht</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Termindetails</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Leistung</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $appointment->service->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Status</dt>
                        <dd class="mt-1">@include('admin.partials.status-badge', ['status' => $appointment->status])</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Datum</dt>
                        <dd class="mt-1 text-sm">{{ $appointment->starts_at->format('d.m.Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Uhrzeit</dt>
                        <dd class="mt-1 text-sm">{{ $appointment->starts_at->format('H:i') }} – {{ $appointment->ends_at->format('H:i') }} Uhr</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Kunde</dt>
                        <dd class="mt-1 text-sm font-medium">{{ $appointment->customer_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">E-Mail</dt>
                        <dd class="mt-1 text-sm"><a href="mailto:{{ $appointment->customer_email }}" class="text-indigo-600">{{ $appointment->customer_email }}</a></dd>
                    </div>
                    @if ($appointment->customer_phone)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Telefon</dt>
                            <dd class="mt-1 text-sm">{{ $appointment->customer_phone }}</dd>
                        </div>
                    @endif
                    @if ($appointment->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Kundennotiz</dt>
                            <dd class="mt-1 text-sm text-slate-700">{{ $appointment->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Bearbeiten</h2>
                <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">Status</label>
                        <select
                            name="status"
                            id="status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $appointment->status->value) === $status->value)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="admin_notes" class="mb-1.5 block text-sm font-medium text-slate-700">Interne Notiz</label>
                        <textarea
                            name="admin_notes"
                            id="admin_notes"
                            rows="4"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >{{ old('admin_notes', $appointment->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Speichern
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
                <h2 class="font-semibold text-red-800">Termin löschen</h2>
                <p class="mt-2 text-sm text-red-700">Diese Aktion kann nicht rückgängig gemacht werden.</p>
                <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100" onclick="return confirm('Termin wirklich löschen?')">
                        Termin löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
