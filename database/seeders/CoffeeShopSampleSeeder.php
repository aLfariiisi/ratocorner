<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\StockOpname;
use App\Models\Table;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class CoffeeShopSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Vendor
        $vendor = Vendor::firstOrCreate(
            ['code' => 'VND-001'],
            [
                'name' => 'PT Roastery Kopi Nusantara',
                'contact_person' => 'Hendro',
                'phone' => '0813-8899-7711',
                'email' => 'order@kopinusantara.id',
                'address' => 'Bandung, Jawa Barat',
                'notes' => 'Supplier utama biji kopi Arabica & Robusta blend',
                'is_active' => true,
            ]
        );

        $vendorDairy = Vendor::firstOrCreate(
            ['code' => 'VND-002'],
            [
                'name' => 'CV Sumber Dairy Sejahtera',
                'contact_person' => 'Sari Dewi',
                'phone' => '0812-7766-5544',
                'email' => 'sales@sumberdairy.com',
                'address' => 'Bogor, Jawa Barat',
                'notes' => 'Supplier fresh milk, whipping cream, dan sirup',
                'is_active' => true,
            ]
        );

        // 2. Ingredients (Bahan Baku)
        $beans = Ingredient::firstOrCreate(
            ['code' => 'ING-COFFEE-01'],
            [
                'vendor_id' => $vendor->id,
                'name' => 'House Blend Coffee Beans (70:30)',
                'unit' => 'gram',
                'current_stock' => 10000.00, // 10 kg
                'minimum_stock' => 2000.00,  // 2 kg alert
                'cost_per_unit' => 250.00,   // Rp 250 / gram (Rp 250.000 / kg)
                'description' => 'Arabica Gayo & Robusta Temanggung House Blend',
            ]
        );

        $milk = Ingredient::firstOrCreate(
            ['code' => 'ING-MILK-01'],
            [
                'vendor_id' => $vendorDairy->id,
                'name' => 'Fresh Milk Barista Edition 1L',
                'unit' => 'ml',
                'current_stock' => 24000.00, // 24 Liter
                'minimum_stock' => 5000.00,  // 5 Liter alert
                'cost_per_unit' => 22.00,    // Rp 22 / ml (Rp 22.000 / Liter)
                'description' => 'Homogenized pasteurized fresh milk for steaming latte art',
            ]
        );

        $aren = Ingredient::firstOrCreate(
            ['code' => 'ING-SYRUP-01'],
            [
                'vendor_id' => $vendorDairy->id,
                'name' => 'Sirup Gula Aren Organik 1L',
                'unit' => 'ml',
                'current_stock' => 5000.00,
                'minimum_stock' => 1000.00,
                'cost_per_unit' => 35.00,    // Rp 35 / ml (Rp 35.000 / Liter)
                'description' => 'Gula aren cair asli tanpa pemanis buatan',
            ]
        );

        $cup = Ingredient::firstOrCreate(
            ['code' => 'ING-PACK-01'],
            [
                'vendor_id' => $vendorDairy->id,
                'name' => 'Cup + Lid Ratocorner 12oz',
                'unit' => 'pcs',
                'current_stock' => 500.00,
                'minimum_stock' => 100.00,
                'cost_per_unit' => 1200.00,  // Rp 1.200 / pcs
                'description' => 'Paper cup + sip lid cetak logo Ratocorner',
            ]
        );

        // 3. Category & Products
        $catCoffee = Category::firstOrCreate(
            ['slug' => 'coffee'],
            [
                'name' => 'Coffee & Espresso',
                'description' => 'Signature espresso-based coffee drinks',
                'is_active' => true,
            ]
        );

        $catNonCoffee = Category::firstOrCreate(
            ['slug' => 'non-coffee'],
            [
                'name' => 'Non-Coffee',
                'description' => 'Artisan chocolate, matcha, and artisan teas',
                'is_active' => true,
            ]
        );

        $kopiSusu = Product::firstOrCreate(
            ['slug' => 'kopi-susu-ratocorner'],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Kopi Susu Aren Ratocorner',
                'sku' => 'PRD-KS-001',
                'description' => 'Signature kopi susu gula aren dengan double shot espresso dan susu segar',
                'price' => 24000.00,
                'hpp' => 0.00,
                'is_available' => true,
            ]
        );

        $americano = Product::firstOrCreate(
            ['slug' => 'americano-iced'],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Iced Americano',
                'sku' => 'PRD-AMR-001',
                'description' => 'Double shot espresso over cold water and ice',
                'price' => 20000.00,
                'hpp' => 0.00,
                'is_available' => true,
            ]
        );

        // 4. Recipes & HPP Calculation
        // Kopi Susu Recipe:
        // - 18 gr coffee beans (18 * 250 = 4,500)
        // - 120 ml milk (120 * 22 = 2,640)
        // - 25 ml aren syrup (25 * 35 = 875)
        // - 1 cup + lid (1 * 1200 = 1,200)
        // Total HPP = 9,215
        ProductRecipe::updateOrCreate(
            ['product_id' => $kopiSusu->id, 'ingredient_id' => $beans->id],
            ['quantity' => 18.00, 'unit' => 'gram', 'cost' => 4500.00, 'notes' => 'Double shot extraction']
        );
        ProductRecipe::updateOrCreate(
            ['product_id' => $kopiSusu->id, 'ingredient_id' => $milk->id],
            ['quantity' => 120.00, 'unit' => 'ml', 'cost' => 2640.00, 'notes' => 'Steamed/chilled milk']
        );
        ProductRecipe::updateOrCreate(
            ['product_id' => $kopiSusu->id, 'ingredient_id' => $aren->id],
            ['quantity' => 25.00, 'unit' => 'ml', 'cost' => 875.00, 'notes' => 'Liquid palm sugar']
        );
        ProductRecipe::updateOrCreate(
            ['product_id' => $kopiSusu->id, 'ingredient_id' => $cup->id],
            ['quantity' => 1.00, 'unit' => 'pcs', 'cost' => 1200.00, 'notes' => 'Takeaway cup & lid']
        );
        $kopiSusu->refreshHpp();

        // Americano Recipe:
        ProductRecipe::updateOrCreate(
            ['product_id' => $americano->id, 'ingredient_id' => $beans->id],
            ['quantity' => 18.00, 'unit' => 'gram', 'cost' => 4500.00, 'notes' => 'Double shot espresso']
        );
        ProductRecipe::updateOrCreate(
            ['product_id' => $americano->id, 'ingredient_id' => $cup->id],
            ['quantity' => 1.00, 'unit' => 'pcs', 'cost' => 1200.00, 'notes' => 'Cold cup']
        );
        $americano->refreshHpp();

        // 5. Customer
        $customer = Customer::firstOrCreate(
            ['phone' => '081299887766'],
            [
                'name' => 'Dimas Arya',
                'email' => 'dimas.arya@example.com',
                'loyalty_points' => 25,
                'total_orders' => 2,
                'total_spent' => 48000.00,
                'notes' => 'Regular customer, suka less sweet',
            ]
        );

        // 6. Sample Dine-In Order
        $table = Table::where('table_number', '01')->first();
        $cashier = User::where('email', 'kasir@ratocorner.com')->first();

        $order = Order::firstOrCreate(
            ['order_number' => 'ORD-'.now()->format('Ymd').'-0001'],
            [
                'table_id' => $table?->id,
                'customer_id' => $customer->id,
                'cashier_id' => $cashier?->id,
                'order_type' => 'dine_in',
                'customer_name' => $customer->name,
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => 'qris',
                'subtotal' => 48000.00,
                'tax_amount' => 5280.00,
                'service_charge' => 2400.00,
                'discount_amount' => 0.00,
                'total_amount' => 55680.00,
                'paid_amount' => 55680.00,
                'change_amount' => 0.00,
                'notes' => 'Dine in meja 01, bayar QRIS langsung',
                'paid_at' => now(),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order->id, 'product_id' => $kopiSusu->id],
            [
                'product_name' => $kopiSusu->name,
                'quantity' => 2,
                'unit_price' => 24000.00,
                'hpp' => $kopiSusu->hpp,
                'subtotal' => 48000.00,
                'notes' => '1 Less sweet, 1 Normal',
            ]
        );

        // 7. Stock Opname Sample Record
        $manager = User::where('email', 'manager@ratocorner.com')->first();
        StockOpname::firstOrCreate(
            [
                'ingredient_id' => $beans->id,
                'opname_date' => now()->toDateString(),
            ],
            [
                'user_id' => $manager?->id,
                'system_stock' => 10000.00,
                'physical_stock' => 9850.00,
                'difference' => -150.00,
                'cost_difference' => -37500.00,
                'status' => 'adjusted',
                'notes' => 'Dial-in espresso kalibrasi grinder pagi hari',
            ]
        );
    }
}
