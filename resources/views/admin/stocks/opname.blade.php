@extends('layouts.admin')

@section('title', 'Stok Opname Manual')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Stok Opname Manual</h1>
        <p class="text-sm text-gray-500">Pencocokan jumlah stok fisik di bar/gudang dengan stok yang tercatat di sistem.</p>
    </div>
    <a href="{{ route('admin.stocks.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Stok
    </a>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Input Hasil Hitung Fisik -->
    <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 h-fit">
        <h2 class="text-base font-bold text-gray-800 mb-4">Input Hasil Hitung Fisik</h2>
        <form action="{{ route('admin.stock-opnames.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Bahan Baku</label>
                <select name="ingredient_id" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">-- Pilih Bahan Baku --</option>
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}">
                            {{ $ing->name }} (Sistem: {{ number_format($ing->current_stock, 2) }} {{ $ing->unit }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Stok Fisik Aktual</label>
                <input type="number" name="physical_stock" step="any" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Jumlah hasil hitung fisik">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Alasan Selisih / Catatan Audit</label>
                <textarea name="notes" rows="3" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Contoh: Susut penyimpanan, tumpah saat shift pagi, atau expired"></textarea>
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                <i class="fa-solid fa-check-double mr-1"></i> Simpan & Sesuaikan Stok
            </button>
        </form>
    </div>

    <!-- Tabel Riwayat Stok Opname -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                        <th class="py-3 px-4">Waktu & Petugas</th>
                        <th class="py-3 px-4">Bahan Baku</th>
                        <th class="py-3 px-4 text-center">Sistem &rarr; Fisik</th>
                        <th class="py-3 px-4 text-center">Selisih</th>
                        <th class="py-3 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($opnames as $op)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 text-xs text-gray-600">
                            {{ $op->created_at->format('d M Y H:i') }}
                            <span class="block font-semibold text-gray-800">{{ $op->conductedBy->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 font-semibold text-gray-800">{{ $op->ingredient->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center text-xs">
                            {{ number_format($op->system_stock, 2) }} &rarr; <strong>{{ number_format($op->physical_stock, 2) }}</strong>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $op->difference < 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $op->difference > 0 ? '+' : '' }}{{ number_format($op->difference, 2) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $op->notes }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-400">Belum ada riwayat stok opname.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $opnames->links() }}
        </div>
    </div>
</div>
@endsection
