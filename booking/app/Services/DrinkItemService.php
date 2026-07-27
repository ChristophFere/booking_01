<?php

namespace App\Services;

use App\Models\DrinkItem;
use Illuminate\Support\Collection;

class DrinkItemService
{
    public function listKey(): string
    {
        return DrinkItem::DEFAULT_LIST_KEY;
    }

    /**
     * @return Collection<int, DrinkItem>
     */
    public function getItems(string $sort = 'name'): Collection
    {
        $query = DrinkItem::query()
            ->where('list_key', $this->listKey());

        if ($sort === 'quantity') {
            $query->orderByDesc('quantity')->orderBy('name');
        } else {
            $query->orderBy('name');
        }

        return $query->get();
    }

    public function totalQuantity(): int
    {
        return (int) DrinkItem::query()
            ->where('list_key', $this->listKey())
            ->sum('quantity');
    }

    public function addItem(string $name): DrinkItem
    {
        $name = trim($name);
        $nameKey = DrinkItem::normalizeNameKey($name);
        $listKey = $this->listKey();

        $existing = DrinkItem::query()
            ->where('list_key', $listKey)
            ->where('name_key', $nameKey)
            ->first();

        if ($existing) {
            $existing->increment('quantity');

            return $existing->fresh();
        }

        return DrinkItem::query()->create([
            'list_key' => $listKey,
            'name' => $name,
            'name_key' => $nameKey,
            'quantity' => 1,
        ]);
    }

    public function increment(DrinkItem $item): DrinkItem
    {
        $this->ensureDefaultList($item);

        $item->increment('quantity');

        return $item->fresh();
    }

    public function decrement(DrinkItem $item): ?DrinkItem
    {
        $this->ensureDefaultList($item);

        if ($item->quantity <= 1) {
            $item->delete();

            return null;
        }

        $item->decrement('quantity');

        return $item->fresh();
    }

    public function clearAll(): void
    {
        DrinkItem::query()
            ->where('list_key', $this->listKey())
            ->delete();
    }

    /**
     * @return array{
     *     items: list<array{id: int, name: string, quantity: int}>,
     *     total_quantity: int,
     *     sort: string
     * }
     */
    public function toPayload(string $sort = 'name'): array
    {
        $items = $this->getItems($sort);

        return [
            'items' => $items->map(fn (DrinkItem $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->quantity,
            ])->values()->all(),
            'total_quantity' => $this->totalQuantity(),
            'sort' => $sort,
        ];
    }

    /**
     * @return list<string>
     */
    public function orderLines(string $sort = 'name'): array
    {
        return $this->getItems($sort)
            ->map(fn (DrinkItem $item) => $item->quantity.'x '.$item->name)
            ->values()
            ->all();
    }

    private function ensureDefaultList(DrinkItem $item): void
    {
        if ($item->list_key !== $this->listKey()) {
            abort(404);
        }
    }
}
