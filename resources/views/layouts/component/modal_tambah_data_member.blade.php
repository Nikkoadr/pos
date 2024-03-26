    <div class="modal fade" id="modal_tambah_data_member">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Form Tambah Data Member</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
                <form method="POST" action="/tambah_data_member">
                        @csrf
                        <input type="hidden" name="id_toko" value="1">
                        <div class="row mb-3">
                            <label for="nama_member" class="col-sm-5 col-form-label text-md-end">Nama Member :</label>
                            <div class="col-sm-7">
                                <input id="nama_member" type="text" class="form-control @error('nama_member') is-invalid @enderror" name="nama_member" value="{{ old('nama_member')}}" autocomplete="nama_member" autofocus>
                                @error('nama_member')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="nomor_hp" class="col-sm-5 col-form-label text-md-end">Nomor Hp :</label>
                            <div class="col-sm-7">
                                <input id="nomor_hp" type="text" class="form-control @error('nomor_hp') is-invalid @enderror" name="nomor_hp" value="{{ old('nomor_hp')}}" autocomplete="nomor_hp" autofocus>
                                @error('nomor_hp')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="alamat" class="col-sm-5 col-form-label text-md-end">Alamat :</label>
                            <div class="col-sm-7">
                                <input id="alamat" type="text" class="form-control @error('alamat') is-invalid @enderror" name="alamat" value="{{ old('alamat')}}" autocomplete="alamat" autofocus>
                                @error('alamat')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                                <button style="float: right;" type="submit" class="btn btn-primary">
                                    Tambah
                                </button>
                    </form>
        </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->