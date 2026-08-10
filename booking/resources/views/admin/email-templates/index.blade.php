@extends('layouts.admin')

@section('title', 'E-Mail-Vorlagen')
@section('heading', 'E-Mail-Vorlagen')
@section('subheading', 'Texte für Terminbestätigungen und Absagen anpassen')

@section('content')
    <div class="max-w-3xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-600">
                Diese Vorlagen werden versendet, wenn Sie Termine bestätigen oder absagen.
                Sie können folgende Platzhalter verwenden:
            </p>
            <p class="mt-2 flex flex-wrap gap-2">
                @foreach ($placeholders as $placeholder)
                    <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $placeholder }}</code>
                @endforeach
            </p>
        </div>

        <form method="POST" action="{{ route('admin.email-templates.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Terminbestätigung (Zusage)</h2>
                <p class="mt-1 text-sm text-slate-500">Wird versendet, wenn ein Termin bestätigt wird.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="confirmation_subject" class="mb-1.5 block text-sm font-medium text-slate-700">Betreff</label>
                        <input
                            type="text"
                            name="confirmation_subject"
                            id="confirmation_subject"
                            value="{{ old('confirmation_subject', $templates['confirmation_subject']) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="confirmation_body" class="mb-1.5 block text-sm font-medium text-slate-700">Nachricht (HTML)</label>
                        <textarea
                            name="confirmation_body"
                            id="confirmation_body"
                            rows="12"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >{{ old('confirmation_body', $templates['confirmation_body']) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Terminabsage (Anfrage abgelehnt)</h2>
                <p class="mt-1 text-sm text-slate-500">Wird versendet, wenn eine noch nicht bestätigte Anfrage abgelehnt wird.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="rejection_pending_subject" class="mb-1.5 block text-sm font-medium text-slate-700">Betreff</label>
                        <input
                            type="text"
                            name="rejection_pending_subject"
                            id="rejection_pending_subject"
                            value="{{ old('rejection_pending_subject', $templates['rejection_pending_subject']) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="rejection_pending_body" class="mb-1.5 block text-sm font-medium text-slate-700">Nachricht (HTML)</label>
                        <textarea
                            name="rejection_pending_body"
                            id="rejection_pending_body"
                            rows="12"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >{{ old('rejection_pending_body', $templates['rejection_pending_body']) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Terminstornierung (bestätigter Termin)</h2>
                <p class="mt-1 text-sm text-slate-500">Wird versendet, wenn ein bereits bestätigter Termin storniert wird.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="rejection_cancelled_subject" class="mb-1.5 block text-sm font-medium text-slate-700">Betreff</label>
                        <input
                            type="text"
                            name="rejection_cancelled_subject"
                            id="rejection_cancelled_subject"
                            value="{{ old('rejection_cancelled_subject', $templates['rejection_cancelled_subject']) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="rejection_cancelled_body" class="mb-1.5 block text-sm font-medium text-slate-700">Nachricht (HTML)</label>
                        <textarea
                            name="rejection_cancelled_body"
                            id="rejection_cancelled_body"
                            rows="12"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 font-mono text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >{{ old('rejection_cancelled_body', $templates['rejection_cancelled_body']) }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                Vorlagen speichern
            </button>
        </form>
    </div>
@endsection
