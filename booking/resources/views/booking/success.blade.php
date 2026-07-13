@extends('layouts.public')

@section('title', 'Buchung bestätigt')

@section('content')
    <div class="rounded-2xl border border-emerald-200 bg-white p-8 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-600">
            ✓
        </div>
        <h1 class="mt-4 text-2xl font-bold">Terminanfrage erhalten</h1>
        <p class="mt-2 text-sm text-slate-600">
            Vielen Dank! Ihre Buchungsanfrage wurde gespeichert und wird geprüft.
        </p>
        <a
            href="{{ route('booking.create') }}"
            class="mt-6 inline-block rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
        >
            Weiteren Termin buchen
        </a>
    </div>
@endsection
