@extends('layouts.admin')

@section('title', 'Pembayaran & Transaksi')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pembayaran & Kasir</h1>
        <p class="text-sm text-gray-500">Proses pelunasan tagihan meja pelanggan dan pantau riwayat pembayaran masuk.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
    <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Proses Pembayaran Cepat -->
    <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 h-fit">
        <h2 class="text-base font-bold text-gray-800 mb-4"><i class="fa-solid fa-cash-register text-orange-600 mr-1.5"></i> Konfirmasi Pelunasan</h2>
        <form action="{{ route('admin.payments.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Pesanan Belum Bayar</label>
                <select name="order_id" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">-- Pilih Nomor Order / Meja --</option>
                    @foreach($orders as $unpaid)
                        <option value="{{ $unpaid->id }}">
                            {{ $unpaid->order_number }} - Meja {{ $unpaid->table->table_number ?? '-' }} ({{ $unpaid->customer_name ?? 'Guest' }}) - Rp {{ number_format($unpaid->total_amount, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Metode Pembayaran Diterima</label>
                <select name="payment_method" required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="cash">Tunai / Cash</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank / Debit</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                <i class="fa-solid fa-check-circle mr-1"></i> Konfirmasi Lunas & Potong Stok
            </button>
        </form>
    </div>

    <!-- Tabel Riwayat Pembayaran -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                        <th class="py-3 px-4">No. Order</th>
                        <th class="py-3 px-4">Meja & Pelanggan</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Total Tagihan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($payments as $pay)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 font-mono font-bold text-gray-800 text-xs">
                            {{ $pay->order_number }}
                            <span class="block font-sans font-normal text-gray-400">{{ $pay->created_at->format('d M H:i') }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <span class="font-bold text-gray-800">Meja {{ $pay->table->table_number ?? '-' }}</span>
                            <span class="block text-gray-500">{{ $pay->customer_name ?? 'Guest' }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs uppercase font-semibold text-gray-600">{{ $pay->payment_method }}</td>
                        <td class="py-3 px-4 font-bold text-gray-800">Rp {{ number_format($pay->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($pay->payment_status === 'paid')
                                <span class="bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-full text-xs font-bold">LUNAS</span>
                            @else
                                <span class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full text-xs font-bold">PENDING</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('admin.orders.receipt', $pay->id) }}" target="_blank" class="inline-block bg-gray-800 text-white px-2.5 py-1 rounded text-xs hover:bg-gray-900">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data transaksi pembayaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
