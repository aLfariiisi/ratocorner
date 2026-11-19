@extends('layouts.admin')

@section('title', 'Pengaturan Cafe')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Pengaturan Cafe</h1>
    <p class="text-sm text-gray-500">Ubah identitas, logo, kontak, pajak, Wi-Fi, dan konfigurasi struk kasir Ratocorner.</p>
</div>

<div class="max-w-3xl bg-white rounded-xl shadow-sm border border-orange-100 p-6">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Cafe</label>
                <input type="text" name="name" value="{{ old('name', $setting->name) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tagline Cafe</label>
                <input type="text" name="tagline" value="{{ old('tagline', $setting->tagline) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('tagline') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Kontak</label>
                <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Cafe</label>
            <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">{{ old('address', $setting->address) }}</textarea>
            @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wi-Fi (SSID)</label>
                <input type="text" name="wifi_name" value="{{ old('wifi_name', $setting->wifi_name) }}" placeholder="Contoh: Ratocorner-Guest" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('wifi_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Wi-Fi</label>
                <input type="text" name="wifi_password" value="{{ old('wifi_password', $setting->wifi_password) }}" placeholder="Contoh: kopienak" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                @error('wifi_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pajak (PPN %)</label>
                <div class="relative">
                    <input type="number" step="0.01" name="tax_percentage" value="{{ old('tax_percentage', $setting->tax_percentage) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary pr-8">
                    <span class="absolute right-3 top-2 text-sm text-gray-400 font-bold">%</span>
                </div>
                @error('tax_percentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Charge (%)</label>
                <div class="relative">
                    <input type="number" step="0.01" name="service_charge_percentage" value="{{ old('service_charge_percentage', $setting->service_charge_percentage) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary pr-8">
                    <span class="absolute right-3 top-2 text-sm text-gray-400 font-bold">%</span>
                </div>
                @error('service_charge_percentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Cafe</label>
            @if($setting->logo_path)
                <div class="mb-2 p-2 bg-orange-50/50 rounded-lg border border-orange-100 inline-block">
                    <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->name }}" class="h-14 object-contain">
                </div>
            @endif
            <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-primary hover:file:bg-orange-100">
            @error('logo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Header Struk Cetak</label>
                <textarea name="receipt_header" rows="3" placeholder="Teks bagian atas struk kasir..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary font-mono text-xs">{{ old('receipt_header', $setting->receipt_header) }}</textarea>
                @error('receipt_header') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Footer Struk Cetak</label>
                <textarea name="receipt_footer" rows="3" placeholder="Pesan terima kasih / info Wi-Fi..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary font-mono text-xs">{{ old('receipt_footer', $setting->receipt_footer) }}</textarea>
                @error('receipt_footer') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-opacity-90 transition">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
