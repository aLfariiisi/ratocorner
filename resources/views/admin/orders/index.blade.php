@extends('layouts.admin')

@section('title', 'Operasional Pesanan & Kasir')

@section('content')

<script src="{{ asset('js/admin-orders.js') }}"></script>

    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pesanan Masuk & Kasir</h1>
            <p class="text-sm text-gray-500">Kelola pesanan QR meja, konfirmasi pembayaran (potong stok otomatis), cetak
                struk, dan audit pembatalan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.index') }}"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('status') ? 'bg-orange-600 text-white' : 'bg-white border text-gray-600' }}">Semua</a>
            <a href="{{ route('admin.orders.index', ['status' => 'unpaid']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'unpaid' ? 'bg-amber-600 text-white' : 'bg-white border text-gray-600' }}">Belum
                Bayar</a>
            <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('status') === 'paid' ? 'bg-emerald-600 text-white' : 'bg-white border text-gray-600' }}">Lunas</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 text-sm">
            <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                        <th class="py-3 px-4">No. Order & Waktu</th>
                        <th class="py-3 px-4">Meja & Pelanggan</th>
                        <th class="py-3 px-4">Item Pesanan</th>
                        <th class="py-3 px-4">Total & Metode</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Kasir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-gray-800 block">{{ $order->order_number }}</span>
                                <span class="text-xs text-gray-400">{{ $order->created_at->format('d M H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="bg-orange-100 text-orange-800 px-2 py-0.5 rounded text-xs font-bold">
                                    Meja {{ $order->table->table_number ?? '-' }}
                                </span>
                                <span
                                    class="font-semibold text-gray-800 block mt-1">{{ $order->customer_name ?? 'Guest' }}</span>
                                @if($order->notes)
                                    <span class="text-[11px] text-amber-700 italic block">Catatan: {{ $order->notes }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-gray-600">
                                <ul class="space-y-0.5">
                                    @foreach($order->items as $item)
                                        <li><strong>{{ $item->quantity }}x</strong> {{ $item->product_name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-gray-800 block">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                <span
                                    class="text-[11px] uppercase text-gray-500 font-semibold">{{ $order->payment_method }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($order->payment_status === 'paid')
                                    <span
                                        class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-bold">LUNAS</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-bold">BELUM
                                        BAYAR</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($order->payment_status !== 'paid')
                                    <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Konfirmasi pembayaran lunas? Stok bahan baku akan otomatis terpotong.')">
                                        @csrf
                                        <button type="submit"
                                            class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-emerald-700">
                                            <i class="fa-solid fa-check mr-1"></i> Bayar & Selesai
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.orders.receipt', $order->id) }}" target="_blank"
                                    class="inline-block bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-900">
                                    <i class="fa-solid fa-print mr-1"></i> Struk
                                </a>

                                <button type="button"
                                    onclick="openDeleteModal('{{ $order->id }}', '{{ $order->order_number }}')"
                                    class="bg-red-50 text-red-600 border border-red-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 hover:text-white transition">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
        <div class="p-4 bg-red-50/60 border-b border-red-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-red-800"><i class="fa-solid fa-shield-halved mr-1"></i> Log Audit
                    Penghapusan Pesanan (Extra Tracking)</h2>
                <p class="text-xs text-red-600">Setiap pesanan yang dihapus oleh kasir tercatat permanen beserta pelaku,
                    nominal, item, dan alasannya.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase border-b">
                        <th class="py-2.5 px-4">Waktu Hapus</th>
                        <th class="py-2.5 px-4">No. Order</th>
                        <th class="py-2.5 px-4">Dihapus Oleh</th>
                        <th class="py-2.5 px-4">Alasan Penghapusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($deletionLogs as $log)
                        <tr>
                            <td class="py-2.5 px-4 text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-gray-800">
                                {{ $log->order_number ?? ('#' . $log->order_id) }}</td>
                            <td class="py-2.5 px-4 font-semibold text-gray-700">{{ $log->deletedBy->name ?? 'System' }}</td>
                            <td class="py-2.5 px-4 text-red-700 font-medium">{{ $log->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-400">Belum ada riwayat penghapusan pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="deleteOrderModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Hapus Pesanan <span id="delOrderNumber"
                    class="font-mono text-red-600"></span></h3>
            <p class="text-xs text-gray-500 mb-4">Tindakan ini akan direkam ke Log Audit sistem beserta nama akun Anda.</p>
            <form id="deleteOrderForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Penghapusan (Wajib Diisi) <span
                            class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required minlength="5"
                        class="w-full border rounded-lg px-3 py-2 text-sm"
                        placeholder="Contoh: Pelanggan membatalkan pesanan sebelum dibuat, atau salah input meja"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('deleteOrderModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700">Hapus &
                        Catat Log</button>
                </div>
            </form>
        </div>
    </div>
@endsection