<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockOpname;

class StockService
{
    public function restock(Ingredient $ingredient, float $quantity, float $unitCost, ?int $vendorId = null): void
    {
        $oldStock = $ingredient->current_stock;
        $oldCost = $ingredient->cost_per_unit;
        $newStock = $oldStock + $quantity;
        $newCost = $newStock > 0
            ? (($oldStock * $oldCost) + ($quantity * $unitCost)) / $newStock
            : $unitCost;

        $ingredient->current_stock = $newStock;
        $ingredient->cost_per_unit = round($newCost, 2);
        if ($vendorId) {
            $ingredient->vendor_id = $vendorId;
        }
        $ingredient->save();
    }

    public function applyOpname(StockOpname $opname): void
    {
        $ingredient = $opname->ingredient;
        $ingredient->current_stock = $opname->physical_stock;
        $ingredient->save();
    }
}
