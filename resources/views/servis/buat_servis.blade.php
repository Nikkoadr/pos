@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<style>
    .pattern-help { cursor: pointer; color: #007bff; font-size: 0.8rem; }
    .card-title { font-weight: bold; }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Tambah Data Servis</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools mr-1"></i> Form Servis Masuk</h3>
                </div>
                <form action="{{ url('servis/store/'.$transaksi->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">

                            {{-- SECTION 1 --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Masuk</label>
                                    <input type="date" name="tanggal_masuk" class="form-control"
                                        value="{{ $servis->tanggal_masuk ?? date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Nama Customer</label>
                                    <input type="text" name="nama" class="form-control"
                                        value="{{ $servis->nama ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>No HP / WhatsApp</label>
                                    <input type="text" name="nohp" class="form-control"
                                        value="{{ $servis->nohp ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control"
                                        value="{{ $servis->alamat ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-12 mt-2">
                                <hr>
                                <h5><i class="fas fa-mobile-alt mr-1"></i> Detail Perangkat</h5>
                            </div>

                            {{-- SECTION 2 --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Merk HP</label>
                                    <input type="text" name="merk" class="form-control"
                                        value="{{ $servis->merk ?? '' }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipe / Model</label>
                                    <input type="text" name="tipe" class="form-control"
                                        value="{{ $servis->tipe ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kondisi Fisik</label>
                                    <input type="text" name="kondisi" class="form-control"
                                        value="{{ $servis->kondisi ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Kerusakan</label>
                                    <textarea name="kerusakan" class="form-control" rows="2" required>{{ $servis->kerusakan ?? '' }}</textarea>
                                </div>
                            </div>

                            {{-- SECTION 3 --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PIN</label>
                                    <input type="text" name="pin" class="form-control"
                                        value="{{ $servis->pin ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sandi</label>
                                    <input type="text" name="sandi" class="form-control"
                                        value="{{ $servis->sandi ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pola</label><small class="pattern-help" data-toggle="modal" data-target="#modalPola"> (Lihat Contoh Petunjuk) </small>
                                    <input type="text" name="pola" class="form-control"
                                        value="{{ $servis->pola ?? '' }}">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <a href="/transaksi" class="btn btn-default">Batal</a>

                        {{-- 🔥 tombol dinamis --}}
                        <button type="submit" class="btn btn-success float-right">
                            <i class="fas fa-save mr-1"></i>
                            {{ isset($servis) ? 'Update Servis' : 'Simpan & Buat Servis' }}
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
            <div class="modal-header">
                <h5 class="modal-title">Panduan Kode Pola</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>Gunakan urutan angka untuk mencatat pola:</p>
                <img src="{{ asset('assets/dist/img/pattern.png') }}" class="img-fluid mb-3" style="max-width: 200px;" alt="Pattern Guide">
                <div class="alert alert-info py-2">
                    <small>Misal pola huruf <b>"L"</b> maka tulis: <b>1-4-7-8-9</b></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection