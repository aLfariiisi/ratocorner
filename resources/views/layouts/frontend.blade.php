<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') | {{ $setting->name ?? 'Ratocorner' }}</title>

    @if(isset($setting) && $setting->logo_path)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $setting->logo_path) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('images/rato-corner-logo.jpeg') }}">
    @endif

    <link href="https://googleapis.com" rel="stylesheet">
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
    <script defer src="https://jsdelivr.net"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #fdfaf5; color: #222222; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="font-outfit antialiased bg-[#fdfaf5] text-gray-800">

    <header class="bg-[#1a1311] text-white py-4 px-8 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if(isset($setting) && $setting->logo_path)
                    <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->name ?? 'Ratocorner' }}" class="h-12 bg-white p-1 rounded object-contain">
                @else
                    <img src="{{ asset('images/rato-corner-logo.jpeg') }}" alt="Ratocorner" class="h-12 bg-white p-1 rounded">
                @endif
            </a>
            <div class="hidden md:block">
                <p class="text-sm text-yellow-500">Order & Reservasi</p>
                <p class="font-bold">{{ $setting->phone ?? '+62 800-1234-435' }}</p>
            </div>
        </div>

        <nav class="hidden md:flex gap-6 text-sm font-semibold">
            <a href="{{ route('home') }}" class="hover:text-yellow-500 transition">Home</a>
            <a href="{{ route('about') }}" class="hover:text-yellow-500 transition">About Us</a>
            <a href="{{ route('products.index') }}" class="hover:text-yellow-500 transition">Product</a>
            <a href="{{ route('contact') }}" class="hover:text-yellow-500 transition">Contact Us</a>
        </nav>

        <div class="flex items-center gap-4">
            @php
                $cart = session()->get('cart', []);
                $cartTotalCount = 0;
                $cartTotalPrice = 0;

                if (is_array($cart)) {
                    foreach($cart as $item) {
                        if (is_array($item) && isset($item['price']) && isset($item['quantity'])) {
                            $cartTotalCount += (int) $item['quantity'];
                            $cartTotalPrice += (float) $item['price'] * (int) $item['quantity'];
                        }
                    }
                }
            @endphp

            <a href="{{ route('cart.index') }}" class="flex items-center gap-2 hover:text-yellow-500 relative">
                <div class="relative">
                    <i class="fas fa-shopping-basket text-lg"></i>
                    <span id="header-cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-4 h-4 {{ $cartTotalCount > 0 ? 'flex' : 'hidden' }} items-center justify-center rounded-full font-bold">
                        {{ $cartTotalCount }}
                    </span>
                </div>
                <span id="header-cart-total" class="text-sm font-semibold">Rp {{ number_format($cartTotalPrice, 0, ',', '.') }}</span>
            </a>


            @auth
                @if(auth()->user()->isOwner() || auth()->user()->isManager() || auth()->user()->isKasir())
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#c28455] text-white px-4 py-1.5 rounded text-sm font-bold hover:bg-[#a66a40] transition">
                        <i class="fas fa-solar-panel mr-1"></i> Panel ERP
                    </a>
                @endif
            @endauth
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-[#1a1311] text-gray-400 py-10 text-center text-sm">
        <p>&copy; {{ date('Y') }} {{ $setting->name ?? 'Ratocorner' }}. All Rights Reserved.</p>
        <div class="flex justify-center gap-4 mt-4">
            <a href="{{ route('faq') }}" class="hover:text-white">FAQ</a>
            <a href="{{ route('terms') }}" class="hover:text-white">Syarat & Ketentuan</a>
            <a href="{{ route('privacy') }}" class="hover:text-white">Kebijakan Privasi</a>
        </div>
    </footer>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
