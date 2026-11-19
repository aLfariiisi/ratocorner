@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-3xl bg-white rounded-xl shadow-sm border border-orange-100 p-6">
    <h1 class="text-xl font-bold text-gray-800 mb-4">Form Tambah Produk & Resep</h1>
    
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
            <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c28455]">
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c28455]">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Kode Unik)</label>
                <input type="text" name="sku" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c28455]">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c28455] bg-white">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp)</label>
                <input type="number" name="price" min="0" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c28455]">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#c28455]"></textarea>
        </div>

        <!-- BAGIAN RESEP BAHAN BAKU (BOM) -->
        <div class="mb-6 bg-orange-50/60 p-4 rounded-xl border border-orange-200">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <h3 class="font-bold text-gray-800 text-sm">Resep & Komposisi Bahan Baku</h3>
                    <p class="text-xs text-gray-500">Tentukan bahan mentah dan takaran per 1 porsi menu untuk kalkulasi HPP & pengurangan stok otomatis.</p>
                </div>
                <button type="button" onclick="addIngredientRow()" class="bg-[#c28455] hover:bg-[#a66a40] text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah Bahan
                </button>
            </div>

            <div id="ingredients-container" class="space-y-2">
                <!-- Baris dinamis akan masuk ke sini -->
            </div>
        </div>
        
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</a>
            <button type="submit" class="px-4 py-2 bg-[#c28455] text-white rounded-lg text-sm font-medium hover:bg-[#a66a40]">Simpan Produk</button>
        </div>
    </form>
</div>

<!-- Template Baris Bahan Baku -->
<template id="ingredient-row-template">
    <div class="flex items-center gap-2 ingredient-row">
        <select name="ingredients[]" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-full bg-white focus:outline-none focus:border-[#c28455]">
            <option value="">-- Pilih Bahan Baku --</option>
            @foreach($ingredients as $ing)
                <option value="{{ $ing->id }}">{{ $ing->name }} (Satuan: {{ $ing->unit }})</option>
            @endforeach
        </select>
        <input type="number" name="quantities[]" step="any" min="0.01" required placeholder="Takaran (Misal: 150)" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-44 focus:outline-none focus:border-[#c28455]">
        <button type="button" onclick="this.closest('.ingredient-row').remove()" class="bg-red-500 text-white px-3.5 py-2 rounded-lg text-sm hover:bg-red-600 transition">
            <i class="fa-solid fa-trash"></i>
        </button>
    </div>
</template>

<script>
function addIngredientRow() {
    const template = document.getElementById('ingredient-row-template');
    const clone = template.content.cloneNode(true);
    document.getElementById('ingredients-container').appendChild(clone);
}

// Otomatis munculkan 1 baris kosong saat halaman dibuka
document.addEventListener("DOMContentLoaded", function() {
    addIngredientRow();
});
</script>
@endsection