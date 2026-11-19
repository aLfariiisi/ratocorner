@extends('layouts.admin')

@section('title', 'Data Vendor / Supplier')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Data Vendor / Supplier</h1>
        <p class="text-sm text-gray-500">Daftar pemasok bahan baku (biji kopi, susu, sirup, kemasan) Ratocorner.</p>
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
    <!-- Form Tambah Vendor -->
    <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 h-fit">
        <h2 class="text-base font-bold text-gray-800 mb-4">Tambah Vendor Baru</h2>
        <form action="{{ route('admin.vendors.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Vendor / PT</label>
                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Contoh: Roastery Nusantara">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kontak (PIC)</label>
                <input type="text" name="contact_person" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Contoh: Budi Santoso">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">No. Telepon / WhatsApp</label>
                <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="0812xxxxxxxx">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Email (Opsional)</label>
                <input type="email" name="email" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="vendor@email.com">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Alamat lengkap supplier"></textarea>
            </div>
            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition">
                <i class="fa-solid fa-save mr-1"></i> Simpan Vendor
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Vendor -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-orange-50 text-gray-700 text-xs uppercase tracking-wider border-b border-orange-100">
                        <th class="py-3 px-6">Nama Vendor</th>
                        <th class="py-3 px-6">PIC & Kontak</th>
                        <th class="py-3 px-6 text-center">Bahan Disuplai</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($vendors as $vendor)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 font-semibold text-gray-800">
                            {{ $vendor->name }}
                            @if($vendor->address)
                                <span class="block text-xs font-normal text-gray-400">{{ $vendor->address }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-gray-600">
                            {{ $vendor->contact_person ?? '-' }}
                            <span class="block text-xs text-gray-400">{{ $vendor->phone ?? '' }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="bg-orange-50 text-orange-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                                {{ $vendor->ingredients_count }} Item
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" onsubmit="return confirm('Hapus data vendor ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-400">Belum ada data vendor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection
