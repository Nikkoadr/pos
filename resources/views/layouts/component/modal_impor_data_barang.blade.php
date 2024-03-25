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
            <form action="/import_data_barang" method="POST" enctype="multipart/form-data" class="form-horizontal">
                @csrf
                <div class="form-group">
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
                    <a href="{{ asset('assets/dist/template/databarang.xlsx') }}">Download Template</a>
                </div>
                </form>
        </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->