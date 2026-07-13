@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Überblick über Termine und Buchbarkeit')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Ausstehende Termine</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Termine heute</p>
            <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $stats['today'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Buchbare Wochentage</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $stats['bookableDays'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Gesperrte Tage (ab heute)</p>
            <p class="mt-2 text-3xl font-bold text-slate-700">{{ $stats['blockedDates'] }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="font-semibold">Nächste Termine</h2>
                <a href="{{ route('admin.appointments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Alle anzeigen</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($upcomingAppointments as $appointment)
                    <a href="{{ route('admin.appointments.show', $appointment) }}" class="flex items-center justify-between px-6 py-4 transition hover:bg-slate-50">
                        <div>
                            <p class="font-medium">{{ $appointment->customer_name }}</p>
                            <p class="text-sm text-slate-500">{{ $appointment->service->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium">{{ $appointment->starts_at->format('d.m.Y') }}</p>
                            <p class="text-sm text-slate-500">{{ $appointment->starts_at->format('H:i') }} Uhr</p>
                        </div>
                    </a>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-slate-500">Keine anstehenden Termine.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold">Schnellzugriff</h2>
            <div class="mt-4 space-y-3">
                <a href="{{ route('admin.business-hours.edit') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="text-sm font-medium">Öffnungszeiten konfigurieren</span>
                    <span class="text-indigo-600">&rarr;</span>
                </a>
                <a href="{{ route('admin.blocked-dates.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="text-sm font-medium">Tage sperren</span>
                    <span class="text-indigo-600">&rarr;</span>
                </a>
                <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:border-indigo-200 hover:bg-indigo-50">
                    <span class="text-sm font-medium">Ausstehende Termine prüfen</span>
                    <span class="text-indigo-600">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
@endsection
