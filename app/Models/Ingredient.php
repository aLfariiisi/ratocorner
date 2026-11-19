<?php

namespace App\Models;

use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'vendor_id',
    'name',
    'code',
    'unit',
    'current_stock',
    'minimum_stock',
    'cost_per_unit',
    'description',
])]
class Ingredient extends Model
{
    /** @use HasFactory<IngredientFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
            'cost_per_unit' => 'decimal:2',
        ];
    }

    /**
     * Vendor who supplies this ingredient.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Recipe lines referencing this ingredient.
     */
    public function productRecipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    /**
     * Products using this ingredient.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_recipes')
            ->withPivot(['quantity', 'unit', 'cost'])
            ->withTimestamps();
    }

    /**
     * Stock opnames for this ingredient.
     */
    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockOpname::class);
    }

    /**
     * Check if current stock is at or below minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    #[Scope]
    protected function lowStock(Builder $query): Builder
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }
}
