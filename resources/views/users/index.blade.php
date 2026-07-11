@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Data Karyawan</h1>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                    + Tambah Karyawan
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="tableKaryawan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Nomor HP</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($karyawan as $i => $k)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $k->nama }}</td>
                            <td>{{ $k->role }}</td>
                            <td>{{ $k->nomor_hp }}</td>
                            <td>{{ $k->email }}</td>
                            <td>
                                <a href="{{ route('karyawan.edit', $k->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>
                                <button class="btn btn-danger btn-sm btn-delete"
                                    data-id="{{ $k->id }}">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title">TAMBAH KARYAWAN</h4>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="{{ route('karyawan.store') }}">
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <b>Terjadi kesalahan input:</b>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <label class="font-weight-bold">Nama Karyawan</label>
                    <input type="text"
                        name="nama"
                        class="form-control mb-2 @error('nama') is-invalid @enderror"
                        value="{{ old('nama') }}">
                    @error('nama')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <label class="font-weight-bold">Role / Jabatan</label>
                    <select name="role" class="form-control mb-2 @error('role') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>
                        <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>
                            Karyawan
                        </option>
                    </select>
                    @error('role')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <label class="font-weight-bold">Nomor HP</label>
                    <input type="text"
                        name="nomor_hp"
                        class="form-control mb-2 @error('nomor_hp') is-invalid @enderror"
                        value="{{ old('nomor_hp') }}">
                    @error('nomor_hp')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <label class="font-weight-bold">Email</label>
                    <input type="email"
                        name="email"
                        class="form-control mb-2 @error('email') is-invalid @enderror"
                        value="{{ old('email') }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <label class="font-weight-bold">Password</label>
                    <input type="password"
                        name="password"
                        class="form-control mb-2 @error('password') is-invalid @enderror">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                    <label class="font-weight-bold">Konfirmasi Password</label>
                    <input type="password"
                        name="password_confirmation"
                        class="form-control @error('password_confirmation') is-invalid @enderror">
                    @error('password_confirmation')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-block">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true
});
@if(session('success'))
Toast.fire({
    icon: 'success',
    title: "{{ session('success') }}"
});
@endif
@if ($errors->any())
Toast.fire({
    icon: 'error',
    title: "Terjadi kesalahan input!"
});
@endif
$(document).on('click', '.btn-delete', function () {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Yakin hapus data?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus'
    }).then((result) => {
        if (result.isConfirmed) {

            let form = `
                <form action="/data_karyawan/delete/${id}" method="POST">
                    @csrf
                    @method('DELETE')
                </form>
            `;

            $('body').append(form);
            form = $('body').find('form').last();
            form.submit();
        }
    });
});
$(document).ready(function () {
    $('#tableKaryawan').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true
    });
});
@if ($errors->any())
$(document).ready(function () {
    $('#modalTambah').modal('show');
});
@endif
</script>
@endsection