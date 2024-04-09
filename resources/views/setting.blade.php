@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
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
    </div><!-- /.container-fluid -->
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
                <form action="update_setting_{{ $setting->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nama_toko">Nama Toko:</label>
                        <input type="text" id="nama_toko" name="nama_toko" class="form-control" value="{{ $setting->nama_toko }}">
                    </div>
                    <div class="form-group">
                        <label for="alamat_toko">Alamat Toko:</label>
                        <input type="text" id="alamat_toko" name="alamat_toko" class="form-control" value="{{ $setting->alamat_toko }}">
                    </div>
                    <div class="form-group">
                        <label for="printer">Jenis Printer:</label>
                        <select name="printer" id="printer" class="form-control">
                            <option value="excel" @if($setting->printer === 'excel') selected @endif>Excel Web View</option>
                            <option value="termal" @if($setting->printer === 'termal') selected @endif>Printer Termal 58mm</option>
                            <option value="default" @if($setting->printer === 'default') selected @endif>Default(95mm X 140mm)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->
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