@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Pembayaran Servis</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5>Administrasi</h5>
                        </div>
                        <div class="card-body">
                            <h6><b>ID Transaksi:</b> {{ $transaksi->id }}</h6>
                            <h6><b>Kasir:</b> {{ auth()->user()->nama }}</h6>
                            <h6><b>Tanggal:</b> {{ \Carbon\Carbon::now()->format('d-m-Y') }}</h6>
                            @if($nama_member)
                                <h6><b>Member:</b> {{ $nama_member }}</h6>
                            @endif
                            <hr>
                            @if(isset($detail_servis))
                                <h6><b>Data Servis:</b></h6>
                                <p>
                                    Kode: {{ $detail_servis->kode_servis }} <br>
                                    Pelanggan: {{ $detail_servis->nama }} <br>
                                    Unit: {{ $detail_servis->merk }} - {{ $detail_servis->tipe }}
                                </p>
                            @endif
                            <hr>
                            <h4 class="text-primary"><b>Grand Total:</b> @rp($total_keranjang)</h4>
                            <h6><b>Kembalian:</b> <span id="kembalian" class="text-danger">Rp 0</span></h6>
                            <div class="form-group mt-3">
                                <label><b>Bayar (Nominal)</b></label>
                                <input type="number" id="bayar" class="form-control form-control-lg" placeholder="0">
                            </div>
                        </div>
                        <div class="card-footer">
                            @if($total_keranjang > 0)
                            <form action="/selesaikan_servis" method="POST" id="formTransaksi">
                                @csrf
                                <input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">
                                <input type="hidden" name="bayar" id="inputBayar">
                                <input type="hidden" name="kembalian" id="inputKembalian">
                                <button type="button" class="btn btn-success btn-block konfirmasi">
                                    <i class="fas fa-check-circle"></i> Selesaikan Transaksi
                                </button>
                            </form>
                            @else
                            <button class="btn btn-secondary btn-block" disabled>Keranjang kosong</button>
                            @endif
                        </div>
                    </div>
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5>Keranjang (Barang & Jasa)</h5>
                        </div>

                        <div class="card-body">
                            <input type="text" id="scanner" class="form-control mb-3" placeholder="Scan barcode di sini...">

                            <form action="/tambah_manual" method="POST">
                                @csrf
                                <input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label class="small mb-1">Nama Jasa / Item</label>
                                        <input type="text" name="nama" class="form-control" placeholder="Contoh: : LCD" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small mb-1">Harga Modal</label>
                                        <input type="number" name="harga_modal" class="form-control" placeholder="0" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small mb-1">Harga Jual</label>
                                        <input type="number" name="harga_jual" class="form-control" placeholder="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small mb-1">Qty</label>
                                        <input type="number" name="qty" class="form-control" value="1" min="1">
                                    </div>
                                    <div class="col-md-1">
                                        <div class="d-grid">
                                            <button class="btn btn-success" type="submit">
                                                <i class="fa fa-plus me-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="table-responsive">
                                <table id="table_keranjang" class="table table-sm table-bordered">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($keranjang as $k => $item)
                                        <tr>
                                            <td>{{ $k+1 }}</td>
                                            <td>
                                                {{ $item->nama }}
                                                @if(is_null($item->id_barang))
                                                    <span class="badge badge-warning">Manual</span>
                                                @endif
                                            </td>
                                            <td>@rp($item->harga_jual)</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>@rp($item->harga_jual * $item->qty)</td>
                                            <td class="text-center">
                                                <form action="/hapus_keranjang_{{ $item->id }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                    <h5 class="m-0">Pilih Barang</h5>
                    </div>
                    <div class="card-body">
                        <table id="table_data_barang" class="table table-bordered table-striped">
                            <thead>
                                <tr style="text-align: center">
                                    <th data-orderable="false">No</th>
                                    <th>Nama</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th data-orderable="false">Tambah Ke Keranjang</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        $('#scanner').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let barcodeValue = $(this).val();

                if (barcodeValue) {
                    $.post('/scan-barang', {
                        barcode: barcodeValue,
                        id_transaksi: '{{ $transaksi->id }}'
                    }, function() {
                        location.reload();
                    }).fail(function() {
                        Swal.fire('Error', 'Barang tidak ditemukan atau stok habis', 'error');
                    });
                }
            }
        });
        $('#bayar').on('input', function() {
            let total = {{ $total_keranjang }};
            let bayar = parseInt($(this).val()) || 0;
            let kembali = bayar - total;
            $('#kembalian').text('Rp ' + kembali.toLocaleString('id-ID'));
            $('#inputBayar').val(bayar);
            $('#inputKembalian').val(kembali);
        });
        $('.konfirmasi').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let bayar = parseInt($('#inputBayar').val()) || 0;
            let total = {{ $total_keranjang }};

            if (bayar < total) {
                Swal.fire('Pembayaran Kurang!!', 'Uang yang dibayarkan kurang. JANGAN HUTANG !', 'warning');
                return;
            }

            Swal.fire({
                title: 'Selesaikan Transaksi?',
                text: "Pastikan uang pembayaran sudah diterima",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesaikan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        $('#table_data_barang').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('data-barang') }}',
                type: 'GET'
            },
            columns: [
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'nama', name: 'nama' },
                { data: 'qty', name: 'qty' },
                { 
                    data: '{{ $transaksi->jenis_transaksi == "member" ? "harga_member" : "harga_umum" }}', 
                    render: $.fn.dataTable.render.number('.', ',', 0, 'Rp ') 
                },
                { 
                    data: 'action', 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, full, meta) {
                        return `
                            <form method="post" action="/tambah_keranjang">
                                @csrf
                                <input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">
                                <input type="hidden" name="id_member" value="{{ $transaksi->id_member }}">
                                <input type="hidden" name="id" value="${full.id}">
                                <div class="input-group input-group-sm">
                                    <input class="form-control" type="number" name="jumlah" min="1" value="1">
                                    <div class="input-group-append">
                                        <button class="btn btn-info" type="submit">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>`;
                    }
                }
            ]
        });
    });
</script>
@endsection