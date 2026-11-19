<?php

namespace App\Models;

use Database\Factories\ProductRecipeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'ingredient_id',
    'quantity',
    'unit',
    'cost',
    'notes',
])]
class ProductRecipe extends Model
{
    /** @use HasFactory<ProductRecipeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ProductRecipe $recipe) {
            if ($recipe->ingredient && (float) $recipe->cost === 0.0) {
                $recipe->cost = round((float) $recipe->quantity * (float) $recipe->ingredient->cost_per_unit, 2);
            }
        });

        static::saved(function (ProductRecipe $recipe) {
            $recipe->product?->refreshHpp();
        });

        static::deleted(function (ProductRecipe $recipe) {
            $recipe->product?->refreshHpp();
        });
    }

    /**
     * Product this recipe belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Ingredient used in this recipe.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
