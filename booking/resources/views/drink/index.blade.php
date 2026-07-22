@extends('layouts.drink')

@section('title', 'Getränkeliste')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-emerald-900">Getränkeliste</h1>
        <p class="mt-1 text-sm text-emerald-700">Gemeinsam Getränke für euer Event sammeln</p>
    </div>

    <div class="mb-4 rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-center shadow-sm">
        <p class="text-sm font-medium text-slate-500">Gesamte Getränke</p>
        <p id="total-quantity" class="text-3xl font-bold text-emerald-700">0</p>
    </div>

    <form id="add-form" class="mb-2 scroll-mt-6">
        <input
            type="text"
            id="drink-name"
            name="name"
            placeholder="Getränk eingeben …"
            autocomplete="off"
            maxlength="255"
            enterkeyhint="done"
            class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-4 text-base shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
        >
    </form>
    <p id="add-error" class="mb-4 hidden text-sm text-red-600"></p>

    <div class="mb-4 flex items-center justify-between gap-2">
        <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Getränke</h2>
        <div class="flex rounded-lg border border-emerald-200 bg-white p-0.5 text-xs">
            <button type="button" data-sort="name" class="sort-button rounded-md px-3 py-1.5 font-medium transition">Name</button>
            <button type="button" data-sort="quantity" class="sort-button rounded-md px-3 py-1.5 font-medium transition">Menge</button>
        </div>
    </div>

    <ul id="drink-list" class="space-y-3">
        <li id="drink-list-empty" class="rounded-2xl border border-dashed border-emerald-200 bg-white px-4 py-8 text-center text-sm text-slate-500">
            Noch keine Getränke. Unten rechts auf + tippen und Getränk eingeben.
        </li>
    </ul>

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

    <p id="toast" class="pointer-events-none fixed bottom-28 left-1/2 z-50 hidden max-w-[90vw] -translate-x-1/2 rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-lg"></p>

    <script>
        (() => {
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
            const addForm = document.getElementById('add-form');
            const drinkNameInput = document.getElementById('drink-name');
            const fabAddDrink = document.getElementById('fab-add-drink');
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
                    const li = document.createElement('li');
                    li.dataset.drinkItem = 'true';
                    li.className = 'flex min-h-[5.5rem] items-stretch overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm';
                    li.innerHTML = `
                        <button
                            type="button"
                            data-action="decrement"
                            data-id="${item.id}"
                            class="flex w-[5.25rem] shrink-0 items-center justify-center border-r border-emerald-200 bg-emerald-50 text-5xl font-normal leading-none text-emerald-800 transition hover:bg-emerald-100 active:bg-emerald-200 touch-manipulation select-none"
                            aria-label="Menge verringern"
                        >−</button>
                        <div class="flex min-w-0 flex-1 flex-col items-center justify-center px-2 py-3 text-center">
                            <span class="text-3xl font-bold tabular-nums leading-none text-emerald-800">${item.quantity}</span>
                            <span class="mt-1.5 text-lg font-medium leading-snug text-slate-800">${escapeHtml(item.name)}</span>
                        </div>
                        <button
                            type="button"
                            data-action="increment"
                            data-id="${item.id}"
                            class="flex w-[5.25rem] shrink-0 items-center justify-center bg-emerald-600 text-5xl font-normal leading-none text-white transition hover:bg-emerald-700 active:bg-emerald-800 touch-manipulation select-none"
                            aria-label="Menge erhöhen"
                        >+</button>
                    `;
                    drinkListEl.appendChild(li);
                });
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value;
                return div.innerHTML;
            };

            const applyPayload = (payload) => {
                totalQuantityEl.textContent = payload.total_quantity;
                renderItems(payload.items);
            };

            const loadItems = async () => {
                const response = await fetch(`${itemsUrl}?sort=${currentSort}`, { headers: headers() });
                const payload = await response.json();
                applyPayload(payload);
            };

            const mutate = async (url, method = 'POST') => {
                const response = await fetch(url, { method, headers: headers() });

                if (!response.ok) {
                    throw new Error('Aktion fehlgeschlagen');
                }

                const payload = await response.json();
                applyPayload(payload);
            };

            fabAddDrink.addEventListener('click', () => {
                addErrorEl.classList.add('hidden');
                addForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                drinkNameInput.focus({ preventScroll: true });
            });

            addForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                addErrorEl.classList.add('hidden');

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

                drinkNameInput.value = '';
                applyPayload(await response.json());
                drinkNameInput.focus();
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
                    await loadItems();
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
        })();
    </script>
@endsection
