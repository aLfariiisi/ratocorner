<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Meja {{ $table->table_number }} - {{ $cafeSetting->name ?? 'Ratocorner' }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Yesteryear&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://jsdelivr.net"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #faf5f2;
            color: #222222;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen pb-24" x-data="tableOrderApp()">

    <!-- Top Header Bar -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-orange-100 px-4 py-3 shadow-sm">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 rounded-full bg-[#c1753d] text-white flex items-center justify-center font-serif font-bold text-sm">
                    R
                </div>
                <div>
                    <h1 class="font-serif italic font-bold text-lg text-[#c1753d] leading-none">
                        {{ $cafeSetting->name ?? 'Ratocorner' }}
                    </h1>
                    <span
                        class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $cafeSetting->tagline ?? 'Specialty Coffee' }}</span>
                </div>
            </div>
            <div
                class="bg-[#f7ebe5] border border-[#c1753d]/30 text-[#c1753d] px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-chair text-[11px]"></i>
                <span>Meja {{ $table->table_number }}</span>
            </div>
        </div>
    </header>

    <!-- Main Container (Mobile First) -->
    <main class="max-w-md mx-auto px-4 pt-4">

        <!-- Notifikasi Pesanan Berhasil -->
        @if(session('order_success'))
            <div class="mb-5 bg-emerald-600 text-white p-4 rounded-2xl shadow-md space-y-1">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span>Pesanan Berhasil Dikirim!</span>
                </div>
                <p class="text-xs text-emerald-100">Nomor Pesanan: <strong
                        class="font-mono text-white">{{ session('order_success') }}</strong>. Silakan lakukan pembayaran
                    atau tunggu barista kami menyiapkan pesanan ke Meja {{ $table->table_number }}.</p>
            </div>
        @endif

        <!-- Welcome Banner & Info Meja -->
        <div class="bg-gradient-to-br from-[#c1753d] to-[#8f4f22] text-white p-5 rounded-2xl shadow-md mb-5">
            <p class="text-xs uppercase tracking-wider text-orange-200 font-semibold mb-1">Selamat Datang di
                {{ $cafeSetting->name ?? 'Ratocorner' }}
            </p>
            <h2 class="text-xl font-bold mb-2">Anda memesan di Meja {{ $table->table_number }}</h2>
            <p class="text-xs text-orange-100 leading-relaxed mb-3">Klik tombol <strong>+</strong> pada menu favorit
                Anda untuk memasukkan ke keranjang tanpa perlu login.</p>

            @if($cafeSetting->wifi_name)
                <div class="bg-black/20 rounded-xl p-2.5 flex items-center justify-between text-xs text-orange-100">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-wifi text-yellow-300"></i>
                        <span class="font-semibold">{{ $cafeSetting->wifi_name }}</span>
                    </div>
                    @if($cafeSetting->wifi_password)
                        <div>
                            <span class="text-orange-200">Pass:</span>
                            <span class="font-mono font-bold text-white">{{ $cafeSetting->wifi_password }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Menu Title -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Daftar Menu</h3>
            <span class="text-xs text-gray-500">{{ $products->count() }} Pilihan</span>
        </div>

        <!-- Menu List dengan Tombol "+" di Setiap Kartu Produk -->
        <div class="space-y-4">
            @forelse($products as $product)
                <div
                    class="bg-white rounded-2xl p-4 shadow-sm border border-orange-100 flex gap-3.5 items-center hover:border-orange-200 transition">
                    <div
                        class="w-20 h-20 bg-orange-50 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center border border-orange-100">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-mug-hot text-2xl text-[#c1753d]/60"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider text-[#c1753d]">{{ $product->category->name ?? 'Minuman' }}</span>
                        <h4 class="font-bold text-sm text-gray-800 truncate">{{ $product->name }}</h4>
                        <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5">
                            {{ $product->description ?? 'Diseduh segar dengan bahan premium.' }}
                        </p>
                        <div class="flex items-center justify-between mt-2.5">
                            <span class="font-extrabold text-[#c1753d] text-sm">Rp
                                {{ number_format($product->price, 0, ',', '.') }}</span>

                            <!-- Tombol "+" & Kontrol Qty pada Kontainer Produk -->
                            <div class="flex items-center gap-2">
                                <template x-if="getQty({{ $product->id }}) > 0">
                                    <div
                                        class="flex items-center gap-2 bg-[#f7ebe5] px-2 py-1 rounded-xl border border-[#c1753d]/30">
                                        <button type="button" @click="decrement({{ $product->id }})"
                                            class="w-6 h-6 rounded-lg bg-white text-[#c1753d] font-bold text-xs flex items-center justify-center shadow-sm">-</button>
                                        <span class="text-xs font-bold text-gray-800 min-w-[14px] text-center"
                                            x-text="getQty({{ $product->id }})"></span>
                                        <button type="button"
                                            @click="addItem({{ $product->id }}, @js($product->name), {{ (float) $product->price }})"
                                            class="w-6 h-6 rounded-lg bg-[#c1753d] text-white font-bold text-xs flex items-center justify-center shadow-sm">+</button>
                                    </div>
                                </template>
                                <template x-if="getQty({{ $product->id }}) === 0">
                                    <button type="button"
                                        @click="addItem({{ $product->id }}, @js($product->name), {{ (float) $product->price }})"
                                        class="w-8 h-8 rounded-xl bg-[#c1753d] text-white font-bold text-base flex items-center justify-center hover:bg-[#a66231] shadow-sm transition"
                                        title="Tambah ke Keranjang">
                                        +
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border text-center text-gray-400 text-sm">
                    <i class="fa-solid fa-mug-saucer text-3xl mb-2 text-gray-300"></i>
                    <p>Menu sedang disiapkan oleh barista kami.</p>
                </div>
            @endforelse
        </div>
    </main>

    <!-- Fixed Bottom Bar (Keranjang & Checkout) -->
    <div class="fixed bottom-0 inset-x-0 bg-white border-t border-orange-100 p-3 z-30 shadow-lg">
        <div class="max-w-md mx-auto flex items-center justify-between gap-3">
            <template x-if="totalItems() > 0">
                <button type="button" @click="openCart = true"
                    class="w-full py-3 px-4 bg-[#c1753d] text-white rounded-xl font-bold text-sm flex items-center justify-between hover:bg-[#a66231] shadow-md transition">
                    <div class="flex items-center gap-2">
                        <span class="bg-white text-[#c1753d] px-2 py-0.5 rounded-lg text-xs font-extrabold"
                            x-text="totalItems() + ' Item'"></span>
                        <span>Lihat Keranjang & Pesan</span>
                    </div>
                    <span x-text="'Rp ' + totalPrice().toLocaleString('id-ID')"></span>
                </button>
            </template>

            <template x-if="totalItems() === 0">
                <div class="w-full flex items-center justify-between text-xs text-gray-600">
                    <div>
                        <span class="text-gray-400 block text-[10px]">LOKASI MEJA</span>
                        <span class="font-bold text-gray-800">Meja {{ $table->table_number }}
                            ({{ $table->location ?? 'Indoor' }})</span>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cafeSetting->phone ?? '') }}?text=Halo%20Ratocorner,%20saya%20di%20Meja%20{{ $table->table_number }}"
                        target="_blank"
                        class="px-4 py-2 bg-[#f7ebe5] text-[#c1753d] border border-[#c1753d]/30 rounded-xl font-semibold flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Bantuan</span>
                    </a>
                </div>
            </template>
        </div>
    </div>