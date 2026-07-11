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

            {{-- FILTER --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('laporan') }}">
                        <div class="row d-flex align-items-end">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal Awal</label>
                                    <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label><i class="fas fa-calendar-alt"></i> Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label><i class="fas fa-tag"></i> Jenis Transaksi</label>
                                    <select name="jenis" class="form-control">
                                        <option value="semua" {{ $jenis == 'semua' ? 'selected' : '' }}>Semua</option>
                                        <option value="umum" {{ $jenis == 'umum' ? 'selected' : '' }}>Penjualan Umum</option>
                                        <option value="member" {{ $jenis == 'member' ? 'selected' : '' }}>Penjualan Member</option>
                                        <option value="servis" {{ $jenis == 'servis' ? 'selected' : '' }}>Servis</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="{{ route('laporan') }}" class="btn btn-secondary" title="Reset">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
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
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <p>Laba Bersih</p>
                            <h3>Rp {{ number_format($total_laba_bersih,0,',','.') }}</h3>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <p>Total Pembelian Barang</p>
                            <h3>Rp {{ number_format($total_pembelian,0,',','.') }}</h3>
                            <span class="badge bg-dark">{{ $total_qty_pembelian }} item</span>
                        </div>
                        <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                </div>
            </div>

            {{-- TABEL LAPORAN PENJUALAN --}}
            <div class="card mt-3">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Daftar Transaksi Penjualan Selesai</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0 table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode Transaksi</th>
                                    <th>Jenis</th>
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
                                    <td>
                                        @if($data->jenis == 'umum')
                                            <span class="badge badge-success">Umum</span>
                                        @elseif($data->jenis == 'member')
                                            <span class="badge badge-warning">Member</span>
                                        @elseif($data->jenis == 'servis')
                                            <span class="badge badge-primary">Servis</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($data->omzet, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($data->hpp, 0, ',', '.') }}</td>
                                    <td class="text-success font-weight-bold">
                                        Rp {{ number_format($data->laba_bersih, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data penjualan untuk periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            
                            @if($laporan->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="4" class="text-right">TOTAL PERIODE:</td>
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

            {{-- TABEL LAPORAN PEMBELIAN --}}
            <div class="card mt-3">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Daftar Pembelian Barang</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0 table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode Pembelian</th>
                                    <th>Supplier</th>
                                    <th>Nama Barang</th>
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
                                    <td class="text-danger font-weight-bold">
                                        Rp {{ number_format($p->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada pembelian untuk periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            
                            @if($detailPembelian->count() > 0)
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="7" class="text-right">TOTAL PEMBELIAN:</td>
                                    <td class="text-danger">Rp {{ number_format($total_pembelian, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAMPILKAN PENYESUAIAN (jika ada) --}}
            @if($penyesuaian->count() > 0)
            <div class="card mt-3 border-warning">
                <div class="card-header border-warning">
                    <h3 class="card-title"><i class="fas fa-edit"></i> Penyesuaian Stok (ADJ)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0 table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Barang</th>
                                    <th>Qty</th>
                                    <th>Harga Modal</th>
                                    <th>Total Penyesuaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penyesuaian as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ date('d/m/Y', strtotime($p->tanggal_pembelian)) }}</td>
                                    <td><span class="badge badge-warning">{{ $p->kode_pembelian }}</span></td>
                                    <td>{{ $p->barang ? $p->barang->nama : '-' }}</td>
                                    <td>{{ $p->qty }}</td>
                                    <td>Rp {{ number_format($p->harga_modal, 0, ',', '.') }}</td>
                                    <td class="text-info">
                                        Rp {{ number_format($p->qty * $p->harga_modal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="6" class="text-right font-weight-bold">TOTAL PENYESUAIAN:</td>
                                    <td class="text-info font-weight-bold">
                                        Rp {{ number_format($total_penyesuaian, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection