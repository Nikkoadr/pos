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

                            @if($transaksi->jenis_transaksi == 'member')
                                <h6 class="card-title"><b>Member :</b> {{ $transaksi->nama_member ?? 'Tidak ada Member' }}</h6><br>
                            @else
                                <h6 class="card-title"><b>Member :</b> -</h6><br>
                            @endif

                            <h6 class="card-title"><b>Tanggal Transaksi :</b> {{ \Carbon\Carbon::now()->locale('id_ID')->isoFormat('D MMMM YYYY') }}</h6><br>
                            @if($transaksi->jenis_transaksi == 'servis')
                                <hr>
                                <h6 class="card-title"><b>Data Servis :</b></h6><br>

                                @if(isset($servis))
                                    <h6 class="card-title"><b>Kode :</b> {{ $servis->kode_servis }}</h6><br>
                                    <h6 class="card-title"><b>Customer :</b> {{ $servis->nama }}</h6><br>
                                    <h6 class="card-title"><b>No HP :</b> {{ $servis->nohp }}</h6><br>
                                    <h6 class="card-title"><b>Device :</b> {{ $servis->merk }} - {{ $servis->tipe }}</h6><br>
                                    <h6 class="card-title"><b>Kerusakan :</b> {{ $servis->kerusakan }}</h6><br>

                                    <h6 class="card-title">
                                        <b>Estimasi :</b> @rp($total)
                                    </h6><br>

                                @else
                                    <h6 class="text-danger"><b>Data servis belum diisi!</b></h6>
                                    <a href="{{ url('transaksi_servis_'.$transaksi->id) }}" class="btn btn-warning btn-sm">
                                        Isi Servis
                                    </a><br><br>
                                @endif
                            @endif

                            {{-- ================= NON SERVIS (ADA TRANSAKSI) ================= --}}
                            @if($transaksi->jenis_transaksi != 'servis')
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
                            @endif

                        </div>

                        <div class="card-footer">

                            @if($transaksi->jenis_transaksi == 'servis')
                                @if(isset($servis))
                                    <a href="{{ url('cetak_transaksi_servis/'.$transaksi->id) }}" class="btn btn-primary float-right">
                                        <i class="fas fa-print mr-1"></i> Cetak Nota Pengambilan
                                    </a>
                                @else
                                    <button class="btn btn-secondary float-right disabled">
                                        Isi data servis dulu
                                    </button>
                                @endif

                            @else
                                @if($total != 0)
                                    <form action="{{ route('checkout') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">
                                        <input type="hidden" name="jenis_transaksi" value="{{ $transaksi->jenis_transaksi }}">
                                        
                                        @if($transaksi->jenis_transaksi == 'member')
                                            <input type="hidden" name="member" value="{{ $transaksi->id_member }}">
                                        @endif

                                        <input type="hidden" name="bayar" id="inputBayar">
                                        <input type="hidden" name="kembalian" id="inputKembalian">

                                        <button class="btn btn-success float-right konfirmasi" type="submit">
                                            Checkout
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-success float-right disabled" disabled>
                                        Keranjang masih Kosong
                                    </button>
                                @endif
                            @endif

                        </div>
                    </div>
                <div class="card card-primary card-outline">
                    <div class="card-header">
                    <h5 class="m-0">Keranjang</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" id="scanner" class="form-control" placeholder="Scan Barcode di sini..." autofocus>
                        </div>
                        <div class="mb-3">
                        <form action="/tambah_manual" method="POST">
                            @csrf
                            <input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">

                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="nama" class="form-control" placeholder="Nama jasa / item" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="harga" class="form-control" placeholder="Harga" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="qty" class="form-control" value="1" min="1" placeholder="qty">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success btn-block" type="submit">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                            <td>
                                {{ $data->nama }}
                                @if(is_null($data->id_barang))
                                    <span class="badge badge-warning">Manual</span>
                                @endif
                            </td>
                            <td>@rp($data -> harga_jual)</td>
                            <td class="col-3">
                                @if(is_null($data->id_barang))
                                {{ $data->qty }}
                                @else
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
                                @endif
                            </td>
                            <td>@rp($data->harga_jual * $data->qty)</td>
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
                                        <th data-orderable="false">No</th>
                                        <th>Nama</th>
                                        <th>Stok</th>
                                        <th>Harga</th>
                                        <th data-orderable="false">Tambah Ke Keranjang</th>
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
$('#scanner').on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();

        let barcode = $(this).val().trim();

        if (barcode === '') return;

        $.ajax({
            url: '/scan-barang',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                barcode: barcode,
                id_transaksi: '{{ $transaksi->id }}',
                id_member: '{{ $transaksi->id_member }}'
            },
            success: function(res) {
                $('#scanner').val('');
                location.reload();
            },
            error: function() {
                alert('Barang tidak ditemukan!');
                $('#scanner').val('');
            }
        });
    }
});
</script>
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

    var inputBayar = document.getElementById('bayar');
    var inputKembalian = document.getElementById('kembalian');
    var spanKembalian = document.getElementById('kembalian');
    function formatUang(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    inputBayar.addEventListener('input', function() {
        var bayar = parseInt(inputBayar.value.replace(/\D/g, "")) || 0;
        inputBayar.value = formatUang(bayar);
        var total = {{ $total }};
        var kembalian = bayar - total;
        spanKembalian.textContent = 'Rp. ' + formatUang(kembalian);
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
                }
            },
            { data: 'nama', name: 'nama' },
            { data: 'qty', name: 'qty' },
            @if ( $transaksi->jenis_transaksi == 'member')
                { data: 'harga_member', name: 'harga_member', render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') },  
            @else

                { data: 'harga_umum', name: 'harga_umum', render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') },
            @endif
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, full, meta) {
                    let csrfToken = '{{ csrf_token() }}';
                    let transaksiId = '{{ $transaksi->id }}';
                    let transaksiMember = '{{ $transaksi->id_member }}';
                    return `
                        <form method="post" action="/tambah_keranjang">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="id_transaksi" value="${transaksiId}">
                                    <input type="hidden" name="id_member" value="${transaksiMember}">
                                    <input type="hidden" name="id" value="${full.id}">
                                    <input class="form-control" type="number" name="jumlah" min="1" max="${full.qty}" value="1">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-info" type="submit">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>`;
                }
            }
        ]
    });
});
</script>

@endsection