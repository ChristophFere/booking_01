<?php

use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\BookingSettingsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlockedDateController;
use App\Http\Controllers\Admin\BusinessHourController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('business-hours', [BusinessHourController::class, 'edit'])->name('business-hours.edit');
        Route::put('business-hours', [BusinessHourController::class, 'update'])->name('business-hours.update');

        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        Route::get('blocked-dates', [BlockedDateController::class, 'index'])->name('blocked-dates.index');
        Route::post('blocked-dates', [BlockedDateController::class, 'store'])->name('blocked-dates.store');
        Route::delete('blocked-dates/{blockedDate}', [BlockedDateController::class, 'destroy'])->name('blocked-dates.destroy');

        Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::post('appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::put('appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

        Route::get('booking-settings', [BookingSettingsController::class, 'index'])->name('booking-settings.index');
        Route::put('booking-settings', [BookingSettingsController::class, 'update'])->name('booking-settings.update');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
        Route::put('settings/mail', [SettingsController::class, 'updateMail'])->name('settings.mail.update');
        Route::post('settings/mail/test', [SettingsController::class, 'sendTestMail'])->name('settings.mail.test');
    });
});
