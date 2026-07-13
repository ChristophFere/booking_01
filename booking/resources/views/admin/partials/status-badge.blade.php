@php
    $colors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'confirmed' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'completed' => 'bg-slate-100 text-slate-700',
    ];
    $class = $colors[$status->value] ?? 'bg-slate-100 text-slate-700';
@endphp

<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $class }}">
    {{ $status->label() }}
</span>
