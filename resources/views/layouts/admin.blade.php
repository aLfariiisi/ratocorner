<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ratocorner ERP - @yield('title', 'Dashboard')</title>

    <!-- Favicon Dinamis dari CafeSetting -->
    @php
        try {
            $cafeSetting = \App\Models\CafeSetting::first();
        } catch (\Throwable $e) {
            $cafeSetting = null;
        }
    @endphp

    @if($cafeSetting && $cafeSetting->logo_path)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $cafeSetting->logo_path) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('images/rato-corner-logo.jpeg') }}">
    @endif

    <!-- Google Fonts: Outfit & Yesteryear -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Yesteryear&display=swap"
        rel="stylesheet">
    <!-- Tailwind CSS CDN untuk utility UI yang cepat & rapi ala Dashboard Modern -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#c1753d',
                        secondary: '#f7ebe5',
                        darkcustom: '#222222',
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        yesteryear: ['Yesteryear', 'cursive'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f7ebe5;
            color: #222222;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-orange-100 flex flex-col z-20 shadow-sm">
        <div class="h-16 flex items-center px-6 border-b border-orange-100 justify-between">
            <span
                class="text-2xl font-bold font-yesteryear text-primary">{{ $cafeSetting->name ?? 'Ratocorner' }}</span>
            <span
                class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-orange-100 text-primary">ERP</span>
        </div>
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1 text-sm font-medium">
            <!-- Dashboard (Semua Staf) -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
            </a>

            <!-- Meja QR (Semua Staf: Kasir, Manager, Owner) -->
            <a href="{{ route('admin.tables.index') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.tables*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                <i class="fa-solid fa-qrcode w-5"></i> Meja QR
            </a>

            <!-- Pesanan ERP (Semua Staf) -->
            <a href="{{ Route::has('admin.orders.index') ? route('admin.orders.index') : '#' }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.orders*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                <i class="fa-solid fa-cash-register w-5"></i> Pesanan (POS)
            </a>

            <!-- Produk & Kategori (Owner & Manager) -->
            @if(auth()->user() && (auth()->user()->isOwner() || auth()->user()->isManager()))
                <a href="{{ Route::has('admin.products.index') ? route('admin.products.index') : '#' }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.products*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-mug-hot w-5"></i> Menu Produk
                </a>
                <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.categories*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-tags w-5"></i> Kategori
                </a>
                <!-- Manajemen Stok & Bahan Baku (Owner & Manager) -->
                <a href="{{ Route::has('admin.stocks.index') ? route('admin.stocks.index') : '#' }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.stocks*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-boxes-stacked w-5"></i> Stok Bahan Baku
                </a>
            @endif

            <!-- Pembayaran & Kasir (Semua Staf) -->
            <a href="{{ Route::has('admin.payments.index') ? route('admin.payments.index') : '#' }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.payments*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                <i class="fa-solid fa-wallet w-5"></i> Pembayaran
            </a>

            <!-- Pelanggan CRM (Semua Staf) -->
            <a href="{{ Route::has('admin.customers.index') ? route('admin.customers.index') : '#' }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.customers*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                <i class="fa-solid fa-users w-5"></i> Pelanggan
            </a>

            <!-- LAPORAN: KHUSUS OWNER & MANAGER (Kasir DILARANG KERAS) -->
            @can('access-reports')
                <div class="pt-3 pb-1">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Laporan & Keuangan</p>
                </div>
                <a href="{{ Route::has('admin.reports.analytics') ? route('admin.reports.analytics') : '#' }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.reports.analytics') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-chart-line w-5"></i> Analitik Penjualan
                </a>
                <a href="{{ Route::has('admin.reports.financials') ? route('admin.reports.financials') : '#' }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.reports.financials') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-file-invoice-dollar w-5"></i> Keuangan (Laba/Rugi)
                </a>
                <a href="{{ Route::has('admin.reports.hpp') ? route('admin.reports.hpp') : '#' }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.reports.hpp') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-calculator w-5"></i> Analisis Resep & HPP
                </a>
            @endcan

            <!-- PENGATURAN: KHUSUS OWNER & MANAGER (Kasir DILARANG KERAS) -->
            @can('access-settings')
                <div class="pt-3 pb-1">
                    <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-gray-400">Konfigurasi</p>
                </div>
                <a href="{{ route('admin.settings.edit') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.settings*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-gear w-5"></i> Pengaturan Cafe
                </a>
            @endcan

            @if(auth()->user()?->isOwner())
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-orange-50 text-primary font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-primary' }} transition">
                    <i class="fa-solid fa-user-shield w-5"></i> Manajemen Staf
                </a>
            @endif
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-orange-100 flex items-center justify-between px-8 z-10">
            <div class="flex items-center w-96 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                <i class="fa-solid fa-search text-gray-400 mr-2"></i>
                <input type="text" placeholder="Cari pesanan, meja, atau menu..."
                    class="bg-transparent border-none focus:outline-none text-sm w-full">
            </div>

            <div class="flex items-center gap-5">
                <!-- User Profile & Role Badge -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                        {{ substr(auth()->user()->name ?? 'S', 0, 1) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <span
                            class="block text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()->name ?? 'Staf' }}</span>
                        @php
                            $roleLabel = auth()->user()?->role instanceof \App\Enums\UserRole ? auth()->user()->role->label() : ucfirst(auth()->user()?->role ?? 'Kasir');
                            $roleBg = match (strtolower($roleLabel)) {
                                'owner' => 'bg-purple-100 text-purple-700',
                                'manager' => 'bg-blue-100 text-blue-700',
                                default => 'bg-emerald-100 text-emerald-700',
                            };
                        @endphp
                        <span
                            class="inline-block text-[10px] font-bold px-2 py-0.5 rounded {{ $roleBg }} uppercase tracking-wider">
                            {{ $roleLabel }}
                        </span>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Logout"
                        class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden md:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content Section -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-4 bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm"
                    role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>

</html>