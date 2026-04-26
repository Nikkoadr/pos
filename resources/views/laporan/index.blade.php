@extends('layouts.app')

@section('content')
<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <h1>Laporan Keuangan</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            {{-- FILTER TANGGAL --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('laporan') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Tanggal Awal</label>
                                <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                            </div>
                            <div class="col-md-4">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                            </div>
                            <div class="col-md-4 mt-4">
                                <button class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                <a href="{{ route('laporan') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RINGKASAN BOX --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <p>Total Pendapatan (Omzet)</p>
                            <h3>Rp {{ number_format($total_pendapatan,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <p>Total Modal (HPP)</p>
                            <h3>Rp {{ number_format($total_hpp,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-box-open"></i></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <p>Laba Kotor</p>
                            <h3>Rp {{ number_format($total_laba_kotor,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <p>Laba Bersih</p>
                            <h3>Rp {{ number_format($total_laba_kotor,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-wallet"></i></div>
                    </div>
                </div>
            </div>

            {{-- TABEL LAPORAN PER TRANSAKSI --}}
            <div class="card mt-3">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Daftar Transaksi Selesai</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0 table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode Transaksi</th>
                                    <th>Omzet (Penjualan)</th>
                                    <th>HPP (Modal)</th>
                                    <th>Laba Kotor</th>
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
                                    <td class="text-success font-weight-bold">
                                        Rp {{ number_format($data->laba_kotor, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Data tidak ditemukan untuk periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            
                            @if($laporan->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right">TOTAL PERIODE:</td>
                                    <td>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($total_hpp, 0, ',', '.') }}</td>
                                    <td class="text-primary">Rp {{ number_format($total_laba_kotor, 0, ',', '.') }}</td>
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