@extends('layouts.admin')

@section('title', 'Buchungszeitraum')
@section('heading', 'Buchungszeitraum')
@section('subheading', 'Zeitraum festlegen, in dem Kunden Termine buchen dürfen')

@section('content')
    <div class="max-w-xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-600">
                Nur Tage innerhalb dieses Zeitraums sind im Buchungskalender auswählbar.
                Liegen noch keine Einstellungen vor, gilt standardmäßig: ab heute bis in 6 Monate.
            </p>

            <form method="POST" action="{{ route('admin.booking-settings.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="available_from" class="mb-1.5 block text-sm font-medium text-slate-700">Buchbar ab</label>
                    <input
                        type="date"
                        name="available_from"
                        id="available_from"
                        value="{{ old('available_from', $settings['available_from']) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="available_until" class="mb-1.5 block text-sm font-medium text-slate-700">Buchbar bis</label>
                    <input
                        type="date"
                        name="available_until"
                        id="available_until"
                        value="{{ old('available_until', $settings['available_until']) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Speichern
                </button>
            </form>
        </div>
    </div>
@endsection
