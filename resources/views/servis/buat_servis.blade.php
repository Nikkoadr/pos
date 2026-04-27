@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<style>
    .pattern-help { cursor: pointer; color: #007bff; font-size: 0.8rem; text-decoration: underline; }
    .card-title { font-weight: bold; }
    .required::after { content: " *"; color: red; }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Servis</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools mr-1"></i> {{ isset($servis) ? 'Edit' : 'Tambah' }} Data Servis Masuk</h3>
                </div>
                
                <form action="{{ url('servis/store/'.$transaksi->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        {{-- INFORMASI PELANGGAN --}}
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h5 class="text-primary border-bottom pb-2"><i class="fas fa-user mr-1"></i> Informasi Pelanggan</h5>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Tanggal Masuk</label>
                                    <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                        value="{{ old('tanggal_masuk', $servis->tanggal_masuk ?? date('Y-m-d')) }}">
                                    @error('tanggal_masuk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Nama Customer</label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                        value="{{ old('nama', $servis->nama ?? '') }}" placeholder="Nama Lengkap">
                                    @error('nama') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">No HP / WhatsApp</label>
                                    <input type="text" name="nohp" class="form-control @error('nohp') is-invalid @enderror"
                                        value="{{ old('nohp', $servis->nohp ?? '') }}" placeholder="0812xxxx">
                                    @error('nohp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                        value="{{ old('alamat', $servis->alamat ?? '') }}" placeholder="Alamat Singkat">
                                    @error('alamat') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- DETAIL PERANGKAT --}}
                        <div class="row mt-4">
                            <div class="col-md-12 mb-3">
                                <h5 class="text-primary border-bottom pb-2"><i class="fas fa-mobile-alt mr-1"></i> Detail Perangkat & Kerusakan</h5>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="required">Merk HP</label>
                                    <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror"
                                        value="{{ old('merk', $servis->merk ?? '') }}" placeholder="Contoh: Samsung, iPhone">
                                    @error('merk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipe / Model</label>
                                    <input type="text" name="tipe" class="form-control @error('tipe') is-invalid @enderror"
                                        value="{{ old('tipe', $servis->tipe ?? '') }}" placeholder="Contoh: Galaxy S23, iPhone 14 Pro">
                                    @error('tipe') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kondisi Fisik</label>
                                    <input type="text" name="kondisi" class="form-control @error('kondisi') is-invalid @enderror"
                                        value="{{ old('kondisi', $servis->kondisi ?? '') }}" placeholder="Mulus, Lecet, Pecah, dll">
                                    @error('kondisi') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">Kerusakan</label>
                                    <textarea name="kerusakan" class="form-control @error('kerusakan') is-invalid @enderror" rows="3" 
                                        placeholder="Jelaskan keluhan perangkat...">{{ old('kerusakan', $servis->kerusakan ?? '') }}</textarea>
                                    @error('kerusakan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Security (Pola/PIN)</label> 
                                    <small class="pattern-help" data-toggle="modal" data-target="#modalPola">Lihat Petunjuk Pola</small>
                                    <input type="text" name="security" class="form-control @error('security') is-invalid @enderror"
                                        value="{{ old('security', $servis->security ?? '') }}" placeholder="Contoh: 1-2-3-6 atau PIN: 123456">
                                    @error('security') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Estimasi Waktu Pengambilan</label>
                                    <input type="datetime-local" name="waktu_pengambilan" class="form-control @error('waktu_pengambilan') is-invalid @enderror"
                                        value="{{ old('waktu_pengambilan', isset($servis->waktu_pengambilan) ? \Carbon\Carbon::parse($servis->waktu_pengambilan)->format('Y-m-d\TH:i') : '') }}">
                                    @error('waktu_pengambilan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <a href="/transaksi" class="btn btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success float-right shadow-sm">
                            <i class="fas fa-save mr-1"></i>
                            {{ isset($servis) ? 'Perbarui Data Servis' : 'Simpan & Cetak Tanda Terima' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

{{-- MODAL PETUNJUK POLA --}}
<div class="modal fade" id="modalPola" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">Panduan Kode Pola</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3">Gunakan urutan angka 1-9 untuk mencatat pola kunci layar:</p>
                <img src="{{ asset('assets/dist/img/pattern.png') }}" class="img-fluid mb-3 border rounded shadow-sm" style="max-width: 180px;" alt="Pattern Guide">
                <div class="alert alert-warning py-2 mb-0">
                    <small>Contoh pola <b>"L"</b>: <br> ketik <b>1-4-7-8-9</b></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection