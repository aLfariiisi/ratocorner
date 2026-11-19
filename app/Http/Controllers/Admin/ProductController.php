<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'recipes.ingredient'])->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
            'recipes' => ['nullable', 'array'],
            'recipes.*.ingredient_id' => ['required_with:recipes', 'exists:ingredients,id'],
            'recipes.*.quantity' => ['required_with:recipes', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('products', 'public')
                : null;

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
                'sku' => $validated['sku'] ?? 'MENU-' . strtoupper(Str::random(5)),
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'hpp' => 0,
                'image_path' => $imagePath,
                'is_available' => $request->boolean('is_available', true),
            ]);

            if (! empty($validated['recipes'])) {
                foreach ($validated['recipes'] as $item) {
                    $ingredient = Ingredient::findOrFail($item['ingredient_id']);
                    $product->recipes()->create([
                        'ingredient_id' => $ingredient->id,
                        'quantity' => $item['quantity'],
                        'unit' => $ingredient->unit,
                        'cost' => round($item['quantity'] * $ingredient->cost_per_unit, 2),
                    ]);
                }
            }

            $product->refreshHpp();
        });

        return redirect()->route('admin.products.index')->with('success', 'Menu produk & resep HPP berhasil dibuat.');
    }

    public function edit(Product $product): View
    {
        $product->load('recipes.ingredient');
        $categories = Category::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'ingredients'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:products,sku,' . $product->id],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_available' => ['boolean'],
            'recipes' => ['nullable', 'array'],
            'recipes.*.ingredient_id' => ['required_with:recipes', 'exists:ingredients,id'],
            'recipes.*.quantity' => ['required_with:recipes', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($request, $validated, $product) {
            $data = [
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'sku' => $validated['sku'] ?? $product->sku,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'is_available' => $request->boolean('is_available', true),
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);
            $product->recipes()->delete();

            if (! empty($validated['recipes'])) {
                foreach ($validated['recipes'] as $item) {
                    $ingredient = Ingredient::findOrFail($item['ingredient_id']);
                    $product->recipes()->create([
                        'ingredient_id' => $ingredient->id,
                        'quantity' => $item['quantity'],
                        'unit' => $ingredient->unit,
                        'cost' => round($item['quantity'] * $ingredient->cost_per_unit, 2),
                    ]);
                }
            }

            $product->refreshHpp();
        });

        return redirect()->route('admin.products.index')->with('success', 'Menu produk & resep HPP berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
