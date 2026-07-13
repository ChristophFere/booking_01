<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Termin buchen') – {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-16 max-w-3xl items-center justify-between px-4">
            <a href="{{ route('booking.create') }}" class="text-lg font-semibold text-indigo-600">
                {{ config('app.name', 'Terminbuchung') }}
            </a>
            <a href="{{ url('/') }}" class="text-sm text-slate-500 hover:text-slate-700">Startseite</a>
        </div>
    </header>

    <main class="mx-auto max-w-3xl px-4 py-8">
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
