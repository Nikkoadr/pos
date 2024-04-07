<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        @page {
            size: 100mm 140mm;
            margin: 5mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 2mm;
            padding: 0;
            color: #333;
            font-size: 80%; /* Skala font ke 80% */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th {
            border-bottom: 1px solid black; /* Menetapkan garis bawah pada th */
            padding: 8px;
            text-align: left;
            background-color: #f2f2f2;
        }
        td {
            padding: 8px;
            text-align: left;
        }
        h1, h2 {
            font-size: 18px;
            margin: 5px 0;
            color: #333;
        }
        h2 {
            margin-top: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #555;
        }
        .header p {
            margin: 5px 0;
            color: #777;
        }
        h5 {
            font-size: 14px;
            margin-top: 15px;
            color: #777;
        }
        /* Style untuk label jumlah */
        .amount-label {
            font-weight: bold;
            color: #333;
            text-align: right;
        }
        /* Style untuk jumlah yang besar */
        .amount {
            font-weight: bold;
            color: #333;
            text-align: right;
        }
        /* Style untuk catatan */
        .note {
            font-size: 12px;
            color: #777;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice Toko Andreas</h1>
        <p>Alamat: Blok Karang Mulya RT 17 RW 05 Desa Jangga Kecamatan Losarang Kabupaten Indramayu 45253</p>
    </div>

    <h2>Informasi Nota</h2>
    <table>
        <tr>
            <th>Id Invoice</th>
            <th>Jenis Transaksi</th>
            <th>Kasir</th>
            <th>Tanggal Transaksi</th>
        </tr>
        <tr>
            <td>{{ $nota->id }}</td>
            <td>{{ $nota->jenis_transaksi }}</td>
            <td>{{ $nota->kasir }}</td>
            <td>{{ \Carbon\Carbon::parse($nota->tanggal_transaksi)->locale('id_ID')->isoFormat('D MMMM YYYY, HH:mm') }}</td>
        </tr>
    </table>

    <h2>Detail Barang</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nota->detailNota as $detail)
                <tr>
                    <td>{{ $detail->nama_barang }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>@rp($detail->harga)</td>
                    <td>@rp($detail->subtotal)</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="amount-label">Total Belanja</td>
            <td>:</td>
            <td class="amount">@rp($nota->total_belanja)</td>
        </tr>
        <tr>
            <td class="amount-label">Bayar</td>
            <td>:</td>
            <td class="amount">@rp($nota->bayar)</td>
        </tr>
        <tr>
            <td class="amount-label">Kembalian</td>
            <td>:</td>
            <td class="amount">@rp($nota->kembalian)</td>
        </tr>
    </table>
    <h5 class="note">Catatan: Barang yang sudah dibeli tidak bisa dikembalikan</h5>
    <script> window.print(); </script>
</body>
</html>