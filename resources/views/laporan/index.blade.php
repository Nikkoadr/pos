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
                                <button class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-success">
                        <div class="card-body">
                            <h5>Pendapatan</h5>
                            <h4>Rp {{ number_format($total_pendapatan,0,',','.') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-danger">
                        <div class="card-body">
                            <h5>Modal</h5>
                            <h4>Rp {{ number_format($total_modal,0,',','.') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning">
                        <div class="card-body">
                            <h5>Laba Kotor</h5>
                            <h4>Rp {{ number_format($laba_kotor,0,',','.') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <h5>Laba Bersih</h5>
                            <h4>Rp {{ number_format($laba_bersih,0,',','.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL TRANSAKSI --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Detail Transaksi</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Transaksi</th>
                                <th>Nama Barang</th>
                                <th>Harga Jual</th>
                                <th>Harga Modal</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no=1; @endphp
                            @foreach($detail as $d)
                            @php
                                $total = $d->harga_jual * $d->qty;
                                $modal = ($d->harga_modal ?? 0) * $d->qty;
                                $laba = $total - $modal;
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $d->id_transaksi }}</td>
                                <td>{{ $d->nama_barang }}</td>
                                <td>Rp {{ number_format($d->harga_jual,0,',','.') }}</td>
                                <td>Rp {{ number_format($d->harga_modal ?? 0,0,',','.') }}</td>
                                <td>{{ $d->qty }}</td>
                                <td>Rp {{ number_format($total,0,',','.') }}</td>
                                <td>Rp {{ number_format($laba,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection