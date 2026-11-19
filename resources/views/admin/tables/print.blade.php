<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Meja {{ $table->table_number }} - {{ $cafeSetting->name ?? 'Ratocorner' }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Yesteryear&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f3f4f6; }
        @media print {
            body { background: transparent; padding: 0; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 2px solid #e5e7eb !important; page-break-inside: avoid; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">

    <!-- Tombol Aksi Layar (Disembunyikan saat cetak) -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-[#c1753d] text-white rounded-xl text-sm font-semibold shadow hover:bg-[#a66231] transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Stand Meja Ini
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-300 transition">
            Tutup
        </button>
    </div>

    @php
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=" . urlencode($orderUrl);
    @endphp

    <!-- Stand Meja Kartu (Acrylic Stand Format) -->
    <div class="print-card w-[380px] bg-white rounded-3xl p-8 border-4 border-[#c1753d] shadow-xl text-center flex flex-col items-center">
        <!-- Logo / Cafe Header -->
        <div class="mb-3">
            @if($cafeSetting->logo_path)
                <img src="{{ asset('storage/' . $cafeSetting->logo_path) }}" alt="{{ $cafeSetting->name }}" class="h-12 object-contain mx-auto mb-1">
            @endif
            <h1 class="text-3xl font-bold font-serif italic text-[#c1753d] tracking-wide">{{ $cafeSetting->name ?? 'Ratocorner' }}</h1>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-widest">{{ $cafeSetting->tagline ?? 'Specialty Coffee & Eatery' }}</p>
        </div>

        <div class="w-full border-t border-dashed border-gray-300 my-2"></div>

        <!-- Nomor Meja Besar -->
        <div class="my-3">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-400 block mb-1">Nomor Meja</span>
            <div class="inline-block bg-[#f7ebe5] text-[#c1753d] text-4xl font-extrabold px-6 py-2 rounded-2xl border-2 border-[#c1753d]">
                {{ $table->table_number }}
            </div>
            @if($table->location)
                <p class="text-xs text-gray-500 mt-1 font-medium">Area {{ $table->location }}</p>
            @endif
        </div>

        <!-- QR Code -->
        <div class="bg-white p-4 border-2 border-gray-200 rounded-2xl shadow-inner my-3">
            <img src="{{ $qrUrl }}" alt="Scan QR Meja {{ $table->table_number }}" class="w-56 h-56 object-contain">
        </div>

        <!-- Instruksi Scan -->
        <div class="mb-4">
            <p class="text-base font-extrabold text-gray-800 flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-mobile-screen-button text-[#c1753d]"></i> SCAN UNTUK PESAN
            </p>
            <p class="text-[11px] text-gray-500 mt-0.5">Buka kamera ponsel Anda dan arahkan ke kode QR di atas untuk melihat menu & memesan langsung.</p>
        </div>

        <!-- Info Wi-Fi -->
        @if($cafeSetting->wifi_name)
            <div class="w-full bg-gray-50 p-2.5 rounded-xl border border-gray-200 text-xs text-gray-600 flex items-center justify-around">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-wifi text-[#c1753d]"></i>
                    <span class="font-bold">{{ $cafeSetting->wifi_name }}</span>
                </div>
                @if($cafeSetting->wifi_password)
                    <div class="flex items-center gap-1">
                        <span class="text-gray-400">Pass:</span>
                        <span class="font-mono font-semibold">{{ $cafeSetting->wifi_password }}</span>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-4 text-[10px] text-gray-400">
            Powered by Ratocorner ERP Smart Order
        </div>
    </div>

</body>
</html>
