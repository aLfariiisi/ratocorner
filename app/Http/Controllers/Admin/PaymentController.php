<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Order::with(['table', 'customer', 'cashier'])
            ->latest()
            ->paginate(15);

        $orders = Order::where('payment_status', 'unpaid')->latest()->get();

        return view('admin.payments.index', compact('payments', 'orders'));
    }

    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'payment_method' => ['required', 'in:cash,qris,transfer'],
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $orderService->confirmPayment($order, Auth::user(), $validated['payment_method']);

        return back()->with('success', "Pembayaran pesanan {$order->order_number} berhasil dikonfirmasi & stok terpotong.");
    }
}
