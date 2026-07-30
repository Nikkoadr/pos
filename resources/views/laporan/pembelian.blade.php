@extends('layouts.app')

@section('link')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Laporan Pembelian Barang</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- FILTER TANGGAL --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('laporan.pembelian') }}">
                        <div class="row d-flex align-items-end">
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary" title="Filter Data"><i class="fas fa-filter"></i> Filter</button>
                                    <a href="{{ route('laporan.pembelian') }}" class="btn btn-secondary" title="Reset Filter"><i class="fas fa-undo"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <p>Total Pembelian</p>
                            <h3>Rp {{ number_format($total_pembelian, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <p>Total Qty Barang</p>
                            <h3>{{ number_format($total_qty, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-boxes"></i></div>
                    </div>
                </div>
            </div>

            {{-- TABEL DATATABLE --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pembelian Barang</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-pembelian" class="table table-hover table-bordered w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Barang</th>
                                    <th>Qty</th>
                                    <th>Harga Modal</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailPembelian as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ date('d/m/Y', strtotime($p->tanggal)) }}</td>
                                    <td><span class="badge badge-secondary">{{ $p->kode_pembelian }}</span></td>
                                    <td>{{ $p->nama_barang }}</td>
                                    <td>{{ $p->qty }}</td>
                                    <td>Rp {{ number_format($p->harga_modal, 0, ',', '.') }}</td>
                                    <td class="text-danger font-weight-bold">Rp {{ number_format($p->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
    jQuery(document).ready(function($) {
        if ($.fn.DataTable) {
            $('#table-pembelian').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                "language": {
                    "sSearch": "Cari:",
                    "sLengthMenu": "Tampilkan _MENU_ data",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "sZeroRecords": "Data tidak ditemukan",
                    "oPaginate": {
                        "sPrevious": "Sebelumnya",
                        "sNext": "Selanjutnya"
                    }
                }
            });
        }
    });
</script>
@endsection