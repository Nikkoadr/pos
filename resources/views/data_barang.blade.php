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
        <h1>Data Barang</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Data Barang</li>
        </ol>
        </div>
    </div>
    </div>
</section>

<section class="content">
    <div class="card">
    <div class="card-header">
            <button type="button" class="btn btn-primary m-1" data-toggle="modal" data-target="#modal_import"><i class="fa-solid fa-file-import"></i> Import</button>
            @include('layouts.component.modal_impor_data_barang')
            {{-- <a href="exportuser" class="btn btn-info m-1" target="_blank"><i class="fa-solid fa-file-export"></i> Export</a> --}}
            <button type="button" class="btn btn-success m-1" data-toggle="modal" data-target="#modal_tambah_data_barang"><i class="fa-solid fa-boxes-packing"></i> Tambah</button>
            @include('layouts.component.modal_tambah_data_barang')
        </div>
            <div class="card-body">
            <table id="table_data_barang" class="table table-bordered table-striped">
                <thead>
                <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Qty</th>
                <th>Harga Modal</th>
                <th>Harga Umum</th>
                <th>Harga Grosir</th>
                <th style="text-align: center" data-orderable="false">Menu</th>
                </tr>
                </thead>
                <tbody>
                <?php $no=1; ?>
                @foreach ($data_barang as $data )
                <tr>
                    <td><?= $no++ ?></td>
                    <td>{{ $data -> nama }}</td>
                    <td>{{ $data -> qty }}</td>
                    <td>@rp($data -> harga_modal)</td>
                    <td>@rp($data -> harga_jual1)</td>
                    <td>@rp($data -> harga_jual2)</td>
                    <td style="text-align: center">
                        <a href="view_edit_data_barang_{{ $data->id }}" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i></i></a>
                        <a href="hapus_data_barang_{{ $data->id }}" class="btn btn-danger konfirmasi m-1"><i class="far fa-trash-alt"></i></a>
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
<script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script>
$(function () {
$("#table_data_barang").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": true,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#table_data_barang_wrapper .col-md-6:eq(0)');
});
</script>
<script>
$(function () {
    bsCustomFileInput.init();
});
</script>
<script>
@if (session()->has('success'))
var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
    Toast.fire({
    icon: 'success',
    title: '{{ session('success') }}'
    })
@endif
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

