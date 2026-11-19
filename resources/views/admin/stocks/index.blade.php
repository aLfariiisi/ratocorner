@extends('layouts.admin')

@section('title', 'Manajemen Stok Bahan Baku')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Stok Bahan Baku</h1>
        <p class="text-sm text-gray-500">Kelola persediaan bahan mentah, restock vendor, dan harga modal untuk kalkulasi HPP.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.vendors.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
            <i class="fa-solid fa-truck-field mr-1"></i> Data Vendor
        </a>
        <a href="{{ route('admin.stock-opnames.index') }}" class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-100 transition">
            <i class="fa-solid fa-clipboard-check mr-1"></i> Stok Opname
        </a>
        <button onclick="document.getElementById('addIngredientModal').classList.remove('hidden')" class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
            <i class="fa-solid fa-plus mr-1"></i> Tambah Bahan Baku
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
</div>
@endif

<!-- Peringatan Stok Rendah -->
@if($lowStocks->count() > 0)
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700">
    <div class="flex items-center gap-2 font-bold mb-1">
        <i class="fa-solid fa-triangle-exclamation"></i> Peringatan Stok Bahan Baku Menipis!
    </div>
    <p class="text-xs">Terdapat {{ $lowStocks->count() }} bahan baku yang stoknya berada di bawah batas minimum.</p>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                    <th class="py-3 px-6">Nama Bahan Baku</th>
                    <th class="py-3 px-6">Vendor</th>
                    <th class="py-3 px-6">Satuan</th>
                    <th class="py-3 px-6">Harga Modal / Satuan</th>
                    <th class="py-3 px-6 text-center">Stok Saat Ini</th>
                    <th class="py-3 px-6 text-center">Min. Alert</th>
                    <th class="py-3 px-6 text-center">Restock dari Vendor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($stocks as $stock)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-6 font-semibold text-gray-800">{{ $stock->name }}</td>
                    <td class="py-4 px-6 text-gray-600 text-xs">{{ $stock->vendor->name ?? '-' }}</td>
                    <td class="py-4 px-6 text-gray-500 text-xs"><span class="bg-gray-100 px-2 py-1 rounded">{{ $stock->unit }}</span></td>
                    <td class="py-4 px-6 text-gray-700 font-medium">Rp {{ number_format($stock->cost_per_unit, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-center font-bold {{ $stock->isLowStock() ? 'text-red-500' : 'text-gray-700' }}">
                        {{ number_format($stock->current_stock, 2) }}
                    </td>
                    <td class="py-4 px-6 text-center text-gray-500">{{ number_format($stock->minimum_stock, 2) }}</td>
                    <td class="py-4 px-6 text-center">
                        <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST" class="flex justify-center items-center gap-1.5">
                            @csrf
                            @method('PUT')
                            <input type="number" name="quantity" step="any" min="0.01" required class="w-20 border rounded px-2 py-1 text-xs text-center" placeholder="+Qty">
                            <input type="number" name="unit_cost" value="{{ (int) $stock->cost_per_unit }}" step="any" min="0" required class="w-24 border rounded px-2 py-1 text-xs text-center" placeholder="Harga/unit">
                            <button type="submit" class="bg-orange-500 text-white px-3 py-1 rounded text-xs font-medium hover:bg-orange-600">+ Restock</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center text-gray-400">Belum ada data bahan baku.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $stocks->links() }}
    </div>
</div>

<!-- Modal Tambah Bahan Baku -->
<div id="addIngredientModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
        <h2 class="text-lg font-bold mb-4 text-gray-800">Tambah Bahan Baku Baru</h2>
        <form action="{{ route('admin.stocks.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Bahan Baku</label>
                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Contoh: Biji Kopi House Blend">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Vendor / Supplier</label>
                <select name="vendor_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Vendor (Opsional) --</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Satuan (Unit)</label>
                <input type="text" name="unit" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Contoh: gram, ml, pcs">
            </div>
            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Harga Beli per Satuan (Rp)</label>
                <input type="number" name="cost_per_unit" required step="any" min="0" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Contoh: 250">
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Stok Awal</label>
                    <input type="number" name="current_stock" required step="any" min="0" class="w-full border rounded-lg px-3 py-2 text-sm" value="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Min. Alert Stok</label>
                    <input type="number" name="minimum_stock" required step="any" min="0" class="w-full border rounded-lg px-3 py-2 text-sm" value="100">
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addIngredientModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-semibold hover:bg-orange-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
