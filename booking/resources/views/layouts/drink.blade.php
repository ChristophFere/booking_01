<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Getränkeliste') – {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .drink-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0.875rem;
        }

        .drink-item__btn {
            flex: 0 0 3rem;
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            font-size: 1.5rem;
            line-height: 1;
            font-weight: 500;
            touch-action: manipulation;
            user-select: none;
            transition: transform 0.1s ease, background-color 0.15s ease;
        }

        .drink-item__btn:active {
            transform: scale(0.95);
        }

        .drink-item__btn--minus {
            border: 1px solid #6ee7b7;
            background-color: #ecfdf5;
            color: #065f46;
        }

        .drink-item__btn--plus {
            border: none;
            background-color: #059669;
            color: #fff;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.08);
        }

        .drink-item__center {
            flex: 1 1 auto;
            min-width: 0;
            text-align: center;
        }

        .drink-item__name {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.35;
            color: #1e293b;
            word-break: break-word;
        }

        .drink-item__qty {
            display: inline-flex;
            min-width: 2rem;
            align-items: center;
            justify-content: center;
            margin-top: 0.375rem;
            padding: 0.125rem 0.625rem;
            border-radius: 9999px;
            background-color: #d1fae5;
            color: #065f46;
            font-size: 1.125rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            box-shadow: inset 0 0 0 1px #a7f3d0;
        }
    </style>
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
