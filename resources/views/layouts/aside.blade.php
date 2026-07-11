<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/home') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name', 'Angel Cell') }}</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/admin.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->nama }}</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="{{ url('/home') }}" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- Database --}}
                @php
                    $databaseActive = request()->is('data_barang*') || request()->is('view_edit_data_barang*') || request()->is('data_member*') || request()->is('data_supplier*') || request()->is('data_karyawan*') || request()->is('data_kategori*');
                @endphp
                <li class="nav-item {{ $databaseActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $databaseActive ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-server"></i>
                        <p>
                            Database
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('isAdmin')
                            <li class="nav-item">
                                <a href="{{ url('/data_karyawan') }}" class="nav-link {{ request()->is('data_karyawan*') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-users-gear text-sm"></i>
                                    <p>Data Karyawan</p>
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ url('/data_member') }}" class="nav-link {{ request()->is('data_member*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-user-group text-sm"></i>
                                <p>Data Member</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/data_barang') }}" class="nav-link {{ request()->is('data_barang*') || request()->is('view_edit_data_barang*') ? 'active' : '' }}">
                                <i class="nav-icon fa-solid fa-boxes-stacked text-sm"></i>
                                <p>Data Barang</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Transaksi --}}
                <li class="nav-item">
                    <a href="{{ url('/transaksi') }}" class="nav-link {{ request()->is('transaksi*') || request()->is('proses_transaksi*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-handshake"></i>
                        <p>Transaksi</p>
                    </a>
                </li>

                {{-- Servis --}}
                <li class="nav-item">
                    <a href="{{ url('/servis') }}" class="nav-link {{ request()->is('servis*') || request()->is('proses_servis*') || request()->is('pembayaran_servis*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-screwdriver-wrench"></i>
                        <p>Servis</p>
                    </a>
                </li>

                @can('isAdmin')
                    {{-- Arsip --}}
                    <li class="nav-item">
                        <a href="{{ url('/arsip') }}" class="nav-link {{ request()->is('arsip*') ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-book"></i>
                            <p>Arsip</p>
                        </a>
                    </li>

                    {{-- Laporan (treeview) --}}
                    @php
                        $laporanActive = request()->is('laporan*') || request()->is('laporan/penjualan-umum*') || request()->is('laporan/penjualan-member*') || request()->is('laporan/servis*') || request()->is('laporan/pembelian*');
                    @endphp
                    <li class="nav-item {{ $laporanActive ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $laporanActive ? 'active' : '' }}">
                            <i class="nav-icon fa-solid fa-file-invoice"></i>
                            <p>
                                Laporan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('laporan.penjualan_umum') }}" class="nav-link {{ request()->routeIs('laporan.penjualan_umum') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-cart-shopping text-sm"></i>
                                    <p>Penjualan Umum</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('laporan.penjualan_member') }}" class="nav-link {{ request()->routeIs('laporan.penjualan_member') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-user text-sm"></i>
                                    <p>Penjualan Member</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('laporan.servis') }}" class="nav-link {{ request()->routeIs('laporan.servis') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-tools text-sm"></i>
                                    <p>Servis</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('laporan.pembelian') }}" class="nav-link {{ request()->routeIs('laporan.pembelian') ? 'active' : '' }}">
                                    <i class="nav-icon fa-solid fa-truck text-sm"></i>
                                    <p>Pembelian</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                
            </ul>
        </nav>
    </div>
</aside>