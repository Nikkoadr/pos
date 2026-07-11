@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Laporan Pembelian Barang</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            {{-- FILTER --}}
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
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i></button>
                                    <a href="{{ route('laporan.pembelian') }}" class="btn btn-secondary"><i class="fas fa-undo"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <p>Total Pembelian</p>
                            <h3>Rp {{ number_format($total_pembelian,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <p>Total Qty Barang</p>
                            <h3>{{ number_format($total_qty,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-boxes"></i></div>
                    </div>
                </div>
            </div>

            {{-- TABEL --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pembelian Barang</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Supplier</th>
                                    <th>Barang</th>
                                    <th>Qty</th>
                                    <th>Harga Modal</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detailPembelian as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ date('d/m/Y', strtotime($p->tanggal)) }}</td>
                                    <td><span class="badge badge-secondary">{{ $p->kode_pembelian }}</span></td>
                                    <td>{{ $p->supplier }}</td>
                                    <td>{{ $p->nama_barang }}</td>
                                    <td>{{ $p->qty }}</td>
                                    <td>Rp {{ number_format($p->harga_modal, 0, ',', '.') }}</td>
                                    <td class="text-danger font-weight-bold">Rp {{ number_format($p->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                            @if($detailPembelian->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="7" class="text-right">TOTAL:</td>
                                    <td class="text-danger">Rp {{ number_format($total_pembelian, 0, ',', '.') }}</td>
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