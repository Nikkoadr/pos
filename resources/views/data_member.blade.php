@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Data Member</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Data Member</li>
        </ol>
        </div>
    </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="card">
    <div class="card-header">
            <button type="button" class="btn btn-primary m-1" data-toggle="modal" data-target="#modal_import"><i class="fa-solid fa-file-import"></i> Import</button>
            @include('layouts.component.modal_impor_data_barang')
            {{-- <a href="exportuser" class="btn btn-info m-1" target="_blank"><i class="fa-solid fa-file-export"></i> Export</a> --}}
            <button type="button" class="btn btn-success m-1" data-toggle="modal" data-target="#modal_tambah_data_member"><i class="fa-solid fa-user-plus"></i> Tambah</button>
            @include('layouts.component.modal_tambah_data_member')
        </div>
            <div class="card-body">
            <table id="table_data_member" class="table table-bordered table-striped">
                <thead>
                <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Nomor HP</th>
                <th>Alamat</th>
                <th style="text-align: center" data-orderable="false">Menu</th>
                </tr>
                </thead>
                <tbody>
                <?php $no=1; ?>
                @foreach ($data_member as $data )
                <tr>
                    <td><?= $no++ ?></td>
                    <td>{{ $data -> nama_member }}</td>
                    <td>{{ $data -> nomor_hp }}</td>
                    <td>{{ $data -> alamat }}</td>
                    <td style="text-align: center">
                        <a href="view_edit_data_member_{{ $data->id }}" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i></i></a>
                        <a href="hapus_data_member_{{ $data->id }}" class="btn btn-danger konfirmasi m-1"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
    <!-- /.card-body -->
    </div>
    <!-- /.card -->

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
