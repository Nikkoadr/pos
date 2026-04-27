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
                                            <div class="btn-group">

                                                @if($data->jenis_transaksi == 'servis')

                                                    {{-- KHUSUS SERVIS --}}
                                                    <a href="{{ url('transaksi_servis_'.$data->id) }}" class="btn btn-info">
                                                        <i class="fas fa-tools"></i>
                                                    </a>

                                                @else

                                                    {{-- TRANSAKSI BIASA --}}
                                                    <a href="{{ url('proses_transaksi_'.$data->id) }}" class="btn btn-success">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                @endif

                                                <form action="{{ url('hapus_transaksi/'.$data->id) }}" method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus nota ini? Stok barang akan dikembalikan.')"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
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

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buat Transaksi Baru</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="buat_transaksi" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="jenis_transaksi">Jenis Transaksi</label>
                                <select class="form-control" id="jenis_transaksi" name="jenis_transaksi">
                                    <option value="umum">Umum</option>
                                    <option value="member">Member</option>
                                    <option value="servis">Servis</option>
                                </select>
                            </div>
                            <div class="form-group" id="nama_member">
                                <label for="id_member">Pilih Member</label>
                                <input type="text" class="form-control" id="nama_member_input" name="nama_member" autocomplete="off">
                                <div id="member_suggestions" class="suggestions-container"></div>
                                <input type="hidden" id="id_member" name="id_member">
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const jenisTransaksi = document.getElementById('jenis_transaksi');
    const namaMemberField = document.getElementById('nama_member');
    const inputMember = document.getElementById('nama_member_input');
    const idMemberHidden = document.getElementById('id_member');
    const suggestionsContainer = document.getElementById('member_suggestions');
    const form = document.querySelector('form[action="buat_transaksi"]');

    // 1. Fungsi Toggle Tampilan & Validasi Required
    function toggleMemberField(value) {
        if (value === 'member') {
            namaMemberField.style.display = 'block';
            inputMember.setAttribute('required', 'required');
        } else {
            namaMemberField.style.display = 'none';
            inputMember.removeAttribute('required');
            // Reset nilai jika pindah ke Umum/Servis
            inputMember.value = '';
            idMemberHidden.value = '';
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
        }
    }

    // Inisialisasi awal
    toggleMemberField(jenisTransaksi.value);

    // Event saat dropdown berubah
    jenisTransaksi.addEventListener('change', function() {
        toggleMemberField(this.value);
    });

    // 2. Fitur Autocomplete Member
    inputMember.addEventListener('input', function() {
        let inputText = this.value;
        if (inputText.length > 0) {
            let xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    let suggestions = JSON.parse(xhr.responseText);
                    suggestionsContainer.innerHTML = '';
                    
                    if(suggestions.length > 0) {
                        suggestions.forEach(function(suggestion) {
                            let option = document.createElement('div');
                            option.textContent = suggestion.nama_member;
                            option.className = 'suggestion-item';
                            option.addEventListener('click', function() {
                                inputMember.value = suggestion.nama_member;
                                idMemberHidden.value = suggestion.id; // Simpan ID asli
                                suggestionsContainer.innerHTML = '';
                                suggestionsContainer.style.display = 'none';
                            });
                            suggestionsContainer.appendChild(option);
                        });
                        suggestionsContainer.style.display = 'block';
                    } else {
                        suggestionsContainer.style.display = 'none';
                    }
                }
            };
            xhr.open('GET', '/search/member?keyword=' + encodeURIComponent(inputText), true);
            xhr.send();
        } else {
            suggestionsContainer.style.display = 'none';
            idMemberHidden.value = '';
        }
    });

    // 3. Validasi Form Sebelum Submit
    // Mencegah lolos jika user mengetik nama tapi tidak klik saran (ID kosong)
    form.addEventListener('submit', function(e) {
        if (jenisTransaksi.value === 'member' && !idMemberHidden.value) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Member Tidak Valid',
                text: 'Silahkan pilih member dari daftar yang muncul!'
            });
        }
    });

    // Sembunyikan saran jika klik di luar area
    document.addEventListener('click', function(e) {
        if (!inputMember.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });
});

// 4. Alert Notifikasi (SweetAlert2)
@if (session()->has('sukses'))
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    Toast.fire({
        icon: 'success',
        title: '{{ session('sukses') }}'
    });
@endif

@if (session()->has('transaksi_sukses'))
    Swal.fire({
        title: "{{ session('transaksi_sukses') }}",
        text: "Apakah anda ingin print nota transaksi ini?",
        icon: "success",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Print",
        cancelButtonText: "Tidak",
    }).then((result) => {
        if (result.isConfirmed) {
            window.open("/nota_" + "{{ session('transaksi_id') }}", '_blank');
        }
    });
@endif
</script>
@endsection