<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockOpname;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show ERP Admin Dashboard.
     */
    public function index(): View
    {
        $totalOrders = Order::count();
        $totalIncome = (float) Order::where('payment_status', 'paid')->sum('total_amount');

        // Akumulasi pengeluaran dari selisih stock opname minus & operasional
        $expenses = abs((float) StockOpname::where('difference', '<', 0)->sum('cost_difference'));

        // Total HPP produk yang terjual
        $totalHpp = (float) OrderItem::whereHas('order', function ($query) {
            $query->where('payment_status', 'paid');
        })->sum('hpp');

        // Net Profit
        $netProfit = max(0, $totalIncome - $totalHpp - $expenses);
        $netProfitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0;

        return view('admin.dashboard', compact(
            'expenses',
            'netProfitMargin',
            'netProfit',
            'totalOrders',
            'totalIncome'
        ));
    }
}
