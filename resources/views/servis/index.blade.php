@extends('layouts.app')

@section('link')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection

@section('content')
<div class="content-wrapper">

<section class="content-header">
    <div class="container-fluid">
        <h1>Data Transaksi Servis</h1>
    </div>
</section>

<section class="content">
<div class="card">
<div class="card-body">

<table id="table_servis" class="table table-bordered table-striped">
<thead>
<tr>
    <th>No</th>
    <th>Info Pelanggan</th>
    <th>Unit & Kerusakan</th>
    <th>Status</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody>

@foreach ($data_servis as $index => $data)

@php
    $status = $data->status_servis ?? 'masuk';

    $badges = [
        'masuk' => 'secondary',
        'proses' => 'warning',
        'selesai' => 'success',
        'dibatalkan' => 'danger',
        'diambil' => 'info'
    ];
@endphp

<tr>
    <td>{{ $index + 1 }}</td>

    {{-- CUSTOMER --}}
    <td>
        <strong>{{ $data->nama }}</strong><br>
        <small class="text-muted">{{ $data->nohp }}</small><br>
        <small>{{ $data->kode_servis }}</small>
    </td>

    {{-- DEVICE --}}
    <td>
        <strong>{{ $data->merk }} {{ $data->tipe }}</strong><br>
        <small class="text-danger">Kerusakan: {{ $data->kerusakan }}</small>
    </td>

    {{-- STATUS --}}
    <td class="text-center">
        <span class="badge badge-{{ $badges[$status] ?? 'dark' }} p-2" style="min-width:80px">
            {{ strtoupper($status) }}
        </span>
    </td>

    {{-- ACTION --}}
    <td class="text-center">

        {{-- MASUK → PROSES --}}
        @if($status == 'masuk')
            <button class="btn btn-sm btn-warning text-white btn-update-status"
                data-id="{{ $data->id }}"
                data-status="proses">
                <i class="fas fa-tools"></i> Kerjakan
            </button>

        {{-- PROSES → SELESAI --}}
        @elseif($status == 'proses')
            <button class="btn btn-sm btn-success btn-update-status"
                data-id="{{ $data->id }}"
                data-status="selesai">
                <i class="fas fa-check"></i> Selesai
            </button>

        {{-- SELESAI → BAYAR --}}
        @elseif($status == 'selesai')
            <a href="/pembayaran/servis/{{ $data->id_transaksi }}"
               class="btn btn-sm btn-info">
                <i class="fas fa-money-bill-wave"></i> Bayar
            </a>
        @endif

        {{-- BATAL (AJAX ONLY) --}}
        @if(!in_array($status, ['dibatalkan','diambil']))
            <button class="btn btn-sm btn-outline-danger btn-update-status ml-1"
                data-id="{{ $data->id }}"
                data-status="dibatalkan">
                <i class="fas fa-times"></i>
            </button>
        @endif

    </td>
</tr>

@endforeach

</tbody>
</table>

</div>
</div>
</section>
</div>

{{-- FORM HIDDEN STATUS UPDATE --}}
<form id="form-update-status" action="{{ route('updateStatusServis') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="id" id="input-status-id">
    <input type="hidden" name="status" id="input-status-value">
</form>
@endsection

@section('script')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
$(function () {

    // DATATABLE
    $("#table_servis").DataTable({
        responsive: true,
        autoWidth: false
    });

    // SUCCESS ALERT
    @if(session()->has('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session("success") }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    // =========================
    // STATUS UPDATE (SAFE + FIXED)
    // =========================
    $(document).on('click', '.btn-update-status', function () {

        const btn = $(this);
        const id = btn.data('id');
        const status = btn.data('status');

        Swal.fire({
            title: 'Konfirmasi',
            text: "Ubah status menjadi " + status.toUpperCase(),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'dibatalkan' ? '#d33' : '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, lanjutkan'
        }).then((result) => {

            if (result.isConfirmed) {

                btn.prop('disabled', true);

                $('#input-status-id').val(id);
                $('#input-status-value').val(status);
                $('#form-update-status').submit();
            }
        });
    });

});
</script>
@endsection