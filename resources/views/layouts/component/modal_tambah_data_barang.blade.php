    <div class="modal fade" id="modal_tambah_data_barang">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Form Tambah Data barang</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
                <form method="POST" action="/tambah_data_barang">
                        @csrf
                        @method('put')
                        <input type="hidden" name="id_toko" value="1">
                        <input type="hidden" name="id_supplier" value="1">
                        <div class="row mb-3">
                            <label for="nama_barang" class="col-sm-5 col-form-label text-md-end">Nama Barang :</label>
                            <div class="col-sm-7">
                                <input id="nik" type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang')}}" autocomplete="nama_barang" autofocus>
                                @error('nama_barang')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="qty" class="col-sm-5 col-form-label text-md-end">Jumlah Stok :</label>
                            <div class="col-sm-7">
                                <input id="qty" type="number" class="form-control @error('qty') is-invalid @enderror" name="qty" value="{{ old('qty')}}" autocomplete="qty" autofocus>
                                @error('qty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="harga_modal" class="col-sm-5 col-form-label text-md-end">Harga Modal :</label>
                            <div class="col-sm-7">
                                <input id="harga_modal" type="number" class="form-control @error('harga_modal') is-invalid @enderror" name="harga_modal" value="{{ old('harga_modal')}}" autocomplete="harga_modal" autofocus>
                                @error('harga_modal')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="harga_umum" class="col-sm-5 col-form-label text-md-end">Harga Umum :</label>
                            <div class="col-sm-7">
                                <input id="harga_umum" type="number" class="form-control @error('harga_umum') is-invalid @enderror" name="harga_umum" value="{{ old('harga_umum')}}" autocomplete="harga_umum" autofocus>
                                @error('harga_umum')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="harga_grosir" class="col-sm-5 col-form-label text-md-end">Harga Grosir :</label>
                            <div class="col-sm-7">
                                <input id="harga_grosir" type="number" class="form-control @error('harga_grosir') is-invalid @enderror" name="harga_grosir" value="{{ old('harga_grosir')}}" autocomplete="harga_grosir" autofocus>
                                @error('harga_grosir')
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