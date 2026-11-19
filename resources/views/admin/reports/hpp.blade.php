@extends('layouts.admin')

@section('title', 'Laporan HPP & Profit Margin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Analisis HPP & Profit Margin</h1>
        <p class="text-sm text-gray-500">Evaluasi harga pokok penjualan, harga jual, dan tingkat keuntungannya per porsi menu.</p>
    </div>
    <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
        <i class="fa-solid fa-print mr-1"></i> Cetak / PDF
    </button>
</div>

<!-- KARTU RINGKASAN STATISTIK -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-100">
        <p class="text-xs text-gray-500 font-medium uppercase">Total Menu Aktif</p>
        <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $totalProducts }} Menu</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-100">
        <p class="text-xs text-gray-500 font-medium uppercase">Rata-rata HPP / Porsi</p>
        <h3 class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($avgHpp, 0, ',', '.') }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-100">
        <p class="text-xs text-gray-500 font-medium uppercase">Rata-rata Harga Jual</p>
        <h3 class="text-xl font-bold text-orange-600 mt-1">Rp {{ number_format($avgPrice, 0, ',', '.') }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-orange-100">
        <p class="text-xs text-gray-500 font-medium uppercase">Rata-rata Profit Margin</p>
        <h3 class="text-xl font-bold text-emerald-600 mt-1">{{ $avgMargin }}%</h3>
    </div>
</div>

<!-- TABEL UTAMA LAPORAN HPP -->
<div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                <th class="py-3 px-6">Nama Menu / Produk</th>
                <th class="py-3 px-6">Kategori</th>
                <th class="py-3 px-6 text-right">HPP (Resep)</th>
                <th class="py-3 px-6 text-right">Harga Jual</th>
                <th class="py-3 px-6 text-right">Laba Kotor (Profit)</th>
                <th class="py-3 px-6 text-center">Margin (%)</th>
                <th class="py-3 px-6 text-center">Aksi Resep</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition">
                <td class="py-4 px-6 font-semibold text-gray-800">
                    {{ $product->name }}
                    <div class="text-xs font-normal text-gray-400">SKU: {{ $product->sku }}</div>
                </td>
                <td class="py-4 px-6 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                
                <!-- HPP -->
                <td class="py-4 px-6 text-right font-medium text-gray-700">
                    Rp {{ number_format($product->calculated_hpp, 0, ',', '.') }}
                </td>
                
                <!-- Harga Jual -->
                <td class="py-4 px-6 text-right font-bold text-orange-600">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </td>
                
                <!-- Laba Kotor (Profit per porsi) -->
                <td class="py-4 px-6 text-right font-semibold text-emerald-600">
                    Rp {{ number_format($product->gross_profit, 0, ',', '.') }}
                </td>
                
                <!-- Margin Persen -->
                <td class="py-4 px-6 text-center">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $product->profit_margin >= 40 ? 'bg-emerald-100 text-emerald-700' : ($product->profit_margin >= 20 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ $product->profit_margin }}%
                    </span>
                </td>
                
                <!-- Aksi -->
                <td class="py-4 px-6 text-center">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 text-xs font-semibold bg-blue-50 px-2.5 py-1.5 rounded-lg transition">
                        <i class="fa-solid fa-utensils mr-1"></i> Atur Resep
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-6 text-center text-gray-400">Belum ada data menu produk terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection