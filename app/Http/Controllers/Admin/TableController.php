<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTableRequest;
use App\Http\Requests\Admin\UpdateTableRequest;
use App\Models\CafeSetting;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index(Request $request): View
    {
        $query = Table::query()->withCount(['orders' => function ($q) {
            $q->whereNotIn('status', ['completed', 'cancelled']);
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $tables = $query->orderBy('table_number')->paginate(12)->withQueryString();
        $cafeSetting = CafeSetting::getSettings();

        return view('admin.tables.index', compact('tables', 'cafeSetting'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create(): View
    {
        return view('admin.tables.create');
    }

    /**
     * Store a newly created table in storage.
     */
    public function store(StoreTableRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['qr_code_token'] = 'tbl-'.strtolower($validated['table_number']).'-'.Str::random(10);
        $validated['is_active'] = $request->boolean('is_active', true);

        Table::create($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja '.$validated['table_number'].' berhasil ditambahkan dengan QR unik.');
    }

    /**
     * Display the specified table and its QR Code.
     */
    public function show(Table $table): View
    {
        $cafeSetting = CafeSetting::getSettings();
        $orderUrl = route('order.table', $table->qr_code_token);

        return view('admin.tables.show', compact('table', 'cafeSetting', 'orderUrl'));
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Table $table): View
    {
        return view('admin.tables.edit', compact('table'));
    }

    /**
     * Update the specified table in storage.
     */
    public function update(UpdateTableRequest $request, Table $table): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $table->update($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Informasi Meja '.$table->table_number.' berhasil diperbarui.');
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy(Table $table): RedirectResponse
    {
        // Check for active orders
        if ($table->orders()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            return redirect()->route('admin.tables.index')
                ->with('error', 'Meja '.$table->table_number.' tidak dapat dihapus karena masih memiliki pesanan aktif.');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja '.$table->table_number.' berhasil dihapus.');
    }

    /**
     * Printable view of single table QR stand card.
     */
    public function print(Table $table): View
    {
        $cafeSetting = CafeSetting::getSettings();
        $orderUrl = route('order.table', $table->qr_code_token);

        return view('admin.tables.print', compact('table', 'cafeSetting', 'orderUrl'));
    }

    /**
     * Printable view of all active tables.
     */
    public function printAll(): View
    {
        $tables = Table::where('is_active', true)->orderBy('table_number')->get();
        $cafeSetting = CafeSetting::getSettings();

        return view('admin.tables.print-all', compact('tables', 'cafeSetting'));
    }
}
