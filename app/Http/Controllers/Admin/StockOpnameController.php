<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockOpname;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function index(): View
    {
        $opnames = StockOpname::with(['ingredient', 'conductedBy'])->latest()->paginate(15);
        $ingredients = Ingredient::orderBy('name')->get();

        return view('admin.stocks.opname', compact('opnames', 'ingredients'));
    }

    public function store(Request $request, StockService $stockService): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'physical_stock' => ['required', 'numeric', 'min:0'],
            'notes' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $stockService) {
            $ingredient = Ingredient::findOrFail($validated['ingredient_id']);
            $difference = (float) $validated['physical_stock'] - (float) $ingredient->current_stock;

            $opname = StockOpname::create([
                'ingredient_id' => $ingredient->id,
                'conducted_by' => Auth::id(),
                'system_stock' => $ingredient->current_stock,
                'physical_stock' => $validated['physical_stock'],
                'difference' => $difference,
                'cost_difference' => round($difference * $ingredient->cost_per_unit, 2),
                'notes' => $validated['notes'],
                'opname_date' => now(),
            ]);

            $stockService->applyOpname($opname);
        });

        return back()->with('success', 'Stok opname berhasil disimpan dan stok sistem telah disesuaikan.');
    }
}
