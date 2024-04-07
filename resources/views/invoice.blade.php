<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Courier New, monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        h1, h2 {
            font-size: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .header p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice Toko Andreas</h1>
        <p>Alamat: Blok Karang Mulya RT 17 RW 05 Desa Jangga Kecamatan Losarang Kabupaten Indramayu 45253</p>
        <p>Nomor HP: 08129002000</p>
    </div>


    <h2>Informasi Nota</h2>
    <table>
        <tr>
            <th>Id Invoice</th>
            <td>{{ $nota->id }}</td>
        </tr>
        <tr>
            <th>Jenis Transaksi</th>
            <td>{{ $nota->jenis_transaksi }}</td>
        </tr>
        <tr>
            <th>Kasir</th>
            <td>{{ $nota->kasir }}</td>
        </tr>
        <tr>
            <th>Tanggal Transaksi</th>
            <td>{{ \Carbon\Carbon::parse($nota->tanggal_transaksi)->locale('id_ID')->isoFormat('D MMMM YYYY') }}</td>
        </tr>
        <tr>
            <th>Total Belanja</th>
            <td>@rp($nota->total_belanja)</td>
        </tr>
        <tr>
            <th>Bayar</th>
            <td>@rp($nota->bayar)</td>
        </tr>
        <tr>
            <th>Kembalian</th>
            <td>@rp($nota->kembalian)</td>
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
    <h5>Catatan: Barang yang sudah dibeli tidak bisa dikembalikan</h5>
    <script> window.print(); </script>
</body>
</html>
