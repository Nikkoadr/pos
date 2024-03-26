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
                <form method="POST" action="update_data_member_{{ $data->id }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_toko" value="1">
                    <div class="form-group">
                        <label for="nama_member">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_member" name="nama_member" value="{{ $data->nama_member }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nomor_hp">Nomor HP</label>
                        <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="{{ $data->nomor_hp }}" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $data->alamat }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="/data_member" class="btn btn-danger">Kembali</a>
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

