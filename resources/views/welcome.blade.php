@extends('layouts.frontend')
@section('title', 'Beranda')

@section('content')
    <section class="relative bg-cover bg-center h-[500px] flex items-center justify-center text-center" style="background-image: url('{{ asset('images/hero-coffee.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>

        <div class="relative z-10 text-white max-w-2xl px-4">
            <p class="text-yellow-500 tracking-widest text-sm font-bold uppercase mb-2">Best Coffee Shop</p>
            <h1 class="text-5xl font-serif font-bold italic mb-6">Coffee from the Best Sunny Plantations</h1>
            <a href="{{ route('products.index') }}" class="bg-[#c28455] hover:bg-[#a66a40] text-white px-8 py-3 rounded-full font-semibold transition">Lihat Semua Menu</a>
        </div>
    </section>

    <section class="py-16 px-8 max-w-6xl mx-auto text-center">
        <h2 class="text-4xl font-serif font-bold italic text-gray-800 mb-2">Best Seller Product This Week!</h2>
        <p class="text-gray-500 mb-12 max-w-xl mx-auto">Nikmati pilihan kopi terbaik kami yang paling banyak diminati minggu ini, diseduh dari biji kopi pilihan kualitas premium.</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @forelse ($products as $product)
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition text-left group">
                <a href="{{ route('products.show', $product->id) }}" class="block overflow-hidden h-48 mb-4 bg-gray-50 rounded flex items-center justify-center">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <img src="{{ asset('images/coffee-cup-1.png') }}" alt="{{ $product->name }}" class="h-full object-contain group-hover:scale-105 transition duration-300">
                    @endif
                </a>
                <div class="text-yellow-400 text-xs mb-1">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <a href="{{ route('products.show', $product->id) }}">
                    <h3 class="font-bold text-lg text-gray-800 hover:text-[#c28455] transition truncate">{{ $product->name }}</h3>
                </a>
                <div class="flex justify-between items-center mt-4">
                    <p class="font-bold text-lg text-[#c28455]">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('products.show', $product->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-2 rounded-full font-semibold transition">Detail</a>
                        <button type="button"
                                onclick="addToCartHome({{ $product->id }})"
                                class="bg-[#1a1311] hover:bg-[#c28455] text-white h-9 w-9 rounded-full flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-[#c28455] focus:ring-offset-2"
                                title="Tambah ke Keranjang">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                <p>Belum ada produk unggulan yang ditampilkan.</p>
            </div>
            @endforelse
        </div>
    </section>

    <script src="https://jsdelivr.net"></script>
    <script>
        function addToCartHome(productId) {
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('header-cart-count');
                    const totalEl = document.getElementById('header-cart-total');
                    if (badge) {
                        badge.textContent = data.cart_count;
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                    }
                    if (totalEl) {
                        totalEl.textContent = 'Rp ' + data.cart_total;
                    }

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Oops!', 'Terjadi kesalahan sistem.', 'error');
            });
        }
    </script>
@endsection
