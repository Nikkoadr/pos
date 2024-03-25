@extends('layouts.app')
@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">Transaksi Umum</h1>
            </div>
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
                <li class="breadcrumb-item active">Transaksi umum</li>
            </ol>
            </div>
        </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                    <h5 class="m-0">Administrasi</h5>
                    </div>
                        <div class="card-body">
                            <h6 class="card-title"><b>ID Transaksi :</b> {{ $transaksi->id }}</h6><br>
                            <h6 class="card-title"><b>Jenis Transaksi :</b> {{ $transaksi->jenis_transaksi }}</h6><br>
                            <h6 class="card-title"><b>Kasir :</b> {{ auth()->user()->nama }}</h6><br>
                            <h6 class="card-title"><b>Member :</b> {{ $transaksi->nama_member }}</h6><br>
                            <h6 class="card-title"><b>Tanggal Transaksi :</b> {{ \Carbon\Carbon::now()->locale('id_ID')->isoFormat('D MMMM YYYY') }}</h6><br>
                            <h6 class="card-title"><b>Grand Total :</b> @rp($total)</h6><br>
                            <h6 class="card-title"><b>Kembalian :</b> <span id="kembalian">0</span></h6><br>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label class="card-title"><b>Bayar :</b></label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Customer Bayar" id="bayar">
                                </div>
                            </div>
                        </div>
                    @if($total != 0)
                        <div class="card-footer">
                            <form action="{{ route('checkout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">
                                <input type="hidden" name="jenis_transaksi" value="{{ $transaksi->jenis_transaksi }}">
                                <input type="hidden" name="member" value="{{ $transaksi->nama_member }}">
                                <input type="hidden" name="bayar" id="inputBayar">
                                <input type="hidden" name="kembalian" id="inputKembalian">
                                <button class="btn btn-success float-right konfirmasi" type="submit">Checkout</button>
                            </form>
                        </div>
                    @else
                        <div class="card-footer">
                            <button class="btn btn-success float-right disabled" disabled type="submit">Keranjang masih Kosong</button>
                        </div>
                    @endif
                </div>
            <div class="card card-primary card-outline">
                <div class="card-header">
                <h5 class="m-0">Keranjang</h5>
                </div>
                <div class="card-body">
                <table id="table_keranjang" class="table table-bordered table-striped">
                    <thead>
                    <tr style="text-align: center">
                        <th>No</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>qty</th>
                        <th>Subtotal</th>
                        <th data-orderable="false">Menu</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; ?>
                    @foreach ($keranjang as $data )
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>{{ $data-> nama }}</td>
                        <td>@rp($data -> harga)</td>
                        <td class="col-3">
                            <form action="/edit_qty" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input class="form-control" type="number" name="qty" value="{{ $data->qty }}">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-info btn-block" type="submit"><i class="fa-solid fa-save"></i></button>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td>@rp($data -> subtotal)</td>
                        <td width="10%" style="text-align: center">
                            <form action="hapus_keranjang_{{ $data->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="col-lg-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                <h5 class="m-0">Pilih Barang</h5>
                </div>
                <div class="card-body">
                    <table id="table_data_barang" class="table table-bordered table-striped">
                        <thead>
                            <tr style="text-align: center">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th data-orderable="false">Jumlah</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
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
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

<script>
$(function () {
$("#table_keranjang").DataTable({
    "responsive": true, 
    "lengthChange": true, 
    "autoWidth": true,
});
});
</script>
<script>
    // Mendapatkan elemen input bayar dan span kembalian
    var inputBayar = document.getElementById('bayar');
    var inputKembalian = document.getElementById('kembalian');
    var spanKembalian = document.getElementById('kembalian');

    // Fungsi untuk memformat nilai uang dengan tanda titik sebagai pemisah ribuan
    function formatUang(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Menambahkan event listener untuk input bayar
    inputBayar.addEventListener('input', function() {
        // Mengambil nilai bayar dari elemen input
        var bayar = parseInt(inputBayar.value.replace(/\D/g, "")); // Menghapus semua karakter kecuali angka
        inputBayar.value = formatUang(bayar); // Memformat nilai uang di dalam input
        
        // Menghitung kembalian
        var total = {{ $total }};
        var kembalian = bayar - total;
        
        // Menampilkan kembalian dengan format mata uang
        spanKembalian.textContent = 'Rp. ' + formatUang(kembalian);
        
        // Update nilai input tersembunyi bayar dan kembalian
        document.getElementById('inputBayar').value = bayar;
        document.getElementById('inputKembalian').value = kembalian;
    });
</script>
<script>
$('.konfirmasi').on('click', function (event) {
    event.preventDefault();
    const form = $(this).closest('form');
    Swal.fire({
        text: "Apakah Anda yakin ingin menyelesaikan transaksi ini?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Checkout'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
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
$(document).ready(function() {
    $('#table_data_barang').DataTable({
        responsive: true, 
        lengthChange: true, 
        autoWidth: true,
        processing: true,
        serverSide: true,
        searching: true,
        ajax: {
            url: '{{ route('data-barang') }}',
            type: 'GET'
        },
columns: [
    { data: null, orderable: false, searchable: false, 
        render: function (data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
    }},
    { data: 'nama', name: 'nama' },
    { data: 'qty', name: 'qty' },
    { data: 'harga_jual1', name: 'harga_jual1' },
    { data: 'harga_jual2', name: 'harga_jual2' },
],
    });
});
</script>
@endsection