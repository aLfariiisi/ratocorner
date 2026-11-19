@extends('layouts.admin')

@section('title', 'Manajemen Meja QR')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Meja QR</h1>
        <p class="text-sm text-gray-500">Kelola nomor meja, kapasitas, dan cetak QR Code pemesanan mandiri pelanggan.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.tables.print-all') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-orange-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-orange-50 transition shadow-sm">
            <i class="fa-solid fa-print text-primary"></i> Cetak Semua QR
        </a>
        <a href="{{ route('admin.tables.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition shadow-sm">
            <i class="fa-solid fa-plus"></i> Tambah Meja
        </a>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-orange-100 mb-6">
    <form method="GET" action="{{ route('admin.tables.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no meja, nama, area..." class="w-full sm:w-64 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-primary">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-primary text-gray-600">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="occupied" {{ request('status') === 'occupied' ? 'selected' : '' }}>Terisi</option>
                <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reservasi</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.tables.index') }}" class="text-xs text-gray-500 hover:text-red-500 underline">Reset</a>
            @endif
        </div>
        <div class="text-xs text-gray-500">
            Total: <span class="font-bold text-gray-800">{{ $tables->total() }}</span> Meja Terdaftar
        </div>
    </form>
</div>

<!-- Grid Kartu Meja -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @forelse($tables as $table)
        @php
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode(route('order.table', $table->qr_code_token));
            $statusBadge = match($table->status) {
                'available' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'occupied' => 'bg-red-100 text-red-700 border-red-200',
                'reserved' => 'bg-amber-100 text-amber-700 border-amber-200',
                default => 'bg-gray-100 text-gray-700 border-gray-200',
            };
            $statusLabel = match($table->status) {
                'available' => 'Tersedia',
                'occupied' => 'Terisi',
                'reserved' => 'Reservasi',
                default => ucfirst($table->status),
            };
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xl font-bold font-mono text-gray-800 bg-orange-50 px-3 py-1 rounded-lg border border-orange-200">
                        Meja {{ $table->table_number }}
                    </span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border {{ $statusBadge }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <p class="text-sm font-semibold text-gray-700 truncate mb-1">{{ $table->name ?? 'Meja Pelanggan' }}</p>
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                    <span><i class="fa-solid fa-users mr-1"></i> {{ $table->capacity }} Orang</span>
                    <span><i class="fa-solid fa-location-dot mr-1"></i> {{ $table->location ?? 'Indoor' }}</span>
                </div>

                <!-- Preview QR Code -->
                <div class="bg-orange-50/50 p-3 rounded-lg flex flex-col items-center justify-center border border-orange-100 mb-3">
                    <img src="{{ $qrUrl }}" alt="QR Meja {{ $table->table_number }}" class="w-32 h-32 object-contain bg-white p-1 rounded shadow-inner">
                    <a href="{{ route('order.table', $table->qr_code_token) }}" target="_blank" class="text-[11px] text-primary hover:underline mt-2 font-mono flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> /order/{{ Str::limit($table->qr_code_token, 14) }}
                    </a>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.tables.show', $table) }}" title="Lihat Detail QR" class="p-1.5 text-gray-600 hover:text-primary transition rounded hover:bg-white">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.tables.print', $table) }}" target="_blank" title="Cetak Stand Meja" class="p-1.5 text-primary hover:text-primary transition rounded hover:bg-white">
                        <i class="fa-solid fa-print"></i>
                    </a>
                    <a href="{{ route('admin.tables.edit', $table) }}" title="Edit Meja" class="p-1.5 text-gray-600 hover:text-blue-600 transition rounded hover:bg-white">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                </div>

                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Meja {{ $table->table_number }}?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Hapus Meja" class="p-1.5 text-red-500 hover:text-red-700 transition rounded hover:bg-white">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-8 rounded-xl border border-orange-100 text-center text-gray-500">
            <i class="fa-solid fa-chair text-4xl text-orange-200 mb-3"></i>
            <p class="font-medium">Belum ada meja yang terdaftar.</p>
            <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Meja" untuk menambahkan meja QR baru.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $tables->links() }}
</div>
@endsection
