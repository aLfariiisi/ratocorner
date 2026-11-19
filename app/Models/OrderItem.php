<?php

namespace App\Models;

use Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'product_id',
    'product_name',
    'quantity',
    'unit_price',
    'hpp',
    'subtotal',
    'notes',
])]
class OrderItem extends Model
{
    /** @use HasFactory<OrderItemFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'hpp' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item) {
            if ($item->product) {
                if (empty($item->product_name)) {
                    $item->product_name = $item->product->name;
                }
                if ((float) $item->unit_price === 0.0) {
                    $item->unit_price = (float) $item->product->price;
                }
                if ((float) $item->hpp === 0.0) {
                    $item->hpp = (float) $item->product->hpp;
                }
            }

            $item->subtotal = round((float) $item->unit_price * (int) $item->quantity, 2);
        });
    }

    /**
     * Order this item belongs to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Product referenced by this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
