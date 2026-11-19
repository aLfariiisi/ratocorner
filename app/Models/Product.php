<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'sku',
    'description',
    'price',
    'hpp',
    'image_path',
    'is_available',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'hpp' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Recipes detailing the ingredients and portion sizes.
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    /**
     * Ingredients in this product's recipe.
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'product_recipes')
            ->withPivot(['quantity', 'unit', 'cost'])
            ->withTimestamps();
    }

    /**
     * Order items referencing this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Calculate total HPP (Harga Pokok Penjualan) from recipe components.
     */
    public function calculateHpp(): float
    {
        return (float) $this->recipes()->sum('cost');
    }

    /**
     * Refresh and persist HPP based on current recipe costs.
     */
    public function refreshHpp(): void
    {
        $this->update([
            'hpp' => $this->calculateHpp(),
        ]);
    }

    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }
}
