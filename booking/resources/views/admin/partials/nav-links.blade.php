@php
    $links = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'match' => 'admin.dashboard'],
        ['route' => 'admin.appointments.index', 'label' => 'Termine', 'match' => 'admin.appointments.*'],
        ['route' => 'admin.business-hours.edit', 'label' => 'Öffnungszeiten', 'match' => 'admin.business-hours.*'],
        ['route' => 'admin.blocked-dates.index', 'label' => 'Gesperrte Tage', 'match' => 'admin.blocked-dates.*'],
        ['route' => 'admin.settings.index', 'label' => 'Einstellungen', 'match' => 'admin.settings.*'],
    ];
@endphp

@foreach ($links as $link)
    @php
        $active = request()->routeIs($link['match']);
        $base = ($mobile ?? false)
            ? 'whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition'
            : 'block rounded-lg px-3 py-2 text-sm font-medium transition';
        $classes = $active
            ? $base . ' bg-indigo-50 text-indigo-700'
            : $base . ' text-slate-600 hover:bg-slate-50 hover:text-slate-900';
    @endphp
    <a href="{{ route($link['route']) }}" class="{{ $classes }}">
        {{ $link['label'] }}
    </a>
@endforeach
