<?php

use App\Http\Controllers\Admin\CafeSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Order\CustomerOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// --------------------------------------------------------------------------
// Public / Customer Routes
// --------------------------------------------------------------------------
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/products', [PublicController::class, 'products'])->name('products.index');
Route::get('/products/{product}', [PublicController::class, 'showProduct'])->name('products.show');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::view('/faq', 'faq')->name('faq');
Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');

// Keranjang & Checkout Publik (Tanpa Login)
Route::get('/cart', [PublicController::class, 'cart'])->name('cart.index');
Route::post('/cart/add/{product?}', [PublicController::class, 'addToCart'])->name('cart.add');
Route::match(['patch', 'post'], '/cart/update/{productId?}', [PublicController::class, 'updateCart'])->name('cart.update');
Route::match(['delete', 'post'], '/cart/remove/{productId?}', [PublicController::class, 'removeFromCart'])->name('cart.remove');

Route::get('/checkout', [PublicController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [PublicController::class, 'processCheckout'])->name('checkout.process');

// Customer QR Table Self-Order Link (/order/{table_code})
Route::get('/order/{table_code}', [CustomerOrderController::class, 'showTableOrder'])->name('order.table');
Route::post('/order/{table_code}', [CustomerOrderController::class, 'submitOrder'])->name('order.table.submit');

// --------------------------------------------------------------------------
// Authentication Routes (Khusus Staf: Owner, Manager, Kasir)
// --------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

// --------------------------------------------------------------------------
// ERP Admin / Staff Routes
// --------------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // 1. Dashboard (Owner, Manager, Kasir)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Manajemen Meja QR (Tables)
    Route::get('/tables/print-all', [TableController::class, 'printAll'])->name('tables.print-all');
    Route::get('/tables/{table}/print', [TableController::class, 'print'])->name('tables.print');
    Route::resource('tables', TableController::class);

    // 3. Modul Operasional Kasir & Staf Lainnya
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::resource('customers', CustomerController::class);

    // 4. Modul Menu, Resep HPP, Stok, Vendor & Opname (Owner & Manager)
    Route::middleware('role:owner,manager')->group(function () {
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        // Vendor
        Route::resource('vendors', VendorController::class)->only(['index', 'store', 'update', 'destroy']);

        // Stok Bahan Baku & Restock
        Route::resource('stocks', IngredientController::class)->only(['index', 'store', 'update', 'destroy']);

        // Stok Opname Manual
        Route::get('/stock-opnames', [StockOpnameController::class, 'index'])->name('stock-opnames.index');
        Route::post('/stock-opnames', [StockOpnameController::class, 'store'])->name('stock-opnames.store');
    });

    // 5. Modul Laporan & Analitik (Kasir DILARANG KERAS: hanya Owner & Manager)
    Route::prefix('reports')->name('reports.')->middleware('role:owner,manager')->group(function () {
        Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
        Route::get('/financials', [ReportController::class, 'financials'])->name('financials');
        Route::get('/hpp', [ReportController::class, 'hpp'])->name('hpp');
    });

    // 6. Modul Pengaturan Cafe (Kasir DILARANG KERAS: hanya Owner & Manager)
    Route::prefix('settings')->name('settings.')->middleware('role:owner,manager')->group(function () {
        Route::get('/', [CafeSettingController::class, 'edit'])->name('edit');
        Route::put('/', [CafeSettingController::class, 'update'])->name('update');
    });

    // 7. Manajemen Akun Staf & Reset Password (KHUSUS OWNER SAJA)
    Route::middleware('role:owner')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    });

});
