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
                    <h1>Edit Data Barang</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="/data_barang">Data Barang</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

                <form method="POST" action="{{ route('update_data_barang', $data->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Barcode</label>
                        <input type="text" name="barcode"
                            class="form-control @error('barcode') is-invalid @enderror"
                            value="{{ old('barcode', $data->barcode) }}">
                        @error('barcode')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control @error('kategori') is-invalid @enderror">
                            <option value="">Pilih Kategori</option>
                            <option value="umum" {{ old('kategori', $data->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                            <option value="member" {{ old('kategori', $data->kategori) == 'member' ? 'selected' : '' }}>Member</option>
                            <option value="sparepart" {{ old('kategori', $data->kategori) == 'sparepart' ? 'selected' : '' }}>Sparepart</option>
                            <option value="aksesoris" {{ old('kategori', $data->kategori) == 'aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $data->nama) }}">
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Qty</label>
                        <input type="number" name="qty"
                            class="form-control @error('qty') is-invalid @enderror"
                            value="{{ old('qty', $data->qty) }}">

                        @error('qty')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Harga Modal</label>
                        <input type="number" name="harga_modal"
                            class="form-control @error('harga_modal') is-invalid @enderror"
                            value="{{ old('harga_modal', $data->harga_modal) }}">

                        @error('harga_modal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Harga Umum</label>
                        <input type="number" name="harga_umum"
                            class="form-control @error('harga_umum') is-invalid @enderror"
                            value="{{ old('harga_umum', $data->harga_umum) }}">

                        @error('harga_umum')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Harga Member</label>
                        <input type="number" name="harga_member"
                            class="form-control @error('harga_member') is-invalid @enderror"
                            value="{{ old('harga_member', $data->harga_member) }}">

                        @error('harga_member')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('data_barang') }}" class="btn btn-danger">Kembali</a>
                    </div>

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
    });
@endif
</script>
@endsection