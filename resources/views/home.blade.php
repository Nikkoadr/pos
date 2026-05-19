@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Ringkasan Penjualan Angel Cell</h1>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            
            @can('isAdmin')
            <div class="row">
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>@rp($pendapatan_hari_ini)</h3>
                            <p>Pendapatan Hari Ini</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>@rp($pendapatan_bulan_ini)</h3>
                            <p>Total Bulan Ini ({{ now()->format('F') }})</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>@rp($pendapatan_tahun_ini)</h3>
                            <p>Total Tahun Ini ({{ now()->year }})</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            @endcan
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $servis_proses }}</h3>
                            <p>Unit Servis Sedang Dikerjakan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $stok_limit }}</h3>
                            <p>Item Stok Hampir Habis</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Antrean Servis Aktif</h3>
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