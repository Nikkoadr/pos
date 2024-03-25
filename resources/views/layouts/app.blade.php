<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name'); }}</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@yield('link')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
@include('layouts/navbar')

@include('layouts/aside')

@yield('content')

<footer class="main-footer">
<div class="float-right d-none d-sm-block">
    <b>Version</b> 3.2.0
</div>
<strong>Copyright &copy; 2014-2021 <a href="https://github.com">Nikko Adrian</a>.</strong> All rights reserved.
</footer>
</div>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@yield('script')
</body>
</html>