<?php

use App\Livewire\Customer\ListCustomers;
use App\Livewire\Items\ListInventories;
use App\Livewire\Items\ListItems;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Management\ListUsers;
use App\Livewire\Sales\ListSales;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/manage-users', ListUsers::class)->name('users.index');
    Route::get('/manage-items', ListItems::class)->name('items.index');
    Route::get('/manage-inventories', ListInventories::class)->name('inventories.index');
    Route::get('/manage-sales', ListSales::class)->name('sales.index');
    Route::get('/manage-customers', ListCustomers::class)->name('customers.index');
    Route::get('/manage-payment-methods', ListPaymentMethods::class)->name('payment.methods.index');
});

require __DIR__.'/settings.php';
