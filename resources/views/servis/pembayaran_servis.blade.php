@extends('layouts.app')

@section('content')
<div class="content-wrapper">

<div class="content-header">
    <div class="container-fluid">
        <h1>Pembayaran Servis</h1>
    </div>
</div>

<section class="content">
<div class="container-fluid">
<div class="row">

{{-- ================= KIRI (INFORMASI + TOTAL) ================= --}}
<div class="col-lg-6">

<div class="card card-primary card-outline">
<div class="card-body">

    <h5><b>ID Transaksi:</b> {{ $transaksi->id }}</h5>
    <h5><b>Kasir:</b> {{ auth()->user()->nama }}</h5>

    <hr>

    {{-- ================= DATA SERVIS ================= --}}
    @if($servis)
        <h5><b>Servis:</b></h5>
        <p>
            {{ $servis->kode_servis }} <br>
            {{ $servis->nama }} <br>
            {{ $servis->merk }} {{ $servis->tipe }}
        </p>

        <h5 class="text-primary">
            Estimasi Servis: @rp($total_servis)
        </h5>
    @endif

    <hr>

    {{-- ================= TOTAL KERANJANG ================= --}}
    <h5>
        Tambahan Barang: @rp($total_keranjang)
    </h5>

    <hr>

    {{-- ================= GRAND TOTAL ================= --}}
    <h3 class="text-success">
        Grand Total: @rp($total)
    </h3>

    <hr>

    {{-- ================= PEMBAYARAN ================= --}}
    <label>Bayar</label>
    <input type="text" id="bayar" class="form-control mb-2">

    <h5>
        Kembalian: <span id="kembalian">0</span>
    </h5>

</div>
</div>

</div>

{{-- ================= KANAN (KERANJANG TAMBAHAN) ================= --}}
<div class="col-lg-6">

<div class="card card-primary card-outline">
<div class="card-header">
    <h5>Tambahan Barang</h5>
</div>

<div class="card-body">

{{-- FORM MANUAL --}}
<form action="/tambah_manual" method="POST">
@csrf
<input type="hidden" name="id_transaksi" value="{{ $transaksi->id }}">

<div class="row">
    <div class="col-md-5">
        <input type="text" name="nama" class="form-control" placeholder="Nama">
    </div>
    <div class="col-md-3">
        <input type="number" name="harga" class="form-control" placeholder="Harga">
    </div>
    <div class="col-md-2">
        <input type="number" name="qty" class="form-control" value="1">
    </div>
    <div class="col-md-2">
        <button class="btn btn-success btn-block">+</button>
    </div>
</div>

</form>

<hr>

{{-- TABLE KERANJANG --}}
<table class="table table-bordered">
<thead>
<tr>
    <th>Nama</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
@foreach($keranjang as $item)
<tr>
    <td>{{ $item->nama }}</td>
    <td>@rp($item->harga)</td>
    <td>{{ $item->qty }}</td>
    <td>@rp($item->subtotal)</td>
</tr>
@endforeach
</tbody>
</table>

</div>
</div>

</div>

</div>
</div>
</section>

</div>
@endsection