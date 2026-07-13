@extends('layouts.admin')

@section('title', 'Termine')
@section('heading', 'Termine')
@section('subheading', 'Alle Buchungen verwalten und bearbeiten')

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a
            href="{{ route('admin.appointments.index') }}"
            class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ ! $currentStatus ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}"
        >
            Alle
        </a>
        @foreach ($statuses as $status)
            <a
                href="{{ route('admin.appointments.index', ['status' => $status->value]) }}"
                class="rounded-full px-4 py-1.5 text-sm font-medium transition {{ $currentStatus === $status->value ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}"
            >
                {{ $status->label() }}
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Kunde</th>
                        <th class="px-6 py-3">Leistung</th>
                        <th class="px-6 py-3">Termin</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($appointments as $appointment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $appointment->customer_name }}</p>
                                <p class="text-sm text-slate-500">{{ $appointment->customer_email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $appointment->service->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <p>{{ $appointment->starts_at->format('d.m.Y') }}</p>
                                <p class="text-slate-500">{{ $appointment->starts_at->format('H:i') }} – {{ $appointment->ends_at->format('H:i') }} Uhr</p>
                            </td>
                            <td class="px-6 py-4">
                                @include('admin.partials.status-badge', ['status' => $appointment->status])
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.appointments.show', $appointment) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                Keine Termine gefunden.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
@endsection
