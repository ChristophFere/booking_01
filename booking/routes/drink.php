<?php

use App\Http\Controllers\Drink\DrinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('drink')->name('drink.')->group(function () {
    Route::get('/', [DrinkController::class, 'index'])->name('index');
    Route::get('/items', [DrinkController::class, 'items'])->name('items.index');
    Route::post('/items', [DrinkController::class, 'store'])->name('items.store');
    Route::post('/items/{drinkItem}/increment', [DrinkController::class, 'increment'])->name('items.increment');
    Route::post('/items/{drinkItem}/decrement', [DrinkController::class, 'decrement'])->name('items.decrement');
    Route::delete('/items', [DrinkController::class, 'destroyAll'])->name('items.destroy-all');
    Route::get('/order-text', [DrinkController::class, 'orderText'])->name('order-text');
});
