<?php

namespace App\Models;

use Database\Factories\OrderDeletionLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'order_number',
    'deleted_by',
    'reason',
    'order_data',
])]
class OrderDeletionLog extends Model
{
    /** @use HasFactory<OrderDeletionLogFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_data' => 'array',
        ];
    }

    /**
     * The order that was deleted (if still soft-deleted).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    /**
     * User who performed the deletion.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
