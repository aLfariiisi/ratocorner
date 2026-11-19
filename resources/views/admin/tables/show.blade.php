@extends('layouts.admin')

@section('title', 'Detail QR Meja ' . $table->table_number)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('admin.tables.index') }}" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-semibold mb-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Meja
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail QR Meja {{ $table->table_number }}</h1>
        <p class="text-sm text-gray-500">{{ $table->name ?? 'Meja Pelanggan' }} - Area {{ $table->location ?? 'Indoor' }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.tables.print', $table) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition shadow-sm">
            <i class="fa-solid fa-print"></i> Cetak Kartu Stand Meja
        </a>
        <a href="{{ route('admin.tables.edit', $table) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">
            <i class="fa-solid fa-pen-to-square"></i> Edit
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- QR Card Display -->
    <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 flex flex-col items-center text-center">
        @php
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=" . urlencode($orderUrl);
        @endphp

        <div class="p-4 bg-white border-2 border-orange-200 rounded-2xl shadow-sm mb-4">
            <img src="{{ $qrUrl }}" alt="QR Meja {{ $table->table_number }}" class="w-64 h-64 object-contain">
        </div>

        <span class="text-xs text-gray-400 font-mono mb-2">Scan dengan Kamera / Google Lens</span>
        <a href="{{ $orderUrl }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline break-all">
            <i class="fa-solid fa-link"></i> {{ $orderUrl }}
        </a>
    </div>

    <!-- Info Detail Meja & Pesanan Aktif -->
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi Meja</h2>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 text-xs">Nomor Meja</dt>
                    <dd class="font-bold text-gray-800 text-lg">Meja {{ $table->table_number }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Kapasitas Kursi</dt>
                    <dd class="font-semibold text-gray-800">{{ $table->capacity }} Orang</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Lokasi / Area</dt>
                    <dd class="font-semibold text-gray-800">{{ $table->location ?? 'Indoor' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Status Saat Ini</dt>
                    <dd>
                        @php
                            $statusBadge = match($table->status) {
                                'available' => 'bg-emerald-100 text-emerald-700',
                                'occupied' => 'bg-red-100 text-red-700',
                                'reserved' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $statusLabel = match($table->status) {
                                'available' => 'Tersedia (Ready)',
                                'occupied' => 'Terisi Pelanggan',
                                'reserved' => 'Direservasi',
                                default => ucfirst($table->status),
                            };
                        @endphp
                        <span class="inline-block text-xs font-bold px-2.5 py-0.5 rounded-full {{ $statusBadge }}">
                            {{ $statusLabel }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Token QR Pemesanan</dt>
                    <dd class="font-mono text-xs text-gray-600 bg-gray-50 p-2 rounded border truncate">{{ $table->qr_code_token }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 text-xs">Status Pemesanan Online</dt>
                    <dd class="font-semibold {{ $table->is_active ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $table->is_active ? 'Aktif (Dapat Dipesan)' : 'Nonaktif' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Pesanan Terakhir di Meja Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Pesanan Aktif di Meja Ini</h2>
            
            @php
                $activeOrder = $table->activeOrder;
            @endphp

            @if($activeOrder)
                <div class="p-4 bg-orange-50/50 rounded-lg border border-orange-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-primary">{{ $activeOrder->order_number }}</span>
                        <p class="font-semibold text-gray-800 text-sm">Customer: {{ $activeOrder->customer_name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-500">{{ $activeOrder->created_at->format('d M Y, H:i') }} - Status: <span class="font-bold uppercase">{{ $activeOrder->status }}</span></p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-gray-800">Rp {{ number_format($activeOrder->total_amount, 0, ',', '.') }}</span>
                        <span class="block text-xs font-semibold {{ $activeOrder->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ strtoupper($activeOrder->payment_status) }}
                        </span>
                    </div>
                </div>
            @else
                <div class="text-center py-6 text-gray-400 text-sm">
                    <i class="fa-solid fa-mug-saucer text-2xl mb-1 text-gray-300"></i>
                    <p>Tidak ada pesanan aktif di meja ini saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
