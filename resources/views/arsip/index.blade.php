@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Arsip Transaksi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Arsip</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Penjualan & Servis Selesai</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="date" id="filterTanggal" class="form-control float-right" value="{{ date('Y-m-d') }}">
                            <div class="input-group-append">
                                <button type="button" id="btnFilter" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="arsipTab">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" data-jenis="">Semua</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-jenis="umum">Penjualan Umum</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-jenis="member">Penjualan Member</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-jenis="servis">Servis</a>
                        </li>
                    </ul>

                    <table id="table-arsip" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kasir</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>

<script>
    $(function () {
        let jenis = "";
        let tanggal = $('#filterTanggal').val();

        var table = $('#table-arsip').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('arsip.data') }}",
                data: function(d){
                    d.jenis = jenis;
                    d.tanggal = tanggal;
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable:false, searchable:false },
                { data: 'tanggal_transaksi' },
                { data: 'jenis_transaksi' },
                { data: 'kasir' },
                { data: 'pelanggan', orderable:false, searchable:false },
                { data: 'total_belanja' },
                { data: 'action', orderable:false, searchable:false },
            ]
        });

        // Filter jenis
        $('#arsipTab .nav-link').click(function(e){
            e.preventDefault();
            $('#arsipTab .nav-link').removeClass('active');
            $(this).addClass('active');
            jenis = $(this).data('jenis');
            table.ajax.reload();
        });

        // Filter tanggal
        $('#btnFilter').click(function(){
            tanggal = $('#filterTanggal').val();
            table.ajax.reload();
        });

        // Enter key pada input tanggal
        $('#filterTanggal').on('keypress', function(e){
            if(e.which === 13) {
                $('#btnFilter').click();
            }
        });
    });

    // Fungsi placeholder untuk cetak nota
    function printNota(id) {
        Swal.fire({
            title: 'Cetak Ulang?',
            text: "Nota akan dikirim ke printer thermal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Cetak!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get("/arsip/print/" + id, function(data) {
                    toastr.success('Perintah cetak dikirim.');
                }).fail(function() {
                    toastr.error('Gagal menghubungkan ke printer.');
                });
            }
        });
    }
</script>
@endsection