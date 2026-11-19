<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'order_number',
    'table_id',
    'customer_id',
    'cashier_id',
    'order_type',
    'customer_name',
    'status',
    'payment_status',
    'payment_method',
    'subtotal',
    'tax_amount',
    'service_charge',
    'discount_amount',
    'total_amount',
    'paid_amount',
    'change_amount',
    'notes',
    'paid_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a sequential daily order number (e.g., ORD-20261119-0001).
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-'.now()->format('Ymd').'-';
        $latest = static::withTrashed()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($latest) {
            $parts = explode('-', $latest->order_number);
            $sequence = ((int) end($parts)) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Table associated with this order.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Customer who placed this order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Cashier or user who processed this order.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Ordered items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Deletion logs associated with this order.
     */
    public function deletionLogs(): HasMany
    {
        return $this->hasMany(OrderDeletionLog::class);
    }

    /**
     * Recalculate order subtotal and total based on items and percentages.
     */
    public function recalculateTotals(float $taxRate = 0.0, float $serviceRate = 0.0): void
    {
        $subtotal = (float) $this->items()->sum('subtotal');
        $tax = round($subtotal * ($taxRate / 100), 2);
        $service = round($subtotal * ($serviceRate / 100), 2);
        $discount = (float) $this->discount_amount;
        $total = max(0, $subtotal + $tax + $service - $discount);

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'service_charge' => $service,
            'total_amount' => $total,
        ]);
    }

    /**
     * Create an audit trail record before deletion.
     */
    public function logDeletion(?User $deletedBy, string $reason): OrderDeletionLog
    {
        return $this->deletionLogs()->create([
            'order_number' => $this->order_number,
            'deleted_by' => $deletedBy?->id,
            'reason' => $reason,
            'order_data' => array_merge($this->toArray(), [
                'items' => $this->items()->get()->toArray(),
            ]),
        ]);
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    #[Scope]
    protected function completed(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    #[Scope]
    protected function paid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }
}
