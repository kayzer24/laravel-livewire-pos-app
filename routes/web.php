<?php

use App\Livewire\Customer\CreateCustomer;
use App\Livewire\Customer\EditCustomer;
use App\Livewire\Customer\ListCustomers;
use App\Livewire\Items\CreateInventory;
use App\Livewire\Items\CreateItem;
use App\Livewire\Items\EditInventory;
use App\Livewire\Items\EditItem;
use App\Livewire\Items\ListInventories;
use App\Livewire\Items\ListItems;
use App\Livewire\Management\CreatePaymentMethod;
use App\Livewire\Management\CreateUser;
use App\Livewire\Management\EditPaymentMethod;
use App\Livewire\Management\EditUser;
use App\Livewire\Management\ListPaymentMethods;
use App\Livewire\Management\ListUsers;
use App\Livewire\Sales\CreateSale;
use App\Livewire\Sales\EditSale;
use App\Livewire\Sales\ListSales;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    /* User */
    Route::get('/manage-users', ListUsers::class)->name('users.index');
    Route::get('/users/create', CreateUser::class)->name('users.create');
    Route::get('/users/{record}/edit', EditUser::class)->name('users.edit');

    /* Customer */
    Route::get('/manage-customers', ListCustomers::class)->name('customers.index');
    Route::get('/customers/create', CreateCustomer::class)->name('customers.create');
    Route::get('/customers/{record}/edit', EditCustomer::class)->name('customers.edit');

    /* Payment Method */
    Route::get('/manage-payment-methods', ListPaymentMethods::class)->name('payment.methods.index');
    Route::get('/payment-methods/create', CreatePaymentMethod::class)->name('payment-methods.create');
    Route::get('/payment-methods/{record}/edit', EditPaymentMethod::class)->name('payment-methods.edit');

    /* Items */
    Route::get('/manage-items', ListItems::class)->name('items.index');
    Route::get('/items/create', CreateItem::class)->name('items.create');
    Route::get('/items/{record}/edit', EditItem::class)->name('items.edit');

    /* Inventory */
    Route::get('/manage-inventories', ListInventories::class)->name('inventories.index');
    Route::get('/inventories/create', CreateInventory::class)->name('inventories.create');
    Route::get('/inventories/{record}/edit', EditInventory::class)->name('inventories.edit');

    /* Sales */
    Route::get('/manage-sales', ListSales::class)->name('sales.index');
    Route::get('/sales/create', CreateSale::class)->name('sales.create');
    Route::get('/sales/{record}/edit', EditSale::class)->name('sales.edit');
});

require __DIR__.'/settings.php';
