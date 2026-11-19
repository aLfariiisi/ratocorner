<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Vendor;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(): View
    {
        $stocks = Ingredient::with('vendor')->latest()->paginate(15);
        $lowStocks = Ingredient::lowStock()->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('admin.stocks.index', compact('stocks', 'lowStocks', 'vendors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
        ]);

        Ingredient::create($validated);

        return back()->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, Ingredient $ingredient, StockService $stockService): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
        ]);

        DB::transaction(function () use ($ingredient, $validated, $stockService) {
            $stockService->restock(
                $ingredient,
                (float) $validated['quantity'],
                (float) $validated['unit_cost'],
                $validated['vendor_id'] ?? null
            );

            foreach ($ingredient->products as $product) {
                foreach ($product->recipes()->where('ingredient_id', $ingredient->id)->get() as $recipe) {
                    $recipe->update([
                        'cost' => round($recipe->quantity * $ingredient->fresh()->cost_per_unit, 2),
                    ]);
                }
                $product->refreshHpp();
            }
        });

        return back()->with('success', "Restock {$ingredient->name} berhasil dan HPP produk terkait telah diperbarui.");
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $ingredient->delete();

        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}
