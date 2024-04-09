
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
<!-- Brand Logo -->
<a href="#" class="brand-link">
    <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">{{ config('app.name', 'Laravel') }}</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
        <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
    </div>
    <div class="info">
        <a href="#" class="d-block">{{ Auth::user()->nama }}</a>
    </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
        <a href="home" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
            Dashboard
            </p>
        </a>
        </li>
        <li class="nav-item menu-open">
        <a href="#" class="nav-link {{ request()->is('data_barang') ? 'active' : '' }} {{ request()->is('view_edit_data_barang_*') ? 'active' : '' }} {{ request()->is('data_member') ? 'active' : '' }} {{ request()->is('data_supplier') ? 'active' : '' }} {{ request()->is('data_karyawan') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-server"></i>
            <p>
            Database
            <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            {{-- <li class="nav-item">
            <a href="data_karyawan" class="nav-link {{ request()->is('data_karyawan') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-id-card"></i>
            <p>
            Data Karyawan
            </p>
            </a>
            </li> --}}
            {{-- <li class="nav-item">
            <a href="data_supplier" class="nav-link {{ request()->is('data_supplier') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-truck-fast"></i>
            <p>
            Data Supplier
            </p>
            </a>
            </li> --}}
            <li class="nav-item">
            <a href="data_member" class="nav-link {{ request()->is('data_member') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-user-group"></i>
            <p>
            Data Member
            </p>
            </a>
            </li>
            <li class="nav-item">
            <a href="data_barang" class="nav-link {{ request()->is('data_barang') ? 'active' : '' }} {{ request()->is('view_edit_data_barang_*') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-boxes-stacked"></i>
            <p>
            Data Barang
            </p>
            </a>
            </li>
        </ul>
        </li>
        <li class="nav-item">
        <a href="transaksi" class="nav-link {{ request()->is('transaksi') ? 'active' : '' }} {{ request()->is('proses_transaksi_*') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-handshake"></i>
            <p>
            Transaksi
            </p>
        </a>
        </li>
        <li class="nav-item">
        <a href="riwayat_transaksi" class="nav-link {{ request()->is('riwayat_transaksi') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-file-invoice-dollar"></i>
            <p>
            Riwayat Transaksi
            </p>
        </a>
        </li>
        <li class="nav-item">
        <a href="laporan" class="nav-link {{ request()->is('laporan') ? 'active' : '' }} {{ request()->is('laporan_filter') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-book"></i>
            <p>
            Laporan
            </p>
        </a>
        </li>
        <li class="nav-item">
        <a href="setting" class="nav-link {{ request()->is('setting') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-gears"></i>
            <p>
            Setting
            </p>
        </a>
        </li>
        
    </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
</aside>