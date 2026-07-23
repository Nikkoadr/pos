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
            @can('isAdmin')
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary m-1" data-toggle="modal" data-target="#modal_import">
                            <i class="fa-solid fa-file-import"></i> Import
                        </button>
                        @include('layouts.component.modal_impor_data_barang')
                        <button type="button" class="btn btn-success m-1" data-toggle="modal" data-target="#modal_tambah_data_barang">
                            <i class="fa-solid fa-boxes-packing"></i> Tambah
                        </button>
                        @include('layouts.component.modal_tambah_data_barang')
                        <button class="btn btn-info m-1 btn-multiple-print">
                            <i class="fa-solid fa-print"></i> Print Terpilih
                        </button>
                        <button class="btn btn-danger m-1" id="hapus_terpilih">
                            <i class="fa-solid fa-trash-can"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>
            </div>
            @endcan

            <div class="card-body">
                {{-- FILTER KATEGORI (Hanya untuk Admin) --}}
                @can('isAdmin')
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-inline">
                            <div class="form-group mr-2">
                                <label class="mr-2"><i class="fas fa-tag"></i> <strong>Filter Kategori:</strong></label>
                                <select name="kategori" id="filter_kategori" class="form-control" style="min-width: 200px;">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach($kategori_list as $kat)
                                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                                            {{ ucfirst($kat) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="reset_filter">
                                <i class="fas fa-times"></i> Reset Filter
                            </button>
                            <div class="ml-auto">
                                <span class="text-muted">Total Barang: <strong id="total_barang">0</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                {{-- TABEL --}}
                <table id="table_data_barang" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px;">
                                <input type="checkbox" id="select_all">
                            </th>
                            <th>Barcode</th>
                            <th>Kategori</th>
                            <th>Nama</th>
                            <th>Qty</th>
                            @can('isAdmin')
                            <th>Harga Modal</th>
                            @endcan
                            <th>Harga Umum</th>
                            @can('isAdmin')
                            <th>Harga Member</th>
                            <th style="text-align: center" data-orderable="false">Menu</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data diisi oleh DataTables server-side -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@can('isAdmin')
{{-- MODAL TAMBAH STOK GENERIK --}}
<div class="modal fade" id="modal_tambah_stok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok: <span id="stok_barang_nama"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_tambah_stok" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Stok Saat Ini</label>
                        <input type="text" id="stok_saat_ini" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Tambah (Qty)</label>
                        <input type="number" name="qty" class="form-control" placeholder="Contoh: 5" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Harga Modal (per unit)</label>
                        <input type="number" name="harga_modal" class="form-control" step="0.01" min="0" required>
                        <small class="text-muted">*Harga modal terbaru untuk barang ini.</small>
                    </div>
                    <input type="hidden" name="id_supplier" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
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
    // Inisialisasi DataTables server-side
    var table = $('#table_data_barang').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('data_barang.json') }}",
            type: 'GET',
            data: function (d) {
                d.kategori = $('#filter_kategori').val();
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'barcode', name: 'barcode' },
            { data: 'kategori', name: 'kategori' },
            { data: 'nama', name: 'nama' },
            { data: 'qty', name: 'qty' },
            @can('isAdmin')
            { data: 'harga_modal', name: 'harga_modal' },
            @endcan
            { data: 'harga_umum', name: 'harga_umum' },
            @can('isAdmin')
            { data: 'harga_member', name: 'harga_member' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            @endcan
        ],
        order: [[1, 'asc']],
        drawCallback: function(settings) {
            var info = this.api().page.info();
            $('#total_barang').text(info.recordsTotal);
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        lengthMenu: [
            [10, 20, 50, 100, 1000],
            [10, 20, 50, 100, "1000"]
        ],
        pageLength: 10,
        dom: 'frtip' // Memastikan hanya kotak pencarian (f), tabel (r & t), informasi (i), dan paginasi (p) yang tampil
    });

    // Filter kategori -> reload DataTable
    $('#filter_kategori').on('change', function() {
        table.ajax.reload();
    });

    // Reset filter
    $('#reset_filter').on('click', function() {
        $('#filter_kategori').val('');
        table.ajax.reload();
    });

    // Select all checkbox
    $('#select_all').on('click', function(e) {
        var isChecked = $(this).is(':checked');
        $(".sub_chk").prop('checked', isChecked);
    });

    @can('isAdmin')
    // Print terpilih
    $('.btn-multiple-print').on('click', function(e) {
        var allVals = [];
        $(".sub_chk:checked").each(function() {
            allVals.push($(this).data('id'));
        });

        if (allVals.length <= 0) {
            Swal.fire({
                icon: 'warning',
                text: "Pilih setidaknya satu barang untuk dicetak!"
            });
        } else {
            var ids = allVals.join(",");
            window.open("{{ url('data_barang/cetak-barcode') }}?ids=" + ids, '_blank');
        }
    });

    // Hapus terpilih
    $('#hapus_terpilih').on('click', function(e) {
        var allVals = [];
        $(".sub_chk:checked").each(function() {
            allVals.push($(this).data('id'));
        });

        if (allVals.length <= 0) {
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
                        url: "{{ route('barang.hapus_multiple') }}",
                        type: 'DELETE',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: 'ids='+join_selected_values,
                        success: function (data) {
                            if (data['status'] == true) {
                                Swal.fire('Berhasil!', data['message'], 'success').then(() => {
                                    table.ajax.reload(null, false);
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

    // Event klik tombol tambah stok (delegasi)
    $(document).on('click', '.btn-tambah-stok', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var qty = $(this).data('qty');
        var hargaModal = $(this).data('harga_modal');

        $('#stok_barang_nama').text(nama);
        $('#stok_saat_ini').val(qty);
        $('#form_tambah_stok input[name="harga_modal"]').val(hargaModal);
        $('#form_tambah_stok').attr('action', "{{ url('barang/tambah-stok') }}/" + id);
    });

    // Konfirmasi hapus per item (delegasi)
    $(document).on('click', '.konfirmasi', function(event) {
        event.preventDefault();
        const url = $(this).attr('href');
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
    @endcan

    // Toast notifikasi sukses
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
        });
    @endif

    // Tampilkan modal tambah jika ada error
    @if ($errors->any())
        $('#modal_tambah_data_barang').modal('show');
    @endif

    // Custom file input
    bsCustomFileInput.init();
});
</script>
@endsection