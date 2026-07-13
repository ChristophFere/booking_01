@extends('layouts.admin')

@section('title', 'Einstellungen')
@section('heading', 'Einstellungen')
@section('subheading', 'Passwort und E-Mail-Server für Terminbestätigungen')

@section('content')
    <div class="grid gap-8 lg:grid-cols-2">
        {{-- Passwort --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Passwort ändern</h2>
            <p class="mt-1 text-sm text-slate-500">Aktualisieren Sie Ihr Administrator-Passwort.</p>

            <form method="POST" action="{{ route('admin.settings.password.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-medium text-slate-700">Aktuelles Passwort</label>
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Neues Passwort</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                    <p class="mt-1 text-xs text-slate-500">Mindestens 8 Zeichen.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">Passwort bestätigen</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Passwort speichern
                </button>
            </form>
        </div>

        {{-- E-Mail --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">E-Mail-Server</h2>
            <p class="mt-1 text-sm text-slate-500">SMTP-Konfiguration für Terminbestätigungen.</p>

            <form method="POST" action="{{ route('admin.settings.mail.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="mailer" class="mb-1.5 block text-sm font-medium text-slate-700">Versandart</label>
                    <select
                        name="mailer"
                        id="mailer"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                        <option value="smtp" @selected(old('mailer', $mailSettings['mailer']) === 'smtp')>SMTP (Produktion)</option>
                        <option value="log" @selected(old('mailer', $mailSettings['mailer']) === 'log')>Log (nur Test)</option>
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="host" class="mb-1.5 block text-sm font-medium text-slate-700">SMTP-Host</label>
                        <input
                            type="text"
                            name="host"
                            id="host"
                            value="{{ old('host', $mailSettings['host']) }}"
                            placeholder="smtp.example.com"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="port" class="mb-1.5 block text-sm font-medium text-slate-700">Port</label>
                        <input
                            type="number"
                            name="port"
                            id="port"
                            value="{{ old('port', $mailSettings['port']) }}"
                            placeholder="587"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="encryption" class="mb-1.5 block text-sm font-medium text-slate-700">Verschlüsselung</label>
                        <select
                            name="encryption"
                            id="encryption"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                            <option value="" @selected(old('encryption', $mailSettings['encryption']) === null || old('encryption', $mailSettings['encryption']) === '')>Keine</option>
                            <option value="tls" @selected(old('encryption', $mailSettings['encryption']) === 'tls')>TLS</option>
                            <option value="ssl" @selected(old('encryption', $mailSettings['encryption']) === 'ssl')>SSL</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">Benutzername</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="{{ old('username', $mailSettings['username']) }}"
                            autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="mail_password" class="mb-1.5 block text-sm font-medium text-slate-700">Passwort</label>
                        <input
                            type="password"
                            name="password"
                            id="mail_password"
                            autocomplete="new-password"
                            placeholder="{{ $mailSettings['has_stored_password'] ? '•••••••• (gespeichert – leer lassen zum Beibehalten)' : 'SMTP-Passwort' }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="from_address" class="mb-1.5 block text-sm font-medium text-slate-700">Absender-E-Mail</label>
                        <input
                            type="email"
                            name="from_address"
                            id="from_address"
                            value="{{ old('from_address', $mailSettings['from_address']) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <div>
                        <label for="from_name" class="mb-1.5 block text-sm font-medium text-slate-700">Absendername</label>
                        <input
                            type="text"
                            name="from_name"
                            id="from_name"
                            value="{{ old('from_name', $mailSettings['from_name']) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>
                </div>

                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    E-Mail-Einstellungen speichern
                </button>
            </form>

            <div class="mt-8 border-t border-slate-100 pt-6">
                <h3 class="font-semibold">Test-E-Mail senden</h3>
                <p class="mt-1 text-sm text-slate-500">Prüfen Sie die Konfiguration mit einer Testnachricht.</p>

                <form method="POST" action="{{ route('admin.settings.mail.test') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                    @csrf

                    <div class="flex-1">
                        <label for="recipient" class="mb-1.5 block text-sm font-medium text-slate-700">Empfänger</label>
                        <input
                            type="email"
                            name="recipient"
                            id="recipient"
                            value="{{ old('recipient', auth()->user()->email) }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                        >
                    </div>

                    <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Test senden
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
