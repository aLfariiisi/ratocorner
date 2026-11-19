<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CafeSetting;
use App\Models\Order;
use App\Models\OrderDeletionLog;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['table', 'customer', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $orders = $query->paginate(15);
        $deletionLogs = OrderDeletionLog::with('deletedBy')->latest()->take(10)->get();

        return view('admin.orders.index', compact('orders', 'deletionLogs'));
    }

    public function show(Order $order): View
    {
        $order->load(['table', 'customer', 'cashier', 'items.product']);
        $cafeSetting = CafeSetting::getSettings();

        return view('admin.orders.show', compact('order', 'cafeSetting'));
    }

    public function confirmPayment(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['nullable', 'in:cash,qris,transfer'],
        ]);

        $orderService->confirmPayment(
            $order,
            Auth::user(),
            $validated['payment_method'] ?? null
        );

        return back()->with('success', "Pembayaran pesanan {$order->order_number} lunas & stok bahan baku otomatis terpotong.");
    }

    public function receipt(Order $order): View
    {
        $order->load(['table', 'customer', 'cashier', 'items']);
        $cafeSetting = CafeSetting::getSettings();

        return view('admin.orders.receipt', compact('order', 'cafeSetting'));
    }

    public function destroy(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $orderService->deleteOrderWithAudit(
            $order,
            Auth::user(),
            $validated['reason']
        );

        return redirect()->route('admin.orders.index')
            ->with('success', "Pesanan {$order->order_number} telah dihapus dan tercatat di log audit.");
    }
}
