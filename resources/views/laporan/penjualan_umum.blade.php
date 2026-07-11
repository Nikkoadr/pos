@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Laporan Penjualan Umum</h1>
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
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i></button>
                                    <a href="{{ route('laporan.penjualan_umum') }}" class="btn btn-secondary"><i class="fas fa-undo"></i></a>
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
                            <h3>Rp {{ number_format($total_pendapatan,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <p>Total HPP</p>
                            <h3>Rp {{ number_format($total_hpp,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-box-open"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <p>Laba Bersih</p>
                            <h3>Rp {{ number_format($total_laba_bersih,0,',','.') }}</h3>
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
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
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
                                <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
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