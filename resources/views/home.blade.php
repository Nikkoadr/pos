@extends('layouts.app')
@section('link')
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
@endsection

@section('preloader')
<div class="preloader flex-column justify-content-center align-items-center">
<img class="animation__shake" src="{{ asset('assets/img/logo.png') }}" alt="AdminLTELogo" height="80" width="60">
</div>
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Dashboard</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
        </div>
    </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
            <h3>@rp($pendapatan)</h3>

            <p>Pendapatan Hari Ini</p>
            </div>
            <div class="icon">
            <i class="ion ion-calculator"></i>
            </div>
            <a href="laporan" class="small-box-footer">Lihat lebih <i class="fas fa-arrow-circle-right"></i></a>
        </div>
        </div>
        
        </div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
