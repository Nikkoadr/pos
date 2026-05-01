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
            <button type="button" class="btn btn-success m-1" data-toggle="modal" data-target="#modal_tambah_data_barang"><i class="fa-solid fa-boxes-packing"></i> Tambah</button>
            @include('layouts.component.modal_tambah_data_barang')
            
            <button class="btn btn-info m-1 btn-multiple-print"><i class="fa-solid fa-print"></i> Print Terpilih</button>
            <button class="btn btn-danger m-1" id="hapus_terpilih">
                <i class="fa-solid fa-trash-can"></i> Hapus Terpilih
            </button>
        </div>
            <div class="card-body">
            <table id="table_data_barang" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px;">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th>No</th>
                        <th>Barcode</th>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Harga Modal</th>
                        <th>Harga Umum</th>
                        <th>Harga Member</th>
                        <th style="text-align: center" data-orderable="false">Menu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; ?>
                    @foreach ($data_barang as $data)
                    <tr>
                        <td>
                            <input type="checkbox" class="sub_chk" data-id="{{ $data->id }}">
                        </td>
                        <td>{{ $no++ }}</td>
                        <td>{{ $data->barcode }}</td>
                        <td>{{ $data->nama }}</td>
                        <td>{{ $data->qty }}</td>
                        <td>@rp($data->harga_modal)</td>
                        <td>@rp($data->harga_umum)</td>
                        <td>@rp($data->harga_member)</td>
<td style="text-align: center">
    <!-- Tombol Tambah Stok -->
    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal_tambah_stok_{{ $data->id }}">
        <i class="fa-solid fa-plus"></i>
    </button>
    
    <a href="view_edit_data_barang_{{ $data->id }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen-to-square"></i></a>
    <a href="hapus_data_barang_{{ $data->id }}" class="btn btn-sm btn-danger konfirmasi"><i class="far fa-trash-alt"></i></a>
</td>

<!-- Modal Tambah Stok (Letakkan di dalam foreach agar mendapatkan ID barang) -->
<div class="modal fade" id="modal_tambah_stok_{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok: {{ $data->nama }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('barang.tambah_stok', $data->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Stok Saat Ini</label>
                        <input type="text" class="form-control" value="{{ $data->qty }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Penambahan</label>
                        <input type="number" name="jumlah_tambah" class="form-control" placeholder="Contoh: 5" required min="1">
                        <small class="text-muted">*Stok akan otomatis diakumulasikan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
$(document).ready(function () {
    // Fungsi Centang Semua
    $('#select_all').on('click', function(e) {
        if($(this).is(':checked',true)) {
            $(".sub_chk").prop('checked', true);
        } else {
            $(".sub_chk").prop('checked',false);
        }
    });

$('.btn-multiple-print').on('click', function(e) {
    var allVals = [];
    $(".sub_chk:checked").each(function() {
        allVals.push($(this).attr('data-id'));
    });

    if(allVals.length <= 0) {
        Swal.fire({
            icon: 'warning',
            text: "Pilih setidaknya satu barang untuk dicetak!"
        });
    } else {
        // Gabungkan ID dengan koma, misal: 1,2,5
        var ids = allVals.join(",");
        // Buka tab baru menuju route cetak
        window.open("{{ url('data_barang/cetak-barcode') }}?ids=" + ids, '_blank');
    }
});

$('#hapus_terpilih').on('click', function(e) {
    var allVals = [];
    // Ambil semua ID dari checkbox yang dicentang
    $(".sub_chk:checked").each(function() {
        allVals.push($(this).attr('data-id'));
    });

    if(allVals.length <= 0) {
        Swal.fire({
            icon: 'info',
            text: "Pilih data yang ingin dihapus terlebih dahulu!"
        });
    } else {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Data yang dipilih (" + allVals.length + " item) akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                var join_selected_values = allVals.join(",");
                
                $.ajax({
                    url: "{{ route('barang.hapus_multiple') }}", // Sesuaikan nama route
                    type: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: 'ids='+join_selected_values,
                    success: function (data) {
                        if (data['status'] == true) {
                            // Berhasil, reload table atau halaman
                            Swal.fire('Berhasil!', data['message'], 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    },
                    error: function (data) {
                        Swal.fire('Error!', data.responseText, 'error');
                    }
                });
            }
        });
    }
});
});
</script>
<script>
$(function () {
$("#table_data_barang").DataTable({
    "responsive": true, "lengthChange": false, "autoWidth": true,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#table_data_barang_wrapper .col-md-6:eq(0)');
});
</script>
@if ($errors->any())
<script>
    $(document).ready(function() {
        $('#modal_tambah_data_barang').modal('show');
    });
</script>
@endif
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

