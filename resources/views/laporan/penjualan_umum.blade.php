@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Laporan Penjualan Umum</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- FILTER --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('laporan.penjualan_umum') }}">
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
                                    <a href="{{ route('laporan.penjualan_umum') }}" class="btn btn-secondary" title="Reset Filter"><i class="fas fa-undo"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <p>Total Pendapatan</p>
                            <h3>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <p>Total HPP</p>
                            <h3>Rp {{ number_format($total_hpp, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-box-open"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <p>Laba Bersih</p>
                            <h3>Rp {{ number_format($total_laba_bersih, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
            </div>

            {{-- TABEL --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Daftar Transaksi Penjualan Umum</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-penjualan-umum" class="table table-hover table-bordered w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Omzet</th>
                                    <th>HPP</th>
                                    <th>Laba Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($data->tanggal)) }}</td>
                                    <td><span class="badge badge-info">{{ $data->id }}</span></td>
                                    <td>Rp {{ number_format($data->omzet, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($data->hpp, 0, ',', '.') }}</td>
                                    <td class="text-success font-weight-bold">Rp {{ number_format($data->laba_bersih, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($laporan->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right">TOTAL:</td>
                                    <td>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($total_hpp, 0, ',', '.') }}</td>
                                    <td class="text-primary">Rp {{ number_format($total_laba_bersih, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                            @endif
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
            $('#table-penjualan-umum').DataTable({
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