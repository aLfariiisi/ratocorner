<?php

namespace App\Http\Controllers;

use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $setting = CafeSetting::getSettings();
        $products = Product::with('category')->where('is_available', true)->take(8)->get();

        return view('welcome', compact('setting', 'products'));
    }

    public function products(Request $request): View
    {
        $setting = CafeSetting::getSettings();
        $categories = Category::orderBy('name')->get();
        $query = Product::with('category')->where('is_available', true);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12);

        return view('products.index', compact('setting', 'categories', 'products'));
    }

    public function showProduct(Product $product): View
    {
        $setting = CafeSetting::getSettings();
        $product->load('category');
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->take(4)
            ->get();

        return view('products.show', compact('setting', 'product', 'relatedProducts'));
    }

    public function about(): View
    {
        $setting = CafeSetting::getSettings();

        return view('about', compact('setting'));
    }

    public function contact(): View
    {
        $setting = CafeSetting::getSettings();

        return view('contact', compact('setting'));
    }

    public function cart(): View
    {
        $setting = CafeSetting::getSettings();
        $cart = session()->get('cart', []);

        return view('cart.index', compact('setting', 'cart'));
    }

    public function addToCart(Request $request, ?Product $product = null): RedirectResponse|JsonResponse
    {
        if (! $product && $request->filled('product_id')) {
            $product = Product::findOrFail($request->input('product_id'));
        }

        if (! $product) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Produk tidak ditemukan.');
        }

        $cart = session()->get('cart', []);
        $qty = max(1, (int) $request->input('quantity', 1));

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $qty;
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->image_path,
                'quantity' => $qty,
            ];
        }

        session()->put('cart', $cart);

        $totalCount = collect($cart)->sum('quantity');
        $totalPrice = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$product->name} ditambahkan ke keranjang.",
                'cart_count' => $totalCount,
                'total_count' => $totalCount,
                'cart_total' => number_format($totalPrice, 0, ',', '.'),
                'total_price' => $totalPrice,
            ]);
        }

        return back()->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request, ?int $productId = null): RedirectResponse
    {
        $id = $productId ?? (int) ($request->input('id') ?? $request->input('product_id'));
        $qty = max(1, (int) $request->input('quantity', 1));
        $cart = session()->get('cart', []);

        if ($id && isset($cart[$id])) {
            $cart[$id]['quantity'] = $qty;
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Jumlah pesanan diperbarui.');
    }

    public function removeFromCart(Request $request, ?int $productId = null): RedirectResponse
    {
        $id = $productId ?? (int) ($request->input('id') ?? $request->input('product_id'));
        $cart = session()->get('cart', []);

        if ($id) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $setting = CafeSetting::getSettings();
        $tables = Table::where('is_active', true)->orderBy('table_number')->get();
        $paymentMethods = collect([
            (object) ['name' => 'Tunai / Kasir'],
            (object) ['name' => 'QRIS'],
            (object) ['name' => 'Transfer Bank'],
        ]);

        return view('checkout.index', compact('setting', 'cart', 'tables', 'paymentMethods'));
    }

    public function processCheckout(Request $request): RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:25'],
            'nomor_meja' => ['nullable', 'string', 'max:20'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = DB::transaction(function () use ($validated, $cart) {
            $setting = CafeSetting::getSettings();

            $customer = ! empty($validated['customer_phone'])
                ? Customer::firstOrCreate(['phone' => $validated['customer_phone']], ['name' => $validated['customer_name']])
                : Customer::create(['name' => $validated['customer_name']]);

            // Cocokkan nomor_meja (misal "1" atau "01" atau "M-01") ke tabel tables
            $table = null;
            if (! empty($validated['table_id'])) {
                $table = Table::find($validated['table_id']);
            } elseif (! empty($validated['nomor_meja'])) {
                $num = trim((string) $validated['nomor_meja']);
                $padded = str_pad($num, 2, '0', STR_PAD_LEFT);
                $table = Table::whereIn('table_number', [$num, $padded, 'M-' . $num, 'M-' . $padded])->first();
            }

            // Normalisasi metode bayar ke enum cash / qris / transfer
            $rawMethod = strtolower($validated['payment_method']);
            $method = str_contains($rawMethod, 'qris') ? 'qris'
                : (str_contains($rawMethod, 'transfer') ? 'transfer' : 'cash');

            $subtotal = 0;
            $itemsData = [];

            foreach ($cart as $id => $row) {
                $product = Product::findOrFail($id);
                $qty = (int) $row['quantity'];
                $sub = $product->price * $qty;
                $subtotal += $sub;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'hpp' => $product->hpp,
                    'subtotal' => $sub,
                ];
            }

            $tax = round($subtotal * (((float) ($setting->tax_percentage ?? 0)) / 100), 2);
            $service = round($subtotal * (((float) ($setting->service_charge_percentage ?? 0)) / 100), 2);

            $extraNotes = $validated['notes'] ?? '';
            if (! $table && ! empty($validated['nomor_meja'])) {
                $extraNotes = trim("Meja #{$validated['nomor_meja']}. " . $extraNotes);
            }

            $order = Order::create([
                'table_id' => $table?->id,
                'customer_id' => $customer->id,
                'customer_name' => $validated['customer_name'],
                'order_type' => ($table || ! empty($validated['nomor_meja'])) ? 'dine_in' : 'take_away',
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $method,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'service_charge' => $service,
                'total_amount' => $subtotal + $tax + $service,
                'notes' => $extraNotes ?: null,
            ]);

            foreach ($itemsData as $d) {
                $order->items()->create($d);
            }

            if ($table) {
                $table->update(['status' => 'occupied']);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('home')->with('success', "Pesanan {$order->order_number} berhasil dibuat! Silakan konfirmasi di kasir.");
    }
}
