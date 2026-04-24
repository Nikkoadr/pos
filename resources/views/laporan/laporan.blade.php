@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Laporan Transaksi</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Laporan</li>
        </ol>
        </div>
    </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter</h3>
    </div>
    <div class="card-body">
        <form action="/laporan_filter" method="POST">
            @csrf
            <div class="form-row">
                <div class="col-md-3 form-group">
                    <label for="tanggal">Harian:</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control">
                </div>
            <div class="col-md-3 form-group">
                <label for="bulan">Bulanan:</label>
                <select id="bulan" name="bulan" class="form-control">
                    <option value="">-- Pilih Bulan --</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ \Carbon\Carbon::create(null, $i, null)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
                <div class="col-md-3 form-group">
                    <label for="tahun">Tahunan:</label>
                    <input type="number" id="tahun" name="tahun" min="1900" max="2099" step="1" value="{{ date('Y') }}" class="form-control">
                </div>
                <div class="col-md-3 form-group align-self-end">
                    <label></label>
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan</h3>
    </div>
    <div class="card-body">
        @include('layouts.partials.laporan_table', ['nota' => $nota, 'total_belanja' => $total_belanja])
    </div>
</div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
