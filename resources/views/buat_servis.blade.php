@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection

@section('content')
<div class="content-wrapper">

    {{-- HEADER --}}
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Data Servis</h1>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="content">
        <div class="container-fluid">

            <div class="row">

                {{-- FULL WIDTH CARD --}}
                <div class="col-12">

                    <div class="card card-primary card-outline">

                        <div class="card-header">
                            <h3 class="card-title">Form Servis Masuk</h3>
                        </div>

                        <form action="{{ route('servis.store', $transaksi->id) }}" method="POST">
                            @csrf

                            <div class="card-body">

                                <div class="row">

                                    {{-- TANGGAL --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tanggal Masuk</label>
                                            <input type="date" name="tanggal_masuk"
                                                class="form-control"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>

                                    {{-- NAMA --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Customer</label>
                                            <input type="text" name="nama" class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- NO HP --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>No HP</label>
                                            <input type="text" name="nohp" class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- ALAMAT --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <input type="text" name="alamat" class="form-control">
                                        </div>
                                    </div>

                                    {{-- MERK --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Merk HP</label>
                                            <input type="text" name="merk" class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- TIPE --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipe / Model</label>
                                            <input type="text" name="tipe" class="form-control">
                                        </div>
                                    </div>

                                    {{-- KERUSAKAN --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Kerusakan</label>
                                            <textarea name="kerusakan" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>

                                    {{-- KONDISI --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Kondisi HP</label>
                                            <input type="text" name="kondisi" class="form-control">
                                        </div>
                                    </div>

                                    {{-- PIN --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>PIN</label>
                                            <input type="text" name="pin" class="form-control">
                                        </div>
                                    </div>

                                    {{-- SANDI --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Sandi</label>
                                            <input type="text" name="sandi" class="form-control">
                                        </div>
                                    </div>

                                    {{-- POLA --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pola</label>
                                            <input type="text" name="pola" class="form-control">
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-success float-right">
                                    Simpan & Buat Servis
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </section>

</div>
@endsection