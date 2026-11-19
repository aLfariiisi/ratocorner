<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Konfirmasi pembayaran & selesaikan pesanan (otomatis potong stok bahan baku).
     */
    public function confirmPayment(Order $order, User $cashier, ?string $paymentMethod = null): void
    {
        DB::transaction(function () use ($order, $cashier, $paymentMethod) {
            if ($order->payment_status === 'paid') {
                return;
            }

            $order->load('items.product.recipes.ingredient');

            // Potong stok bahan baku sesuai takaran resep produk
            foreach ($order->items as $item) {
                if (! $item->product) {
                    continue;
                }

                foreach ($item->product->recipes as $recipe) {
                    if ($recipe->ingredient) {
                        $usedQty = (float) $recipe->quantity * (int) $item->quantity;
                        $recipe->ingredient->decrement('current_stock', $usedQty);
                    }
                }
            }

            $order->update([
                'cashier_id' => $cashier->id,
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod ?? $order->payment_method,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            if ($order->table) {
                $order->table->update(['status' => 'available']);
            }
        });
    }

    /**
     * Hapus pesanan oleh kasir + wajib catat extra tracking ke order_deletion_logs.
     */
    public function deleteOrderWithAudit(Order $order, User $cashier, string $reason): void
    {
        DB::transaction(function () use ($order, $cashier, $reason) {
            $order->load('items');

            // Simpan snapshot lengkap ke log audit sebelum soft delete
            $order->logDeletion($cashier, $reason);

            if ($order->table && $order->payment_status !== 'paid') {
                $order->table->update(['status' => 'available']);
            }

            $order->delete();
        });
    }
}
