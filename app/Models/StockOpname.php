<?php

namespace App\Models;

use Database\Factories\StockOpnameFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ingredient_id',
    'user_id',
    'opname_date',
    'system_stock',
    'physical_stock',
    'difference',
    'cost_difference',
    'status',
    'notes',
])]
class StockOpname extends Model
{
    /** @use HasFactory<StockOpnameFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opname_date' => 'date',
            'system_stock' => 'decimal:2',
            'physical_stock' => 'decimal:2',
            'difference' => 'decimal:2',
            'cost_difference' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StockOpname $opname) {
            $opname->difference = round((float) $opname->physical_stock - (float) $opname->system_stock, 2);

            if ($opname->ingredient) {
                $opname->cost_difference = round((float) $opname->difference * (float) $opname->ingredient->cost_per_unit, 2);
            }
        });
    }

    /**
     * Ingredient being audited.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Staff/user who conducted this stock opname.
     */
    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
