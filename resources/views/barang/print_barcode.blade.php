<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode 33x15mm 2 Line</title>
    <style>
        /* Konfigurasi Ukuran Kertas */
        @page {
            size: 70mm 15mm; /* Total lebar sekitar 2 stiker + gap */
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: white;
        }

        /* Container utama untuk membungkus semua barcode */
        .container {
            width: 70mm; /* Sesuai lebar total label */
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-content: flex-start;
        }

        /* Box per item stiker */
        .barcode-item {
            width: 33mm;     /* Lebar stiker sesuai spek */
            height: 15mm;    /* Tinggi stiker sesuai spek */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            padding: 1mm;
            /* Tambahkan margin kanan sedikit jika ada gap antar stiker (biasanya 2mm) */
            margin-right: 2mm; 
            text-align: center;
            /* border: 0.1mm solid #eee; */ /* Hapus komentar ini jika ingin tes posisi saat potong */
        }

        /* Hilangkan margin kanan untuk stiker kedua di tiap baris */
        .barcode-item:nth-child(2n) {
            margin-right: 0;
        }

        .item-name {
            font-size: 7px;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            margin-bottom: 1px;
        }

        /* Styling barcode agar pas di box */
        .barcode-img {
            margin: 0 auto;
            display: block;
        }

        .item-code {
            font-size: 6px;
            margin-top: 1px;
        }

        .item-price {
            font-size: 7px;
            font-weight: bold;
            margin-top: 0;
        }

        @media print {
            body { width: 70mm; }
            .barcode-item { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        @foreach($data_barang as $item)
            <div class="barcode-item">
                <div class="item-name">{{ substr($item->nama, 0, 20) }}</div>
                
                <div class="barcode-img">
                    {!! DNS1D::getBarcodeHTML($item->barcode, 'C128', 1.0, 22) !!}
                </div>
                
                <div class="item-code">{{ $item->barcode }}</div>
                <div class="item-price">@rp($item->harga_umum)</div>
            </div>
        @endforeach
    </div>
</body>
</html>