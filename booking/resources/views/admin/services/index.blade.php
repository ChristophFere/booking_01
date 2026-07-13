@extends('layouts.admin')

@section('title', 'Leistungen')
@section('heading', 'Leistungen')
@section('subheading', 'Buchbare Leistungen für die öffentliche Terminseite verwalten')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-1">
            <h2 class="font-semibold">Neue Leistung</h2>
            <form method="POST" action="{{ route('admin.services.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="z. B. Erstberatung"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Beschreibung <span class="text-slate-400">(optional)</span></label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="duration_minutes" class="mb-1.5 block text-sm font-medium text-slate-700">Dauer (Minuten)</label>
                    <input
                        type="number"
                        name="duration_minutes"
                        id="duration_minutes"
                        value="{{ old('duration_minutes', 30) }}"
                        min="5"
                        max="480"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="price" class="mb-1.5 block text-sm font-medium text-slate-700">Preis (€) <span class="text-slate-400">(optional)</span></label>
                    <input
                        type="number"
                        name="price"
                        id="price"
                        value="{{ old('price') }}"
                        min="0"
                        step="0.01"
                        placeholder="0,00"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-700">Sortierung</label>
                    <input
                        type="number"
                        name="sort_order"
                        id="sort_order"
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Sofort buchbar (aktiv)
                </label>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Leistung anlegen
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-semibold">Alle Leistungen</h2>
                <p class="mt-1 text-sm text-slate-500">Nur aktive Leistungen erscheinen auf der Buchungsseite.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Dauer</th>
                            <th class="px-6 py-3">Preis</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($services as $service)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium">{{ $service->name }}</p>
                                    @if ($service->description)
                                        <p class="mt-1 text-sm text-slate-500">{{ Str::limit($service->description, 60) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $service->duration_minutes }} Min.</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($service->price !== null)
                                        {{ number_format($service->price, 2, ',', '.') }} €
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($service->is_active)
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Aktiv</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Inaktiv</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="font-medium text-indigo-600 hover:text-indigo-700">Bearbeiten</a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="mt-2 inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Leistung wirklich löschen?')">
                                            Löschen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                                    Noch keine Leistungen angelegt.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
