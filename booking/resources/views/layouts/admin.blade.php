<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') – {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block">
            <div class="flex h-16 items-center border-b border-slate-200 px-6">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-indigo-600">
                    {{ config('app.name', 'Terminbuchung') }}
                </a>
            </div>
            <nav class="space-y-1 p-4">
                @include('admin.partials.nav-links')
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Mobile header --}}
            <header class="border-b border-slate-200 bg-white lg:hidden">
                <div class="flex h-16 items-center justify-between px-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-indigo-600">
                        {{ config('app.name', 'Terminbuchung') }}
                    </a>
                    <span class="text-sm text-slate-500">{{ auth()->user()->name }}</span>
                </div>
                <nav class="flex gap-1 overflow-x-auto border-t border-slate-100 px-2 py-2">
                    @include('admin.partials.nav-links', ['mobile' => true])
                </nav>
            </header>

            {{-- Desktop top bar --}}
            <header class="hidden h-16 items-center justify-between border-b border-slate-200 bg-white px-8 lg:flex">
                <div>
                    <h1 class="text-lg font-semibold">@yield('heading', 'Dashboard')</h1>
                    @hasSection('subheading')
                        <p class="text-sm text-slate-500">@yield('subheading')</p>
                    @endif
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50">
                            Abmelden
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @include('admin.partials.flash')

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
