@extends('layouts.drink')

@section('title', 'Getränkeliste')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-emerald-900">Getränkeliste</h1>
        <p class="mt-1 text-sm text-emerald-700">Gemeinsam Getränke für euer Event sammeln</p>
    </div>

    <div class="mb-6 flex flex-col items-center text-center">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Präsentiert von</p>
        <a
            href="https://musiq.one"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-2 inline-block rounded-lg px-2 py-1 transition hover:opacity-90"
        >
            <img
                src="{{ asset('images/musiqone-logo.png') }}"
                alt="musiq one"
                width="136"
                height="106"
                class="h-14 w-auto"
                loading="lazy"
            >
        </a>
    </div>

    <div class="mb-4 rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-center shadow-sm">
        <p class="text-sm font-medium text-slate-500">Gesamte Getränke</p>
        <p id="total-quantity" class="text-3xl font-bold text-emerald-700">0</p>
    </div>

    <div class="mb-4 flex items-center justify-between gap-2">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Getränke</h2>
        <div class="flex rounded-lg border border-emerald-200 bg-white p-0.5 text-xs">
            <button type="button" data-sort="name" class="sort-button rounded-md px-3 py-1.5 font-medium transition">Name</button>
            <button type="button" data-sort="quantity" class="sort-button rounded-md px-3 py-1.5 font-medium transition">Menge</button>
        </div>
    </div>

    <ul id="drink-list" class="space-y-3">
        <li id="drink-list-empty" class="rounded-2xl border border-dashed border-emerald-200 bg-white px-4 py-8 text-center text-sm text-slate-500">
            Noch keine Getränke. Unten rechts auf + tippen, um ein Getränk hinzuzufügen.
        </li>
    </ul>

    <template id="drink-item-template">
        <li class="drink-item rounded-2xl border border-emerald-200 bg-white shadow-sm" data-drink-item>
            <button type="button" data-action="decrement" class="drink-item__btn drink-item__btn--minus" aria-label="Menge verringern">−</button>
            <div class="drink-item__center">
                <p class="drink-item__name"></p>
                <span class="drink-item__qty"></span>
            </div>
            <button type="button" data-action="increment" class="drink-item__btn drink-item__btn--plus" aria-label="Menge erhöhen">+</button>
        </li>
    </template>

    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
        <button
            type="button"
            id="copy-order-button"
            class="flex-1 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-[0.98]"
        >
            Bestellliste kopieren
        </button>
        <button
            type="button"
            id="clear-list-button"
            class="flex-1 rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50 active:scale-[0.98]"
        >
            Liste leeren
        </button>
    </div>

    <div
        id="add-drink-dialog"
        class="fixed inset-0 z-[60] hidden items-end justify-center p-4 sm:items-center"
        role="dialog"
        aria-modal="true"
        aria-labelledby="add-drink-dialog-title"
    >
        <div id="add-drink-backdrop" class="absolute inset-0 bg-slate-900/40"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-emerald-200 bg-white p-6 shadow-xl">
            <h2 id="add-drink-dialog-title" class="text-lg font-semibold text-emerald-900">Getränk hinzufügen</h2>
            <p class="mt-1 text-sm text-slate-500">Freitext eingeben, z. B. Cola, Wasser, Weizen</p>

            <form id="add-form" class="mt-4">
                <input
                    type="text"
                    id="drink-name"
                    name="name"
                    placeholder="Getränk eingeben …"
                    autocomplete="off"
                    maxlength="255"
                    enterkeyhint="done"
                    class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-4 text-base focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                >
                <p id="add-error" class="mt-2 hidden text-sm text-red-600"></p>
                <div class="mt-4 flex gap-3">
                    <button
                        type="button"
                        id="dialog-cancel-button"
                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Abbrechen
                    </button>
                    <button
                        type="submit"
                        id="dialog-submit-button"
                        class="flex-1 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700"
                    >
                        Hinzufügen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <p id="toast" class="pointer-events-none fixed bottom-28 left-1/2 z-50 hidden max-w-[90vw] -translate-x-1/2 rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-lg"></p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const itemsUrl = @json(route('drink.items.index'));
            const storeUrl = @json(route('drink.items.store'));
            const destroyAllUrl = @json(route('drink.items.destroy-all'));
            const orderTextUrl = @json(route('drink.order-text'));
            const incrementUrlTemplate = @json(route('drink.items.increment', ['drinkItem' => '__ID__']));
            const decrementUrlTemplate = @json(route('drink.items.decrement', ['drinkItem' => '__ID__']));

            let currentSort = 'name';

            const totalQuantityEl = document.getElementById('total-quantity');
            const drinkListEl = document.getElementById('drink-list');
            const drinkListEmptyEl = document.getElementById('drink-list-empty');
            const drinkItemTemplate = document.getElementById('drink-item-template');
            const addForm = document.getElementById('add-form');
            const drinkNameInput = document.getElementById('drink-name');
            const fabAddDrink = document.getElementById('fab-add-drink');
            const addDialog = document.getElementById('add-drink-dialog');
            const addDialogBackdrop = document.getElementById('add-drink-backdrop');
            const dialogCancelButton = document.getElementById('dialog-cancel-button');
            const addErrorEl = document.getElementById('add-error');
            const toastEl = document.getElementById('toast');
            const sortButtons = document.querySelectorAll('.sort-button');

            const itemUrl = (template, id) => template.replace('__ID__', id);

            const headers = () => ({
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            });

            const showToast = (message) => {
                toastEl.textContent = message;
                toastEl.classList.remove('hidden');
                clearTimeout(showToast.timeout);
                showToast.timeout = setTimeout(() => toastEl.classList.add('hidden'), 2500);
            };

            const openAddDialog = () => {
                addErrorEl.classList.add('hidden');
                addErrorEl.textContent = '';
                addDialog.classList.remove('hidden');
                addDialog.classList.add('flex');
                drinkNameInput.focus();
            };

            const closeAddDialog = () => {
                addDialog.classList.add('hidden');
                addDialog.classList.remove('flex');
                drinkNameInput.value = '';
                addErrorEl.classList.add('hidden');
                addErrorEl.textContent = '';
            };

            const updateSortButtons = () => {
                sortButtons.forEach((button) => {
                    const active = button.dataset.sort === currentSort;
                    button.classList.toggle('bg-emerald-600', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('text-slate-600', !active);
                });
            };

            const renderItems = (items) => {
                drinkListEl.querySelectorAll('[data-drink-item]').forEach((node) => node.remove());

                if (!items.length) {
                    drinkListEmptyEl.classList.remove('hidden');
                    return;
                }

                drinkListEmptyEl.classList.add('hidden');

                items.forEach((item) => {
                    const li = drinkItemTemplate.content.firstElementChild.cloneNode(true);
                    li.querySelector('[data-action="decrement"]').dataset.id = item.id;
                    li.querySelector('[data-action="increment"]').dataset.id = item.id;
                    li.querySelector('.drink-item__name').textContent = item.name;
                    li.querySelector('.drink-item__qty').textContent = item.quantity;
                    drinkListEl.appendChild(li);
                });
            };

            const applyPayload = (payload) => {
                totalQuantityEl.textContent = payload.total_quantity;
                renderItems(payload.items);
            };

            const loadItems = async () => {
                const response = await fetch(`${itemsUrl}?sort=${currentSort}`, { headers: headers() });
                if (!response.ok) {
                    throw new Error('Laden fehlgeschlagen');
                }
                applyPayload(await response.json());
            };

            const mutate = async (url, method = 'POST') => {
                const response = await fetch(url, { method, headers: headers() });
                if (!response.ok) {
                    throw new Error('Aktion fehlgeschlagen');
                }
                applyPayload(await response.json());
            };

            const submitDrink = async () => {
                addErrorEl.classList.add('hidden');
                addErrorEl.textContent = '';

                const name = drinkNameInput.value.trim();
                if (!name) {
                    addErrorEl.textContent = 'Bitte geben Sie ein Getränk ein.';
                    addErrorEl.classList.remove('hidden');
                    return;
                }

                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: headers(),
                    body: JSON.stringify({ name }),
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    addErrorEl.textContent = data.errors?.name?.[0] || data.message || 'Getränk konnte nicht hinzugefügt werden.';
                    addErrorEl.classList.remove('hidden');
                    return;
                }

                applyPayload(await response.json());
                closeAddDialog();
                showToast('Getränk hinzugefügt');
            };

            fabAddDrink.addEventListener('click', openAddDialog);
            dialogCancelButton.addEventListener('click', closeAddDialog);
            addDialogBackdrop.addEventListener('click', closeAddDialog);

            addForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                try {
                    await submitDrink();
                } catch {
                    addErrorEl.textContent = 'Getränk konnte nicht hinzugefügt werden.';
                    addErrorEl.classList.remove('hidden');
                }
            });

            drinkListEl.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-action]');
                if (!button) return;

                const id = button.dataset.id;
                const action = button.dataset.action;
                const url = action === 'increment'
                    ? itemUrl(incrementUrlTemplate, id)
                    : itemUrl(decrementUrlTemplate, id);

                try {
                    await mutate(url);
                } catch {
                    showToast('Aktion fehlgeschlagen');
                }
            });

            sortButtons.forEach((button) => {
                button.addEventListener('click', async () => {
                    currentSort = button.dataset.sort;
                    updateSortButtons();
                    try {
                        await loadItems();
                    } catch {
                        showToast('Liste konnte nicht geladen werden');
                    }
                });
            });

            document.getElementById('clear-list-button').addEventListener('click', async () => {
                if (!confirm('Möchten Sie wirklich alle Getränke aus der Liste entfernen?')) {
                    return;
                }

                try {
                    await mutate(destroyAllUrl, 'DELETE');
                    showToast('Liste wurde geleert');
                } catch {
                    showToast('Liste konnte nicht geleert werden');
                }
            });

            document.getElementById('copy-order-button').addEventListener('click', async () => {
                const response = await fetch(`${orderTextUrl}?sort=${currentSort}`, { headers: headers() });
                const data = await response.json();

                if (!data.text) {
                    showToast('Keine Getränke zum Kopieren');
                    return;
                }

                try {
                    await navigator.clipboard.writeText(data.text);
                    showToast('Bestellliste kopiert');
                } catch {
                    showToast('Kopieren nicht möglich');
                }
            });

            updateSortButtons();
            loadItems().catch(() => showToast('Liste konnte nicht geladen werden'));
        });
    </script>
@endpush
