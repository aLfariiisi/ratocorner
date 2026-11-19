<?php

use App\Enums\UserRole;
use App\Models\CafeSetting;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderDeletionLog;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\StockOpname;
use App\Models\Table;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder seeds default cafe settings, users with roles, and qr tables', function () {
    $this->seed(DatabaseSeeder::class);

    // 1. Cafe Setting
    $setting = CafeSetting::first();
    expect($setting)->not->toBeNull()
        ->and($setting->name)->toBe('Ratocorner')
        ->and($setting->currency)->toBe('IDR');

    // 2. Users & Roles
    $owner = User::where('email', 'owner@ratocorner.com')->first();
    expect($owner)->not->toBeNull()
        ->and($owner->role)->toBe(UserRole::Owner)
        ->and($owner->isOwner())->toBeTrue()
        ->and($owner->isManager())->toBeFalse()
        ->and($owner->isKasir())->toBeFalse();

    $manager = User::where('email', 'manager@ratocorner.com')->first();
    expect($manager)->not->toBeNull()
        ->and($manager->role)->toBe(UserRole::Manager)
        ->and($manager->isManager())->toBeTrue();

    $kasir = User::where('email', 'kasir@ratocorner.com')->first();
    expect($kasir)->not->toBeNull()
        ->and($kasir->role)->toBe(UserRole::Kasir)
        ->and($kasir->isKasir())->toBeTrue();

    // 3. Tables (Meja QR)
    $tables = Table::all();
    expect($tables->count())->toBeGreaterThanOrEqual(8);
    expect($tables->first()->qr_code_token)->not->toBeEmpty();
});

test('vendors and ingredients relationship works properly', function () {
    $vendor = Vendor::factory()->create(['name' => 'Toffin Roastery']);
    $ingredient = Ingredient::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Toraja Sapan Beans',
        'unit' => 'gram',
        'current_stock' => 5000.00,
        'minimum_stock' => 1000.00,
        'cost_per_unit' => 200.00,
    ]);

    expect($ingredient->vendor->id)->toBe($vendor->id)
        ->and($vendor->ingredients)->toHaveCount(1)
        ->and($ingredient->isLowStock())->toBeFalse();

    $ingredient->update(['current_stock' => 800.00]);
    expect($ingredient->isLowStock())->toBeTrue();
});

test('product recipe calculates HPP and automatically updates product HPP', function () {
    $product = Product::factory()->create(['price' => 25000.00, 'hpp' => 0.00]);
    $coffeeBeans = Ingredient::factory()->create(['cost_per_unit' => 200.00]); // Rp 200/gr
    $milk = Ingredient::factory()->create(['cost_per_unit' => 25.00]); // Rp 25/ml

    // 18 gram coffee = 18 * 200 = 3600
    ProductRecipe::create([
        'product_id' => $product->id,
        'ingredient_id' => $coffeeBeans->id,
        'quantity' => 18.00,
        'unit' => 'gram',
        'cost' => 3600.00,
    ]);

    // 150 ml milk = 150 * 25 = 3750
    ProductRecipe::create([
        'product_id' => $product->id,
        'ingredient_id' => $milk->id,
        'quantity' => 150.00,
        'unit' => 'ml',
        'cost' => 3750.00,
    ]);

    $product->refresh();
    // 3600 + 3750 = 7350
    expect((float) $product->hpp)->toBe(7350.00);
    expect($product->ingredients)->toHaveCount(2);
});

test('orders support SoftDeletes and deletion audit logging', function () {
    $table = Table::factory()->create();
    $customer = Customer::factory()->create();
    $cashier = User::factory()->kasir()->create();
    $product = Product::factory()->create(['price' => 30000.00, 'hpp' => 10000.00]);

    $order = Order::create([
        'table_id' => $table->id,
        'customer_id' => $customer->id,
        'cashier_id' => $cashier->id,
        'order_type' => 'dine_in',
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'subtotal' => 30000.00,
        'total_amount' => 30000.00,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 1,
        'unit_price' => 30000.00,
        'hpp' => 10000.00,
        'subtotal' => 30000.00,
    ]);

    expect($order->items)->toHaveCount(1)
        ->and($order->order_number)->toStartWith('ORD-');

    // Create deletion audit log before soft delete
    $manager = User::factory()->manager()->create();
    $log = $order->logDeletion($manager, 'Customer canceled order');

    expect($log)->toBeInstanceOf(OrderDeletionLog::class)
        ->and($log->order_number)->toBe($order->order_number)
        ->and($log->deleted_by)->toBe($manager->id)
        ->and($log->reason)->toBe('Customer canceled order')
        ->and($log->order_data['items'])->toHaveCount(1);

    // Soft delete the order
    $order->delete();

    expect(Order::find($order->id))->toBeNull()
        ->and(Order::withTrashed()->find($order->id))->not->toBeNull()
        ->and($order->trashed())->toBeTrue();
});

test('stock opname calculates differences and cost variance correctly', function () {
    $manager = User::factory()->manager()->create();
    $ingredient = Ingredient::factory()->create([
        'cost_per_unit' => 250.00,
        'current_stock' => 1000.00,
    ]);

    $opname = StockOpname::create([
        'ingredient_id' => $ingredient->id,
        'user_id' => $manager->id,
        'opname_date' => now()->toDateString(),
        'system_stock' => 1000.00,
        'physical_stock' => 950.00,
        'status' => 'adjusted',
        'notes' => 'Spill on bar',
    ]);

    expect((float) $opname->difference)->toBe(-50.00)
        ->and((float) $opname->cost_difference)->toBe(-12500.00) // -50 * 250
        ->and($opname->ingredient->id)->toBe($ingredient->id)
        ->and($opname->conductedBy->id)->toBe($manager->id);
});
