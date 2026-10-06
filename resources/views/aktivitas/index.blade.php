@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Jejak Aktivitas Kasir</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/home">Home</a></li>
                        <li class="breadcrumb-item active">Aktivitas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <form method="GET" action="{{ route('aktivitas.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="date" name="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggal_akhir }}">
                            </div>
                            <div class="col-md-2">
                                <select name="aksi" class="form-control">
                                    <option value="">Semua aksi</option>
                                    @foreach(['tambah' => 'Tambah', 'ubah' => 'Ubah', 'hapus' => 'Hapus', 'tambah-stok' => 'Tambah Stok', 'checkout' => 'Checkout', 'status' => 'Ubah Status'] as $val => $label)
                                    <option value="{{ $val }}" {{ request('aksi') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="model" class="form-control">
                                    <option value="">Semua data</option>
                                    @foreach(['barang' => 'Barang', 'member' => 'Member', 'transaksi' => 'Transaksi', 'servis' => 'Servis', 'user' => 'Karyawan'] as $val => $label)
                                    <option value="{{ $val }}" {{ request('model') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="q" class="form-control" placeholder="Cari user / keterangan..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-block" title="Filter"><i class="fas fa-filter"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Data</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->nama_user }}</td>
                                <td>
                                    <span class="badge badge-{{ $log->aksi == 'hapus' ? 'danger' : ($log->aksi == 'tambah' || $log->aksi == 'checkout' ? 'success' : ($log->aksi == 'tambah-stok' ? 'info' : 'warning')) }}">
                                        {{ strtoupper(str_replace('-', ' ', $log->aksi)) }}
                                    </span>
                                </td>
                                <td><span class="badge badge-secondary">{{ $log->model }}@if($log->model_id) #{{ $log->model_id }}@endif</span></td>
                                <td>{{ $log->keterangan }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">Tidak ada aktivitas pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-left">Total: {{ $logs->total() }} aktivitas</div>
                    <div class="float-right">{{ $logs->links() }}</div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
