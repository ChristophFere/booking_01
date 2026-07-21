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
                    @if ($appointment->admin_notes)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Interne Notiz</dt>
                            <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-700">{{ $appointment->admin_notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            @if ($appointment->isPending())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-semibold">Anfrage bearbeiten</h2>
                    <p class="mt-2 text-sm text-slate-600">Bestätigen Sie die Terminanfrage oder lehnen Sie sie mit einer internen Begründung ab.</p>

                    <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                            Termin bestätigen
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}" class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                        @csrf
                        <div>
                            <label for="cancel_admin_notes_pending" class="mb-1.5 block text-sm font-medium text-slate-700">Interne Notiz zur Ablehnung <span class="text-red-600">*</span></label>
                            <textarea
                                name="admin_notes"
                                id="cancel_admin_notes_pending"
                                rows="4"
                                required
                                placeholder="Begründung für die Ablehnung …"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                            Termin ablehnen
                        </button>
                    </form>
                </div>
            @elseif ($appointment->isConfirmed())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-semibold">Termin stornieren</h2>
                    <p class="mt-2 text-sm text-slate-600">Ein bestätigter Termin kann storniert, aber nicht wieder auf „Ausstehend“ gesetzt werden.</p>

                    <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}" class="mt-4 space-y-3">
                        @csrf
                        <div>
                            <label for="cancel_admin_notes_confirmed" class="mb-1.5 block text-sm font-medium text-slate-700">Interne Notiz zur Stornierung <span class="text-red-600">*</span></label>
                            <textarea
                                name="admin_notes"
                                id="cancel_admin_notes_confirmed"
                                rows="4"
                                required
                                placeholder="Begründung für die Stornierung …"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >{{ old('admin_notes', $appointment->admin_notes) }}</textarea>
                            @error('admin_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700" onclick="return confirm('Termin wirklich stornieren?')">
                            Termin stornieren
                        </button>
                    </form>
                </div>

                @unless ($appointment->admin_notes)
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="font-semibold">Interne Notiz</h2>
                        <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PUT')
                            <textarea
                                name="admin_notes"
                                rows="4"
                                placeholder="Optionale interne Notiz …"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            >{{ old('admin_notes') }}</textarea>
                            <button type="submit" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Notiz speichern
                            </button>
                        </form>
                    </div>
                @endunless
            @endif

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
