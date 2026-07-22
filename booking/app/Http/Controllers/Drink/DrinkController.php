<?php

namespace App\Http\Controllers\Drink;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drink\StoreDrinkItemRequest;
use App\Models\DrinkItem;
use App\Services\DrinkItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DrinkController extends Controller
{
    public function __construct(
        private DrinkItemService $drinkItemService,
    ) {}

    public function index(): View
    {
        return view('drink.index');
    }

    public function items(Request $request): JsonResponse
    {
        $sort = $this->resolveSort($request);

        return response()->json($this->drinkItemService->toPayload($sort));
    }

    public function store(StoreDrinkItemRequest $request): JsonResponse
    {
        $this->drinkItemService->addItem($request->validated('name'));

        return response()->json(
            $this->drinkItemService->toPayload($this->resolveSort($request)),
            201,
        );
    }

    public function increment(Request $request, DrinkItem $drinkItem): JsonResponse
    {
        $this->drinkItemService->increment($drinkItem);

        return response()->json($this->drinkItemService->toPayload($this->resolveSort($request)));
    }

    public function decrement(Request $request, DrinkItem $drinkItem): JsonResponse
    {
        $this->drinkItemService->decrement($drinkItem);

        return response()->json($this->drinkItemService->toPayload($this->resolveSort($request)));
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $this->drinkItemService->clearAll();

        return response()->json($this->drinkItemService->toPayload($this->resolveSort($request)));
    }

    public function orderText(Request $request): JsonResponse
    {
        $sort = $this->resolveSort($request);
        $lines = $this->drinkItemService->orderLines($sort);

        return response()->json([
            'text' => implode("\n", $lines),
        ]);
    }

    private function resolveSort(Request $request): string
    {
        $sort = $request->query('sort', 'name');

        return in_array($sort, ['name', 'quantity'], true) ? $sort : 'name';
    }
}
