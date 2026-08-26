<!DOCTYPE html>
<html>
<head>
    <title>Nota Transaksi #{{ $transaksi->id }}</title>
    <style>
        /* Reset dan gaya dasar */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .header { margin-bottom: 5px; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .divider-double { border-top: 2px solid #000; margin: 8px 0; }
        .item-line { display: flex; justify-content: space-between; }
        .item-name { font-weight: normal; }
        .total { font-weight: bold; font-size: 1.2em; }
        .footer { text-align: center; margin-top: 15px; }
        .no-print { display: block; text-align: center; margin-top: 20px; }
        /* Sembunyikan tombol saat print */
        @media print {
            body { width: 100%; margin: 0; padding: 3mm; }
            .no-print { display: none !important; }
        }
        .logo-img { max-width: 60mm; height: auto; margin-bottom: 5px; }
        .rp { font-weight: normal; }
    </style>
</head>
<body>
    <div class="header text-center">
        {{-- Logo --}}
        @php
            $logoPath = public_path('assets/dist/img/logo_print.png');
            $logoExists = file_exists($logoPath);
        @endphp
        @if($logoExists)
            <img src="{{ asset('assets/dist/img/logo_print.png') }}" alt="Logo" class="logo-img">
        @else
            <h3 style="margin:0; font-size:1.5em;">ANGEL CELL</h3>
        @endif
        <p style="margin:2px 0;">Jalan Jangga-Terisi Desa Jangga</p>
        <p style="margin:2px 0;">{{ now()->format('d M Y H:i:s') }}</p>
        <div class="divider"></div>
    </div>
    <div>
        <p style="margin:2px 0;"><b>Kasir</b>     : {{ auth()->user()->nama }}</p>
        <p style="margin:2px 0;"><b>Pelanggan</b> : {{ $nama_member }}</p>
    </div>
    <div class="divider"></div>

    {{-- Detail item --}}
    @foreach($details as $d)
        <div class="item-name">{{ $d->nama_barang }}</div>
        <div class="item-line">
            <span>{{ $d->qty }} x {{ number_format($d->harga_jual, 0, '.', '.') }}</span>
            <span>{{ number_format($d->harga_jual * $d->qty, 0, '.', '.') }}</span>
        </div>
    @endforeach

    <div class="divider"></div>
    <div class="text-right">
        <p style="margin:2px 0; font-size:1.2em; font-weight:bold;">TOTAL: Rp {{ number_format($transaksi->total_belanja, 0, '.', '.') }}</p>
        <p style="margin:2px 0;">BAYAR: Rp {{ number_format($transaksi->bayar, 0, '.', '.') }}</p>
        <p style="margin:2px 0;">KEMBALI: Rp {{ number_format($transaksi->kembalian, 0, '.', '.') }}</p>
    </div>
    <div class="divider"></div>
    <div class="footer">
        <p style="margin:2px 0; font-weight:bold;">TERIMA KASIH</p>
        <p style="margin:2px 0;">Atas Kunjungan Anda</p>
        <p style="margin:2px 0;">Barang yang sudah dibeli</p>
        <p style="margin:2px 0;">tidak dapat ditukar/dikembalikan</p>
    </div>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Cetak Nota</button>
        <br><br>
        <a href="{{ url('transaksi') }}" class="btn btn-secondary">Kembali ke Transaksi</a>
    </div>
</body>
</html>