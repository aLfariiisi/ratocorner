@extends('layouts.admin')

@section('title', 'Edit Meja ' . $table->table_number)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tables.index') }}" class="inline-flex items-center gap-1.5 text-xs text-primary hover:underline font-semibold mb-2">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Meja
    </a>
    <h1 class="text-2xl font-bold text-gray-800">Edit Meja {{ $table->table_number }}</h1>
    <p class="text-sm text-gray-500">Perbarui data kapasitas, status ketersediaan, atau lokasi meja.</p>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow-sm border border-orange-100 p-6">
    <form action="{{ route('admin.tables.update', $table) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja <span class="text-red-500">*</span></label>
                <input type="text" name="table_number" value="{{ old('table_number', $table->table_number) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('table_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama / Label Meja</label>
                <input type="text" name="name" value="{{ old('name', $table->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="50" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('capacity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Area / Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $table->location) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('location') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Ketersediaan</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    <option value="available" {{ old('status', $table->status) === 'available' ? 'selected' : '' }}>Tersedia (Available)</option>
                    <option value="occupied" {{ old('status', $table->status) === 'occupied' ? 'selected' : '' }}>Terisi (Occupied)</option>
                    <option value="reserved" {{ old('status', $table->status) === 'reserved' ? 'selected' : '' }}>Reservasi (Reserved)</option>
                </select>
                @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Meja</label>
                <div class="mt-2.5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $table->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
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
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Perbarui Meja
            </button>
        </div>
    </form>
</div>
@endsection
