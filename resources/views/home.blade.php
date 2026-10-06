@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/chart.js/Chart.min.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Ringkasan Penjualan Angel Cell</h1>
                    <span class="text-muted"><i class="fas fa-calendar-day mr-1"></i>{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="/transaksi" class="btn btn-primary"><i class="fas fa-cash-register mr-1"></i> Transaksi Baru</a>
                    <a href="/data_barang" class="btn btn-default"><i class="fas fa-box mr-1"></i> Data Barang</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">

            @can('isAdmin')
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>@rp($pendapatan_hari_ini)</h3>
                            <p>Omzet Hari Ini &middot; {{ $trx_hari_ini }} transaksi</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="{{ route('laporan.penjualan_umum') }}" class="small-box-footer">Lihat laporan <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>@rp($laba_hari_ini)</h3>
                            <p>Laba Bersih Hari Ini</p>
                        </div>
                        <div class="icon"><i class="fas fa-wallet"></i></div>
                        <a href="{{ route('laporan.penjualan_umum') }}" class="small-box-footer">Lihat laporan <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>@rp($pendapatan_bulan_ini)</h3>
                            <p>Omzet {{ now()->format('F') }} &middot; {{ $trx_bulan_ini }} transaksi</p>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                        <a href="{{ route('laporan.penjualan_member') }}" class="small-box-footer">Lihat laporan <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>@rp($laba_bulan_ini)</h3>
                            <p>Laba Bersih {{ now()->format('F') }}</p>
                        </div>
                        <div class="icon"><i class="fas fa-coins"></i></div>
                        <a href="{{ route('laporan.servis') }}" class="small-box-footer">Lihat laporan <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            @endcan

            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $servis_proses }}</h3>
                            <p>Unit Servis Aktif ({{ $servis_masuk }} antre / {{ $servis_proses - $servis_masuk }} dikerjakan)</p>
                        </div>
                        <div class="icon"><i class="fas fa-tools"></i></div>
                        <a href="/servis" class="small-box-footer">Buka servis <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stok_limit }}</h3>
                            <p>Item Stok Kritis (&lt; 5)</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <a href="/data_barang" class="small-box-footer">Restok sekarang <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3>@rp($pendapatan_tahun_ini)</h3>
                            <p>Omzet Tahun {{ now()->year }} &middot; Laba @rp($laba_tahun_ini)</p>
                        </div>
                        <div class="icon"><i class="fas fa-chart-bar"></i></div>
                        <a href="{{ route('laporan.pembelian') }}" class="small-box-footer">Lihat laporan <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            @can('isAdmin')
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-area mr-1"></i> Tren Omzet, HPP &amp; Laba</h3>
                            <div class="card-tools">
                                <div class="btn-group btn-group-sm" id="btnRentang">
                                    <button type="button" class="btn btn-primary active" data-hari="7">7 Hari</button>
                                    <button type="button" class="btn btn-default" data-hari="30">30 Hari</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="grafikOmzet" style="min-height: 260px; height: 260px; max-height: 260px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Komposisi {{ now()->format('F') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="grafikJenis" style="min-height: 220px; height: 220px; max-height: 220px;"></canvas>
                            <div class="text-center mt-2">
                                <span class="badge badge-success mr-1">Umum: {{ $donut_umum }}</span>
                                <span class="badge badge-warning mr-1">Member: {{ $donut_member }}</span>
                                <span class="badge badge-primary">Servis: {{ $donut_servis }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="card card-outline card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-fire mr-1"></i> Produk Terlaris Bulan Ini</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($produk_terlaris as $i => $p)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-{{ $i == 0 ? 'warning' : 'secondary' }} mr-2">#{{ $i + 1 }}</span>
                                        <b>{{ $p->nama }}</b><br>
                                        <small class="text-muted">@rp($p->omzet) omzet</small>
                                    </div>
                                    <span class="badge badge-success badge-pill">{{ $p->terjual }} terjual</span>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">Belum ada penjualan bulan ini</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card card-outline card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-battery-quarter mr-1"></i> Stok Menipis</h3>
                            <div class="card-tools"><span class="badge badge-danger">{{ $stok_limit }} item</span></div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($stok_menipis as $b)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <b>{{ $b->nama }}</b>
                                        <span class="badge badge-{{ $b->qty <= 0 ? 'danger' : 'warning' }}">{{ $b->qty }} pcs</span>
                                    </div>
                                    <div class="progress progress-xs mt-1">
                                        <div class="progress-bar {{ $b->qty <= 0 ? 'bg-danger' : 'bg-warning' }}" style="width: {{ min(100, max(0, $b->qty) * 20) }}%"></div>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">Semua stok aman</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="/data_barang" class="btn btn-xs btn-danger"><i class="fas fa-plus mr-1"></i> Tambah Stok</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-receipt mr-1"></i> Transaksi Terakhir</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($transaksi_terbaru as $t)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-info mr-1">#{{ $t->id }}</span>
                                        <span class="badge badge-{{ $t->jenis_transaksi == 'umum' ? 'success' : ($t->jenis_transaksi == 'member' ? 'warning' : 'primary') }}">{{ ucfirst($t->jenis_transaksi) }}</span><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d M H:i') }} &middot; {{ $t->kasir }}</small>
                                    </div>
                                    <b class="text-success">@rp($t->total)</b>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">Belum ada transaksi</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('arsip.index') }}" class="btn btn-xs btn-info">Lihat arsip</a>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Antrean Servis Aktif</h3>
                            <div class="card-tools"><span class="badge badge-primary">{{ $servis_proses }} unit</span></div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Pelanggan</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($servis_terbaru as $s)
                                    <tr>
                                        <td>{{ $s->kode_servis }}</td>
                                        <td>{{ $s->nama }}</td>
                                        <td>{{ $s->merk }} {{ $s->tipe }}</td>
                                        <td>
                                            <span class="badge {{ $s->status_servis == 'proses' ? 'badge-primary' : 'badge-secondary' }}">
                                                {{ strtoupper($s->status_servis) }}
                                            </span>
                                        </td>
                                        <td><a href="/servis" class="btn btn-xs btn-default">Buka</a></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada antrean servis aktif</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
<script>
jQuery(document).ready(function($) {
    var rupiah = function(v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); };

    // Tren 7 / 30 hari
    var data7 = {!! json_encode(isset($grafik_7) ? $grafik_7 : ['labels' => [], 'omzet' => [], 'hpp' => [], 'laba' => []]) !!};
    var data30 = {!! json_encode(isset($grafik_30) ? $grafik_30 : ['labels' => [], 'omzet' => [], 'hpp' => [], 'laba' => []]) !!};
    var grafikEl = document.getElementById('grafikOmzet');
    if (grafikEl && window.Chart) {
        var grafik = new Chart(grafikEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: data7.labels,
                datasets: [
                    { label: 'Omzet', data: data7.omzet, borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,.1)', fill: true, lineTension: .3 },
                    { label: 'HPP', data: data7.hpp, borderColor: '#dc3545', backgroundColor: 'transparent', fill: false, borderDash: [5, 5], lineTension: .3 },
                    { label: 'Laba', data: data7.laba, borderColor: '#007bff', backgroundColor: 'transparent', fill: false, lineTension: .3 }
                ]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: { callbacks: { label: function(t, d) { return ' ' + d.datasets[t.datasetIndex].label + ': ' + rupiah(t.yLabel); } } },
                scales: {
                    yAxes: [{ ticks: { callback: function(v) { return (v >= 1000000 ? (v / 1000000) + ' jt' : (v >= 1000 ? (v / 1000) + ' rb' : v)); } } }]
                }
            }
        });
        $('#btnRentang button').click(function() {
            $('#btnRentang button').removeClass('btn-primary active').addClass('btn-default');
            $(this).removeClass('btn-default').addClass('btn-primary active');
            var d = $(this).data('hari') == 30 ? data30 : data7;
            grafik.data.labels = d.labels;
            grafik.data.datasets[0].data = d.omzet;
            grafik.data.datasets[1].data = d.hpp;
            grafik.data.datasets[2].data = d.laba;
            grafik.update();
        });
    }

    // Donat komposisi
    var jenisEl = document.getElementById('grafikJenis');
    if (jenisEl && window.Chart) {
        new Chart(jenisEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Umum', 'Member', 'Servis'],
                datasets: [{ data: [{{ $donut_umum ?? 0 }}, {{ $donut_member ?? 0 }}, {{ $donut_servis ?? 0 }}], backgroundColor: ['#28a745', '#ffc107', '#007bff'] }]
            },
            options: { maintainAspectRatio: false, legend: { position: 'bottom' } }
        });
    }
});
</script>
@endsection
