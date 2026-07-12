@extends('layouts.app')
@section('content')
<div class="content-wrapper">
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Setting</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Setting</li>
        </ol>
        </div>
    </div>
    </div>
</section>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Setting</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('update_setting', $setting->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="nama_toko">Nama Toko:</label>
                    <input type="text" id="nama_toko" name="nama_toko" 
                           class="form-control @error('nama_toko') is-invalid @enderror" 
                           value="{{ old('nama_toko', $setting->nama_toko) }}">
                    @error('nama_toko')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_toko">Alamat Toko:</label>
                    <input type="text" id="alamat_toko" name="alamat_toko" 
                           class="form-control @error('alamat_toko') is-invalid @enderror" 
                           value="{{ old('alamat_toko', $setting->alamat_toko) }}">
                    @error('alamat_toko')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nama_printer">Nama Printer:</label>
                    <input type="text" id="nama_printer" name="nama_printer" 
                           class="form-control @error('nama_printer') is-invalid @enderror" 
                           value="{{ old('nama_printer', $setting->nama_printer) }}">
                    @error('nama_printer')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</section>
</div>
@endsection