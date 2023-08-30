<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TaxController;

Route::get('/language/{language}', LocaleController::class)->name('language');

Route::get('/apartments/{apartment}.ics', [CalendarController::class, 'icalendar'])->name('apartments.icalendar');

Route::get('/reservations/{reservation}/invoice/print', [InvoiceController::class, 'reservation_invoice'])->name('reservations.invoice.print');

Route::middleware('auth')->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::prefix('apartments')->as('apartments.')->group(function () {
        Route::get('/', [ApartmentController::class, 'index'])->middleware('permission:show apartments')->name('index');
        Route::post('/', [ApartmentController::class, 'store'])->middleware('permission:create apartments')->name('store');
        Route::post('/available', [ApartmentController::class, 'available'])->middleware('permission:transfer reservations')->name('available');
        Route::patch('/{apartment}', [ApartmentController::class, 'update'])->middleware('permission:edit apartments')->name('update');
        Route::patch('/{apartment}/state', [ApartmentController::class, 'update_state'])->name('update.state');
        Route::patch('/{apartment}/maintenance', [ApartmentController::class, 'update_maintenance'])->middleware('permission:maintenance apartments')->name('update.maintenance');
        Route::delete('/{apartment}', [ApartmentController::class, 'destroy'])->middleware('permission:delete apartments')->name('destroy');
    });

    Route::prefix('reservations')->as('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->middleware('permission:show reservations')->name('index');
        Route::post('/search', [ReservationController::class, 'search'])->middleware('permission:show reservations')->name('search');
        Route::patch('/terms', [ReservationController::class, 'update_terms'])->middleware('permission:edit terms of the reservation lease')->name('terms.update');

        Route::get('/{reservation}/invoice', [ReservationController::class, 'invoice'])->middleware('permission:print reservations')->name('invoice');
        Route::get('/{reservation}/receipt', [ReservationController::class, 'receipt_voucher'])->middleware('permission:print receipt vouchers')->name('receipt');
        Route::get('/{reservation}/tax', [ReservationController::class, 'tax_invoice'])->middleware('permission:print tax invoices')->name('tax');

        Route::post('/{apartment}', [ReservationController::class, 'store'])->middleware('permission:create reservations')->name('store');
        Route::patch('/{reservation}/transfer', [ReservationController::class, 'transfer'])->middleware('permission:transfer reservations')->name('transfer');
        Route::patch('/{reservation}/checkin', [ReservationController::class, 'checkin'])->middleware('permission:checkin apartments')->name('checkin');
        Route::patch('/{reservation}', [ReservationController::class, 'update'])->middleware('permission:edit reservations')->name('update');
        Route::delete('/{reservation}/cancel', [ReservationController::class, 'cancel'])->middleware('permission:cancel reservations')->name('cancel');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->middleware('permission:delete reservations')->name('destroy');
    });

    Route::get('/calendar', [CalendarController::class, 'index'])->middleware('permission:show calendar')->name('calendar.index');

    Route::prefix('finance')->as('finance.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->middleware('permission:show finance')->name('index');

        Route::post('/expenses', [ExpenseController::class, 'store'])->middleware('permission:create expenses')->name('expenses.store');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('permission:delete expenses')->name('expenses.destroy');

        Route::post('/taxes', [TaxController::class, 'store'])->middleware('permission:create taxes')->name('taxes.store');
        Route::delete('/taxes/{tax}', [TaxController::class, 'destroy'])->middleware('permission:delete taxes')->name('taxes.destroy');
    });

    Route::prefix('statistics')->as('statistics.')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->middleware('permission:show statistics')->name('index');
    });

    Route::prefix('users')->as('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:show users')->name('index');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create users')->name('store');
        Route::patch('/{user}', [UserController::class, 'update'])->middleware('permission:edit users')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete users')->name('destroy');
    });

    Route::patch('/hotel', [HotelController::class, 'update'])->middleware('permission:edit hotel information')->name('hotel.update');
    Route::delete('hotel/logo', [HotelController::class, 'destroy_logo'])->middleware('permission:edit hotel information')->name('hotel.logo.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';