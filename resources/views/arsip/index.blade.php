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
                </div>
                <div class="card-body">
                    <table id="table-arsip" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
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
        $('#table-arsip').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: "{{ route('arsip.data') }}", // Menuju ke fungsi data_arsip di controller
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tanggal_transaksi', name: 'tanggal_transaksi' },
                { data: 'jenis_transaksi', name: 'jenis_transaksi' },
                { data: 'kasir', name: 'kasir' },
                { data: 'total_belanja', name: 'total_belanja' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            order: [[1, 'desc']] // Default urut berdasarkan tanggal terbaru
        });
    });

    // Fungsi placeholder untuk cetak nota dari arsip
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
        })
    }
</script>
@endsection