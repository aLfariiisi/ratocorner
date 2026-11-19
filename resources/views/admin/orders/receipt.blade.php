<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->order_number }} - {{ $cafeSetting->name ?? 'Ratocorner' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f3f4f6;
            color: #111827;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .receipt-box {
            width: 300px;
            background: #ffffff;
            padding: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            font-size: 12px;
            line-height: 1.45;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .logo {
            max-width: 64px;
            max-height: 64px;
            margin: 0 auto 6px;
            display: block;
            border-radius: 50%;
        }

        .brand-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .divider {
            border-top: 1px dashed #374151;
            margin: 10px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .item-block {
            margin-bottom: 6px;
        }

        .wifi-box {
            border: 1px dashed #6b7280;
            padding: 6px;
            margin-top: 8px;
            text-align: center;
            font-size: 11px;
        }

        .no-print {
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-family: sans-serif;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-print {
            background: #c1753d;
            color: #fff;
        }

        .btn-back {
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .receipt-box {
                width: 100%;
                max-width: 80mm;
                box-shadow: none;
                padding: 4px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn btn-print">Cetak Struk Sekarang</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-back">Kembali</a>
    </div>

    <div class="receipt-box">
        <div class="center">
            @if(!empty($cafeSetting->logo_path))
                <img src="{{ asset('storage/' . $cafeSetting->logo_path) }}" alt="Logo" class="logo">
            @endif
            <div class="brand-title">{{ $cafeSetting->name ?? 'RATOCORNER' }}</div>
            @if(!empty($cafeSetting->tagline))
                <div style="font-size: 10px;">{{ $cafeSetting->tagline }}</div>
            @endif
            <div style="font-size: 11px; margin-top: 4px;">
                {{ $cafeSetting->address ?? 'Jl. Senopati No. 45, Jakarta Selatan' }}
            </div>
            @if(!empty($cafeSetting->phone))
                <div style="font-size: 11px;">Telp/WA: {{ $cafeSetting->phone }}</div>
            @endif
            @if(!empty($cafeSetting->receipt_header))
                <div style="font-size: 10px; margin-top: 4px;">{{ $cafeSetting->receipt_header }}</div>
            @endif
        </div>

        <div class="divider"></div>

        <div class="row">
            <span>No. Order</span>
            <span class="bold">{{ $order->order_number }}</span>
        </div>
        <div class="row">
            <span>Waktu</span>
            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span>Meja</span>
            <span class="bold">Meja {{ $order->table->table_number ?? '-' }}</span>
        </div>
        <div class="row">
            <span>Pelanggan</span>
            <span>{{ $order->customer_name ?? 'Guest' }}</span>
        </div>
        <div class="row">
            <span>Kasir</span>
            <span>{{ $order->cashier->name ?? auth()->user()->name ?? 'Staff' }}</span>
        </div>

        <div class="divider"></div>

        @foreach($order->items as $item)
            <div class="item-block">
                <div class="bold">{{ $item->product_name }}</div>
                <div class="row">
                    <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach

        <div class="divider"></div>

        <div class="row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($order->tax_amount > 0)
            <div class="row">
                <span>PPN ({{ number_format($cafeSetting->tax_percentage ?? 0, 0) }}%)</span>
                <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($order->service_charge > 0)
            <div class="row">
                <span>Service ({{ number_format($cafeSetting->service_charge_percentage ?? 0, 0) }}%)</span>
                <span>Rp {{ number_format($order->service_charge, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="divider"></div>

        <div class="row bold" style="font-size: 14px;">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="row" style="margin-top: 4px;">
            <span>Pembayaran</span>
            <span class="bold" style="text-transform: uppercase;">{{ $order->payment_method }}
                ({{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }})</span>
        </div>

        @if(!empty($cafeSetting->wifi_name))
            <div class="wifi-box">
                <div class="bold">FREE WI-FI ACCESS</div>
                <div>SSID: {{ $cafeSetting->wifi_name }}</div>
                @if(!empty($cafeSetting->wifi_password))
                    <div>Pass: <span class="bold">{{ $cafeSetting->wifi_password }}</span></div>
                @endif
            </div>
        @endif

        <div class="divider"></div>

        <div class="center" style="font-size: 11px;">
            {{ $cafeSetting->receipt_footer ?? 'Terima kasih telah berkunjung ke Ratocorner!' }}
        </div>
    </div>

</body>

</html>