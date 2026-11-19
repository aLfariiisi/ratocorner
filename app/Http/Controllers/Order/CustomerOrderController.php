<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function showTableOrder(string $table_code): View
    {
        $table = Table::where('qr_code_token', $table_code)
            ->orWhere('table_number', $table_code)
            ->where('is_active', true)
            ->firstOrFail();

        $cafeSetting = CafeSetting::getSettings();
        $categories = Category::orderBy('name')->get();
        $products = Product::with('category')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        return view('order.table', compact('table', 'cafeSetting', 'categories', 'products'));
    }

    public function submitOrder(Request $request, string $table_code): RedirectResponse
    {
        $table = Table::where('qr_code_token', $table_code)
            ->orWhere('table_number', $table_code)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:25'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cash,qris,transfer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $order = DB::transaction(function () use ($validated, $table) {
            $cafeSetting = CafeSetting::getSettings();

            if (! empty($validated['customer_phone'])) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $validated['customer_phone']],
                    ['name' => $validated['customer_name']]
                );
                $customer->update(['name' => $validated['customer_name']]);
            } else {
                $customer = Customer::create([
                    'name' => $validated['customer_name'],
                ]);
            }

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $itemSubtotal = $product->price * $qty;
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'hpp' => $product->hpp,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $taxRate = (float) ($cafeSetting->tax_percentage ?? 0);
            $serviceRate = (float) ($cafeSetting->service_charge_percentage ?? 0);
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $serviceAmount = round($subtotal * ($serviceRate / 100), 2);
            $grandTotal = $subtotal + $taxAmount + $serviceAmount;

            $order = Order::create([
                'table_id' => $table->id,
                'customer_id' => $customer->id,
                'customer_name' => $validated['customer_name'],
                'order_type' => 'dine_in',
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceAmount,
                'total_amount' => $grandTotal,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            $table->update(['status' => 'occupied']);

            return $order;
        });

        return redirect()
            ->route('order.table', $table->qr_code_token)
            ->with('order_success', $order->order_number);
    }
}
