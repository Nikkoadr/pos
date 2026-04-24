@extends('layouts.app')
@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')
<div class="content-wrapper">
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Riwayat Transaksi</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Riwayat Transaksi</li>
        </ol>
        </div>
    </div>
    </div>
</section>

<section class="content">
    <div class="card col-12">
        <div class="card-header">
            <h3 class="card-title">Pilih Bulan</h3>
        </div>
        <div class="card-body">
            <form method="get" action="/riwayat_transaksi">
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bulan">Bulan:</label>
                            <select class="form-control" id="bulan" name="bulan">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == $bulan ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($i)->isoFormat('MMMM') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tahun">Tahun:</label>
                            <input type="text" class="form-control" id="tahun" name="tahun" placeholder="Masukkan tahun" value="{{ $tahun }}">
                        </div>
                    </div>
                    <div class="col-md-2 align-self-end">
                        <div class="form-group">
                            <label></label>
                            <button type="submit" class="btn btn-primary btn-block">Cari</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Nota</h3>
    </div>
            <div class="card-body">
            <table id="table_data_riwayat" class="table table-bordered table-striped">
                <thead>
                <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Jenis Transaksi</th>
                <th>Nama Member</th>
                <th>Tanggal dan Jam Transaksi</th>
                <th>Total Belanja</th>
                <th>Customer Bayar</th>
                <th>Kembalian</th>
                <th>Menu</th>
                </tr>
                </thead>
                <tbody>
                <?php $no=1; ?>
                @foreach ($riwayat_transaksi as $data )
                <tr>
                    <td><?= $no++ ?></td>
                    <td>{{ $data->id }}</td>
                    <td>{{ $data->jenis_transaksi }}</td>
                    <td>{{ $data->member }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal_transaksi)->translatedFormat('l, j F Y') }}
                        Jam {{ \Carbon\Carbon::parse($data->tanggal_transaksi)->translatedFormat('H:i:s') }}</td>
                    <td>@rp($data -> total_belanja)</td>
                    <td>@rp($data -> bayar)</td>
                    <td>@rp($data -> kembalian)</td>
                    <td>
                        <a target="_blank" href="nota_{{ $data -> id }}" class="btn btn-info"  ><i class="fa-solid fa-circle-info"></i></i></a>
                        <a href="hapus_nota_{{ $data->id }}" class="btn btn-danger konfirmasi m-1"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
    </div>
</section>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script>
$(function () {
$("#table_data_riwayat").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": true,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#table_data_riwayat_wrapper .col-md-6:eq(0)');
});
</script>
<script>
document.querySelectorAll('.konfirmasi').forEach(function(element) {
    element.addEventListener('click', function (event) {
        event.preventDefault();
        const url = this.getAttribute('href');
        Swal.fire({
            text: "Anda yakin ingin menghapus data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
@endsection
