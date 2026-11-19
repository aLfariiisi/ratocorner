<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockOpname;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function financials(): View
    {
        $income = (float) Order::where('payment_status', 'paid')->sum('total_amount');

        $totalHpp = (float) OrderItem::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->selectRaw('COALESCE(SUM(hpp * quantity), 0) as total')->value('total');

        $opnameLoss = abs((float) StockOpname::where('difference', '<', 0)->sum('cost_difference'));
        $expenses = $totalHpp + $opnameLoss;
        $netProfit = $income - $expenses;

        $transactions = Order::with('items')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($order) {
                $orderHpp = $order->items->sum(fn ($item) => (float) $item->hpp * (int) $item->quantity);

                return [
                    'date' => $order->created_at,
                    'description' => 'Penjualan Pesanan ' . $order->order_number . ' (' . $order->order_type . ')',
                    'income' => (float) $order->total_amount,
                    'expense' => $orderHpp,
                ];
            });

        return view('admin.reports.financials', compact('income', 'expenses', 'netProfit', 'transactions'));
    }

    public function analytics(): View
    {
        $totalSales = (float) Order::where('payment_status', 'paid')->sum('total_amount');

        $totalHpp = (float) OrderItem::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->selectRaw('COALESCE(SUM(hpp * quantity), 0) as total')->value('total');

        $opnameLoss = abs((float) StockOpname::where('difference', '<', 0)->sum('cost_difference'));
        $expenses = $totalHpp + $opnameLoss;
        $netProfit = $totalSales - $expenses;
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();

        $salesMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $salesData = array_fill(0, 12, 0);
        $expenseData = array_fill(0, 12, 0);
        $profitData = array_fill(0, 12, 0);

        $currentMonth = (int) now()->format('n') - 1;
        $salesData[$currentMonth] = $totalSales;
        $expenseData[$currentMonth] = $expenses;
        $profitData[$currentMonth] = $netProfit;

        $statusLabels = ['Pending', 'Processing', 'Completed', 'Cancelled'];
        $statusCounts = [
            Order::where('status', 'pending')->count(),
            Order::where('status', 'processing')->count(),
            Order::where('status', 'completed')->count(),
            Order::where('status', 'cancelled')->count(),
        ];

        $topProducts = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.reports.analytics', compact(
            'totalSales',
            'expenses',
            'netProfit',
            'totalOrders',
            'salesMonths',
            'salesData',
            'expenseData',
            'profitData',
            'statusLabels',
            'statusCounts',
            'topProducts'
        ));
    }

    public function hpp(): View
    {
        $products = Product::with('category')->latest()->paginate(15);
        $totalProducts = Product::count();
        $avgHpp = (float) Product::avg('hpp') ?: 0;
        $avgPrice = (float) Product::avg('price') ?: 0;
        $avgMargin = $avgPrice > 0 ? round((($avgPrice - $avgHpp) / $avgPrice) * 100, 1) : 0;

        return view('admin.reports.hpp', compact('products', 'totalProducts', 'avgHpp', 'avgPrice', 'avgMargin'));
    }
}
