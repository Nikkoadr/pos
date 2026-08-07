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
                {{-- Kolom kiri: Informasi utama --}}
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
                                    <span class="badge badge-primary"><i class="fas fa-tools"></i> SERVIS</span>
                                @elseif($transaksi->jenis_transaksi == 'member')
                                    <span class="badge badge-warning"><i class="fas fa-user"></i> PENJUALAN MEMBER</span>
                                @else
                                    <span class="badge badge-success"><i class="fas fa-shopping-cart"></i> PENJUALAN UMUM</span>
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
                        <div class="card-footer bg-white">
                            <form action="{{ route('transaksi.cetak-ulang', $transaksi->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-print mr-1"></i>
                                    Cetak Ulang Nota
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Kolom kanan: Rincian item dan detail servis --}}
                <div class="col-md-8">
                    {{-- Tabel rincian item --}}
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
                                    @forelse($detailItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}.</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>@rp($item->harga_jual)</td>
                                        <td>{{ $item->qty }}</td>
                                        <td class="text-right">@rp($item->subtotal)</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada item</td>
                                    </tr>
                                    @endforelse
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

                    {{-- Detail unit servis (jika ada) --}}
                    @if($transaksi->jenis_transaksi == 'servis' && isset($servisData) && $servisData)
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Detail Unit Servis</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Unit:</strong> {{ $servisData->merk }} {{ $servisData->tipe }}<br>
                                    <strong>Pelanggan:</strong> {{ $servisData->nama }} ({{ $servisData->nohp }})<br>
                                    <strong>Alamat:</strong> {{ $servisData->alamat }}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Kerusakan:</strong><br>
                                    <p class="text-danger">{{ $servisData->kerusakan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')
{{-- Tidak ada script tambahan untuk halaman ini --}}
@endsection