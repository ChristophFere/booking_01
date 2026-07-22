<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Getränkeliste') – {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-emerald-50 text-slate-900 antialiased">
    <main class="mx-auto max-w-lg px-4 py-6 pb-32">
        @yield('content')
    </main>

    @stack('scripts')

    <button
        type="button"
        id="fab-add-drink"
        class="fixed z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-3xl font-light leading-none text-white shadow-[0_4px_8px_rgba(0,0,0,0.22),0_8px_24px_rgba(0,0,0,0.18)] transition hover:bg-emerald-700 hover:shadow-[0_6px_12px_rgba(0,0,0,0.24),0_12px_32px_rgba(0,0,0,0.2)] active:scale-95 touch-manipulation"
        style="bottom: max(1.5rem, env(safe-area-inset-bottom)); right: max(1rem, env(safe-area-inset-right));"
        aria-label="Neues Getränk hinzufügen"
    >
        +
    </button>
</body>
</html>
