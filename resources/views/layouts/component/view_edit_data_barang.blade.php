@extends('layouts.app')
@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection
@section('content')
<div class="content-wrapper">
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Data Barang</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item ">Data Barang</li>
            <li class="breadcrumb-item active">Edit Data Barang</li>
        </ol>
        </div>
    </div>
    </div>
</section>

<section class="content">
    <div class="card">
    <div class="card-header">
        <h5 class="m-0">Form Update Barang</h5>
        </div>
            <div class="card-body">
                <form method="POST" action="update_data_barang_{{ $data->id }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nama">Nama Barang</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $data->nama }}" required>
                    </div>
                    <div class="form-group">
                        <label for="qty">Qty</label>
                        <input type="number" class="form-control" id="qty" name="qty" value="{{ $data->qty }}" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_modal">Harga Modal</label>
                        <input type="number" class="form-control" id="harga_modal" name="harga_modal" value="{{ $data->harga_modal }}" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_jual1">Harga Umum</label>
                        <input type="number" class="form-control" id="harga_jual1" name="harga_jual1" value="{{ $data->harga_jual1 }}" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_jual2">Harga Grosir</label>
                        <input type="number" class="form-control" id="harga_jual2" name="harga_jual2" value="{{ $data->harga_jual2 }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="/data_barang" class="btn btn-danger">Kembali</a>
                </form>
            </div>
    </div>
</section>
</div>
@endsection
@section('script')
<script>
@if (session()->has('success'))
var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
    Toast.fire({
    icon: 'success',
    title: '{{ session('success') }}'
    })
@endif
</script>
@endsection

