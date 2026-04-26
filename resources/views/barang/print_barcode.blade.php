<!DOCTYPE html>
<html>
<head>
    <title>Cetak Barcode</title>
    <style>
        @media print {
            @page {
                margin: 0;
            }
            body {
                margin: 1cm;
            }
        }
        .barcode-item {
            display: inline-block;
            text-align: center;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px;
            width: 150px;
        }
        .item-name {
            font-size: 10px;
            font-family: sans-serif;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .item-price {
            font-size: 10px;
            font-family: sans-serif;
            margin-top: 5px;
        }
    </style>
</head>
<body onload="window.print()">
    <div style="text-align: center;">
        @foreach($data_barang as $item)
            <div class="barcode-item">
                <div class="item-name">{{ $item->nama }}</div>
                
                {!! DNS1D::getBarcodeHTML($item->barcode, 'C128', 1.5, 33) !!}
                
                <div class="item-price">{{ $item->barcode }}</div>
                <div class="item-price">@rp($item->harga_umum)</div>
            </div>
        @endforeach
    </div>
</body>
</html>