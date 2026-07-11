@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Karyawan</h1>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Edit Karyawan</h5>
            </div>
            <div class="card-body">
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
                <form method="POST" action="{{ route('karyawan.update', $karyawan->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama Karyawan</label>
                        <input type="text"
                            name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $karyawan->nama) }}">
                        @error('nama')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror">
                            <option value="admin"
                                {{ old('role', $karyawan->role) == 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                            <option value="karyawan"
                                {{ old('role', $karyawan->role) == 'karyawan' ? 'selected' : '' }}>
                                Karyawan
                            </option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="text"
                            name="nomor_hp"
                            class="form-control @error('nomor_hp') is-invalid @enderror"
                            value="{{ old('nomor_hp', $karyawan->nomor_hp) }}">
                        @error('nomor_hp')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $karyawan->email) }}">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Password (kosongkan jika tidak diganti)</label>
                        <input type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password"
                            name="password_confirmation"
                            class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Update Data
                    </button>
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection