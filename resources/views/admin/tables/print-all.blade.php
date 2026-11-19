<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua Meja QR - {{ $cafeSetting->name ?? 'Ratocorner' }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            .print-page { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">

    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Cetak Semua Meja QR (Total: {{ $tables->count() }} Meja)</h1>
            <p class="text-xs text-gray-500">Gunakan pengaturan print browser: Layout Portrait/Landscape, Margins Default/None.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 bg-[#c1753d] text-white rounded-lg text-sm font-semibold shadow hover:bg-[#a66231] transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Sekarang
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Grid Kartu Meja Siap Potong -->
    <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">
        @foreach($tables as $table)
            @php
                $orderUrl = route('order.table', $table->qr_code_token);
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=" . urlencode($orderUrl);
            @endphp

            <div class="bg-white rounded-3xl p-6 border-4 border-[#c1753d] shadow-md text-center flex flex-col items-center justify-between page-break-inside-avoid">
                <div class="w-full">
                    <h2 class="text-2xl font-bold font-serif italic text-[#c1753d]">{{ $cafeSetting->name ?? 'Ratocorner' }}</h2>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $cafeSetting->tagline ?? 'Specialty Coffee & Eatery' }}</p>
                    <div class="border-t border-dashed border-gray-300 my-2"></div>
                </div>

                <div class="my-1">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block">Nomor Meja</span>
                    <div class="inline-block bg-[#f7ebe5] text-[#c1753d] text-3xl font-extrabold px-5 py-1 rounded-xl border-2 border-[#c1753d]">
                        {{ $table->table_number }}
                    </div>
                    @if($table->location)
                        <p class="text-[11px] text-gray-500 mt-0.5">Area {{ $table->location }}</p>
                    @endif
                </div>

                <div class="bg-white p-3 border-2 border-gray-200 rounded-2xl shadow-inner my-2">
                    <img src="{{ $qrUrl }}" alt="QR Meja {{ $table->table_number }}" class="w-44 h-44 object-contain">
                </div>

                <div class="mb-2">
                    <p class="text-sm font-extrabold text-gray-800 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-mobile-screen-button text-[#c1753d]"></i> SCAN UNTUK PESAN
                    </p>
                    <p class="text-[10px] text-gray-500">Arahkan kamera ponsel Anda ke QR di atas</p>
                </div>

                @if($cafeSetting->wifi_name)
                    <div class="w-full bg-gray-50 p-2 rounded-lg border border-gray-200 text-[11px] text-gray-600 flex items-center justify-around">
                        <div><i class="fa-solid fa-wifi text-[#c1753d] mr-1"></i> <strong>{{ $cafeSetting->wifi_name }}</strong></div>
                        @if($cafeSetting->wifi_password)
                            <div>Pass: <span class="font-mono font-semibold">{{ $cafeSetting->wifi_password }}</span></div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

</body>
</html>
