<div class="modal fade" id="modal_tambah_data_barang">
    <div class="modal-dialog modal-lg"> <!-- dibesarkan -->
        <div class="modal-content">
            
            <div class="modal-header ">
                <h5 class="modal-title">Form Tambah Data Barang</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tambah_data_barang') }}">
                    @csrf
                    @method('put')

                    <input type="hidden" name="id_toko" value="1">
                    <input type="hidden" name="id_supplier" value="1">

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Barcode</label>
                        <div class="col-sm-8">
                            <input type="text" name="barcode"
                                class="form-control @error('barcode') is-invalid @enderror"
                                value="{{ old('barcode') }}">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Nama Barang</label>
                        <div class="col-sm-8">
                            <input type="text" name="nama_barang"
                                class="form-control @error('nama_barang') is-invalid @enderror"
                                value="{{ old('nama_barang') }}">
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Qty</label>
                        <div class="col-sm-8">
                            <input type="number" name="qty"
                                class="form-control @error('qty') is-invalid @enderror"
                                value="{{ old('qty') }}">
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga Modal</label>
                        <div class="col-sm-8">
                            <input type="number" name="harga_modal"
                                class="form-control @error('harga_modal') is-invalid @enderror"
                                value="{{ old('harga_modal') }}">
                            @error('harga_modal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga Umum</label>
                        <div class="col-sm-8">
                            <input type="number" name="harga_umum"
                                class="form-control @error('harga_umum') is-invalid @enderror"
                                value="{{ old('harga_umum') }}">
                            @error('harga_umum')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Harga Member</label>
                        <div class="col-sm-8">
                            <input type="number" name="harga_member"
                                class="form-control @error('harga_member') is-invalid @enderror"
                                value="{{ old('harga_member') }}">
                            @error('harga_member')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>