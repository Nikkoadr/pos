@extends('layouts.app')
@section('link')
    <style>
/* Style for member suggestions container */
.suggestions-container {
    display: none;
    position: absolute;
    width: 50%;
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 5px 5px;
    background-color: #fff;
    z-index: 1000;
}

/* Style for individual suggestion item */
.suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.suggestion-item:hover {
    background-color: #f0f0f0;
}
        </style>
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Transaksi Aktif</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Transaksi Aktif</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Tabel Transaksi Aktif -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Transaksi Aktif</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Transaksi</th>
                                    <th>Jenis Transaksi</th>
                                    <th>Nama Member</th>
                                    <th>Tanggal</th>
                                    <th style="text-align: center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaksi as $data)
                                    <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data->id }}</td>
                                    <td>{{ $data->jenis_transaksi }}</td>
                                    <td>
                                        @if ($data->nama_member)
                                            {{ $data->nama_member }}
                                        @else
                                            Tidak ada Member
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($data->tanggal_transaksi)->translatedFormat('l, j F Y') }}
                                        Jam {{ \Carbon\Carbon::parse($data->tanggal_transaksi)->translatedFormat('H:i:s') }}</td>
                                    <td style="text-align: center">
                                        <a href="proses_transaksi_{{ $data->id }}" class="btn btn-success"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

            <!-- Formulir Membuat Transaksi Baru -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buat Transaksi Baru</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="buat_transaksi" method="POST">
                            @csrf
                            <!-- Tambahkan field-form untuk membuat transaksi baru -->
                            <div class="form-group">
                                <label for="jenis_transaksi">Jenis Transaksi</label>
                                <select class="form-control" id="jenis_transaksi" name="jenis_transaksi">
                                    <option value="umum">Umum</option>
                                    <option value="member">Member</option>
                                </select>
                            </div>
                            <!-- Jika transaksi melibatkan member, tampilkan opsi untuk memilih member -->
                            <div class="form-group" id="nama_member">
                                <label for="id_member">Pilih Member</label>
                                <input type="text" class="form-control" id="nama_member_input" name="nama_member" autocomplete="off">
                                <div id="member_suggestions" class="suggestions-container"></div>
                                <input type="hidden" id="id_member" name="id_member">
                            </div>
                            <!-- Tambahkan field-form lainnya sesuai kebutuhan -->
                            <button type="submit" class="btn btn-primary">Buat Transaksi</button>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection

@section('script')
    <script>
// Jalankan saat halaman dimuat pertama kali
document.addEventListener('DOMContentLoaded', function() {
    // Ambil nilai jenis transaksi saat halaman dimuat
    var initialTransaksiValue = document.getElementById('jenis_transaksi').value;
    var namaMemberField = document.getElementById('nama_member');

    // Periksa apakah jenis transaksi saat halaman dimuat adalah "member"
    if (initialTransaksiValue === 'member') {
        namaMemberField.style.display = 'block';
    } else {
        namaMemberField.style.display = 'none';
        // Reset value pilihan member jika jenis transaksi bukan member
        document.getElementById('nama_member_input').value = '';
        document.getElementById('id_member').value = '';
        document.getElementById('member_suggestions').innerHTML = '';
    }

    // Tambahkan event listener untuk perubahan jenis transaksi
    document.getElementById('jenis_transaksi').addEventListener('change', function() {
        var memberValue = this.value;
        var namaMemberField = document.getElementById('nama_member');
        if (memberValue === 'member') {
            namaMemberField.style.display = 'block';
        } else {
            namaMemberField.style.display = 'none';
            // Reset value pilihan member jika jenis transaksi bukan member
            document.getElementById('nama_member_input').value = '';
            document.getElementById('id_member').value = '';
            document.getElementById('member_suggestions').innerHTML = '';
        }
    });
});

// AJAX untuk mencari anggota berdasarkan input teks
document.getElementById('nama_member_input').addEventListener('input', function() {
    var inputText = this.value;
    if (inputText.length > 0) {
        // Kirim request AJAX untuk mencari anggota
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Berhasil menerima respons, tampilkan hasil
                    var suggestions = JSON.parse(xhr.responseText);
                    var suggestionsContainer = document.getElementById('member_suggestions');
                    suggestionsContainer.innerHTML = '';
                    suggestions.forEach(function(suggestion) {
                        var option = document.createElement('div');
                        option.textContent = suggestion.nama_member;
                        option.className = 'suggestion-item';
                        option.setAttribute('data-member-id', suggestion.id);
                        option.addEventListener('click', function() {
                            document.getElementById('nama_member_input').value = suggestion.nama_member;
                            document.getElementById('id_member').value = suggestion.id;
                            suggestionsContainer.innerHTML = '';
                            suggestionsContainer.style.display = 'none';
                        });
                        suggestionsContainer.appendChild(option);
                    });
                    // Tampilkan daftar saran
                    suggestionsContainer.style.display = 'block';
                } else {
                    // Gagal menerima respons
                    console.error('Request failed');
                }
            }
        };
        xhr.open('GET', '/search/member?keyword=' + inputText, true);
        xhr.send();
    } else {
        // Kosongkan sugesti jika input kosong
        document.getElementById('member_suggestions').innerHTML = '';
        // Sembunyikan daftar saran
        document.getElementById('member_suggestions').style.display = 'none';
    }
});
</script>
<script>
@if (session()->has('sukses'))
var Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
    Toast.fire({
    icon: 'success',
    title: '{{ session('sukses') }}'
    })
@endif
</script>
<script>
@if (session()->has('transaksi_sukses'))
Swal.fire({
title: "{{ session('transaksi_sukses') }}",
text: "Apakah anda ingin print nota transaksi ini",
icon: "success",
showCancelButton: true,
confirmButtonColor: "#3085d6",
cancelButtonColor: "#d33",
confirmButtonText: "Ya, Print",
cancelButtonText: "Tidak",
}).then((result) => {
if (result.isConfirmed) {
window.open("/nota_" + {{ session('transaksi_id') }}, '_blank');
}
});
@endif
</script>
@endsection