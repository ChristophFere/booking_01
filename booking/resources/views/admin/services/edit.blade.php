@extends('layouts.admin')

@section('title', 'Leistung bearbeiten')
@section('heading', 'Leistung bearbeiten')
@section('subheading', $service->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.services.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">&larr; Zurück zur Übersicht</a>
    </div>

    <div class="max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700">Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $service->name) }}"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            <div>
                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Beschreibung</label>
                <textarea
                    name="description"
                    id="description"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >{{ old('description', $service->description) }}</textarea>
            </div>

            <div>
                <label for="duration_minutes" class="mb-1.5 block text-sm font-medium text-slate-700">Dauer (Minuten)</label>
                <input
                    type="number"
                    name="duration_minutes"
                    id="duration_minutes"
                    value="{{ old('duration_minutes', $service->duration_minutes) }}"
                    min="5"
                    max="480"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            <div>
                <label for="price" class="mb-1.5 block text-sm font-medium text-slate-700">Preis (€)</label>
                <input
                    type="number"
                    name="price"
                    id="price"
                    value="{{ old('price', $service->price) }}"
                    min="0"
                    step="0.01"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            <div>
                <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-700">Sortierung</label>
                <input
                    type="number"
                    name="sort_order"
                    id="sort_order"
                    value="{{ old('sort_order', $service->sort_order) }}"
                    min="0"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="hidden" name="is_active" value="0">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    @checked(old('is_active', $service->is_active))
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                >
                Buchbar (aktiv)
            </label>

            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                Speichern
            </button>
        </form>
    </div>
@endsection
