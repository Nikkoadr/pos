<!DOCTYPE html>
<html>
<head>
    <title>Nota Transaksi #{{ $transaksi->id }}</title>
    <style>
        /* Reset dan gaya dasar */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px; /* Sedikit diperbesar agar sesuai skala struk */
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
            color: #000;
            font-weight: 900; /* Memaksa font menjadi sangat tebal (extra bold) */
            line-height: 1.2; /* Jarak antar baris dirapatkan seperti printer thermal */
            letter-spacing: 0.5px; /* Memberi sedikit jarak antar huruf seperti dot-matrix */
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .header { margin-bottom: 5px; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .divider-double { border-top: 2px solid #000; margin: 8px 0; }
        
        /* Gaya untuk item */
        .item-name { 
            font-weight: 900; 
            margin-top: 4px;
        }
        .item-line { 
            display: flex; 
            justify-content: space-between; 
            font-weight: 900;
        }
        
        /* Gaya untuk total */
        .total { 
            font-weight: 900; 
            font-size: 1.6em; /* TOTAL dibuat sangat besar */
        }
        .bayar-kembali {
            font-size: 1.1em; /* BAYAR dan KEMBALI sedikit lebih kecil dari TOTAL */
            font-weight: 900;
        }
        
        .footer { 
            text-align: center; 
            margin-top: 15px; 
            font-weight: 900;
        }
        .no-print { display: block; text-align: center; margin-top: 20px; }
        
        /* Sembunyikan tombol saat print */
        @media print {
            body { width: 100%; margin: 0; padding: 3mm; }
            .no-print { display: none !important; }
        }
        .logo-img { max-width: 60mm; height: auto; margin-bottom: 5px; }
        .rp { font-weight: normal; }
        
        /* Gaya khusus untuk baris Kasir & Pelanggan agar titik dua sejajar */
        .info-row { display: flex; margin: 2px 0; }
        .info-label { width: 75px; font-weight: 900; }
        .info-value { font-weight: 900; }
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
            <h3 style="margin:0; font-size:1.5em; font-weight:900;">ANGEL CELL</h3>
        @endif
        <p style="margin:2px 0; font-weight:900;">Jalan Jangga-Terisi Desa Jangga</p>
        <p style="margin:2px 0; font-weight:900;">{{ now()->format('d M Y H:i:s') }}</p>
        <div class="divider"></div>
    </div>
    
    {{-- Bagian Kasir & Pelanggan --}}
    <div>
        <div class="info-row">
            <span class="info-label">Kasir</span>
            <span class="info-value">: {{ auth()->user()->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pelanggan</span>
            <span class="info-value">: {{ $nama_member }}</span>
        </div>
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
        {{-- TOTAL dibuat besar dan tebal --}}
        <p class="total" style="margin:2px 0;">TOTAL: Rp {{ number_format($transaksi->total_belanja, 0, '.', '.') }}</p>
        {{-- BAYAR dan KEMBALI dibuat lebih kecil --}}
        <p class="bayar-kembali" style="margin:2px 0;">BAYAR: Rp {{ number_format($transaksi->bayar, 0, '.', '.') }}</p>
        <p class="bayar-kembali" style="margin:2px 0;">KEMBALI: Rp {{ number_format($transaksi->kembalian, 0, '.', '.') }}</p>
    </div>
    <div class="divider"></div>
    <div class="footer">
        <p style="margin:2px 0; font-weight:900;">TERIMA KASIH</p>
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