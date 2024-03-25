@if ($nota->isNotEmpty())
    <table class="table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Jenis Transaksi</th>
                <th>Kasir</th>
                <th>Tanggal Transaksi</th>
                <th>Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($nota as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->jenis_transaksi }}</td>
                    <td>{{ $data->kasir }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal_transaksi)->locale('id_ID')->isoFormat('dddd, D MMMM YYYY') }}</td>
                    <td>@rp($data->total_belanja)</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>Total Belanja:</strong></td>
                <td><strong>@rp($total_belanja)</strong></td>
            </tr>
        </tfoot>
    </table>
@else
    <div class="alert alert-info" role="alert">
        Tidak ada data untuk ditampilkan.
    </div>
@endif