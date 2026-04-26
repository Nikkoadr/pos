@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Transaksi #{{ $transaksi->id }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('arsip.index') }}">Arsip</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Utama</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-calendar-alt mr-1"></i> Tanggal</strong>
                            <p class="text-muted">{{ date('d M Y H:i', strtotime($transaksi->tanggal_transaksi)) }}</p>
                            <hr>

                            <strong><i class="fas fa-user mr-1"></i> Kasir / Pelaksana</strong>
                            <p class="text-muted">{{ $transaksi->kasir }}</p>
                            <hr>

                            <strong><i class="fas fa-tag mr-1"></i> Jenis Transaksi</strong>
                            <p>
                                @if($transaksi->jenis_transaksi == 'servis')
                                    <span class="badge badge-primary">SERVIS</span>
                                @else
                                    <span class="badge badge-success">PENJUALAN</span>
                                @endif
                            </p>
                            <hr>

                            <strong><i class="fas fa-money-bill-wave mr-1"></i> Pembayaran</strong>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Total</td>
                                    <td class="text-right"><b>@rp($grandTotal)</b></td>
                                </tr>
                                <tr>
                                    <td>Bayar</td>
                                    <td class="text-right">@rp($transaksi->bayar)</td>
                                </tr>
                                <tr class="border-top">
                                    <td>Kembali</td>
                                    <td class="text-right text-success"><b>@rp($transaksi->kembalian)</b></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Rincian Item</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Nama Barang / Jasa</th>
                                        <th>Harga</th>
                                        <th style="width: 40px">Qty</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detail as $index => $d)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td>{{ $d->nama_barang }}</td>
                                        <td>@rp($d->harga_jual)</td>
                                        <td>{{ $d->qty }}</td>
                                        <td class="text-right">@rp($d->harga_jual * $d->qty)</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Grand Total</th>
                                        <th class="text-right text-primary" style="font-size: 1.2rem">
                                            @rp($grandTotal)
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    @if($transaksi->jenis_transaksi == 'servis')
                    @php 
                        $servis = \App\Models\DetailTransaksiServis::where('id_transaksi', $transaksi->id)->first();
                    @endphp
                    @if($servis)
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Detail Unit Servis</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Unit:</strong> {{ $servis->merk }} {{ $servis->tipe }}<br>
                                    <strong>Pelanggan:</strong> {{ $servis->nama }} ({{ $servis->nohp }})<br>
                                    <strong>Alamat:</strong> {{ $servis->alamat }}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Kerusakan:</strong><br>
                                    <p class="text-danger">{{ $servis->kerusakan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')
@endsection