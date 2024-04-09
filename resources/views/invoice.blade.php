<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ config('app.name', 'Laravel') }} ID : {{ $nota->id }}</title>
<link rel="stylesheet" href="{{ asset('assets/dist/css/invoice.css') }}">
</head>
<body>
    <div class="header">
        <h1 style="font-size: 25px;">Invoice {{ config('app.name', 'Laravel') }}</h1>
    </div>

    <h2 style="font-size: 20px">Informasi Invoice</h2>
<table>
    <tr>
        <td><strong>ID Invoice</strong></td>
        <td><strong>:</strong></td>
        <td class="wide-column">{{ $nota->id }}</td>
    </tr>
    <tr>
        <td><strong>Jenis Transaksi</strong></td>
        <td><strong>:</strong></td>
        <td class="wide-column">{{ $nota->jenis_transaksi }}</td>
    </tr>
    <tr>
        <td><strong>Kasir</strong></td>
        <td><strong>:</strong></td>
        <td class="wide-column">{{ $nota->kasir }}</td>
    </tr>
    <tr>
        <td><strong>Tanggal Transaksi</strong></td>
        <td><strong>:</strong></td>
        <td class="wide-column">{{ \Carbon\Carbon::parse($nota->tanggal_transaksi)->locale('id_ID')->isoFormat('D MMMM YYYY, HH:mm') }}</td>
    </tr>
</table>

    <h2 style="font-size: 20px">Detail Barang</h2>
    <table>
        <thead>
            <tr>
                <th>Qty</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $count = count($nota->detailNota); @endphp
            @foreach($nota->detailNota as $detail)
                <tr>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->nama_barang }}</td>
                    <td>@rp($detail->harga)</td>
                    <td>@rp($detail->subtotal)</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td class="amount-label">Total Item</td>
            <td>:</td>
            <td class="amount">{{ $count }}</td>
        </tr>
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
    <h5 style="font-size: 12px; text-align: left;">Catatan: Barang yang sudah dibeli tidak bisa dikembalikan</h5>
    <h5 style="text-align: center; font-size: 15px;" class="note">Terima Kasih</h5>
    <script> window.print(); </script>
</body>
</html>