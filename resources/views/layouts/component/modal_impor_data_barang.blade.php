<div class="modal fade" id="modal_import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Import</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('import_data_barang') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                    @csrf
                    <div class="form-group">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('import') is-invalid @enderror" id="import" name="import">
                                <label class="custom-file-label" for="import">Pilih file</label>
                            </div>
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">Upload</button>
                            </div>
                        </div>
                        @error('import')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <a href="{{ asset('assets/dist/template/format_data_barang.xlsx') }}">Download Template</a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Masukkan Script SweetAlert2 di bawah ini -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Jika ada error isi data di dalam Excel
        @if(session('error_import'))
            Swal.fire({
                icon: 'error',
                title: 'Import Gagal!',
                html: `{!! session("error_import") !!}`, // Menampilkan daftar baris yang error
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#d33'
            });
        @endif

        // 2. Jika ada error validasi form biasa (misal: file lupa diisi / salah format)
        @error('import')
            Swal.fire({
                icon: 'warning',
                title: 'Periksa File Anda',
                text: '{{ $message }}',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#f8bb86'
            });
        @endif

        // 3. Jika import sukses keseluruhan
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>