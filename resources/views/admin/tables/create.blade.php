@extends('layouts.admin')

@section('title', 'Tambah Meja Baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tables.index') }}" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-semibold mb-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Meja
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Tambah Meja QR Baru</h1>
    <p class="text-sm text-gray-500">Buat meja baru, kode QR unik untuk pemesanan akan otomatis dibuatkan oleh sistem.</p>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow-sm border border-orange-100 p-6">
    <form action="{{ route('admin.tables.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja <span class="text-red-500">*</span></label>
                <input type="text" name="table_number" value="{{ old('table_number') }}" required placeholder="Contoh: 01, VIP-1, OUT-02" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('table_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama / Label Meja</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Meja 01 (Dekat Jendela)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1" max="50" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('capacity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Area / Lokasi</label>
                <input type="text" name="location" value="{{ old('location', 'Indoor') }}" placeholder="Contoh: Indoor, Outdoor, Lantai 2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('location') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Awal</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                    <option value="occupied" {{ old('status') === 'occupied' ? 'selected' : '' }}>Terisi (Occupied)</option>
                    <option value="reserved" {{ old('status') === 'reserved' ? 'selected' : '' }}>Reservasi (Reserved)</option>
                </select>
                @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Meja</label>
                <div class="mt-2.5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                        <span class="ms-2 text-sm text-gray-700 font-medium">Aktif untuk pemesanan QR</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.tables.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
                <i class="fa-solid fa-save mr-1.5"></i> Simpan Meja
            </button>
        </div>
    </form>
</div>
@endsection
