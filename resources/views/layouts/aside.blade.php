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

                @php
                    $databaseActive = request()->is('data_barang*') || request()->is('view_edit_data_barang*') || request()->is('data_member*') || request()->is('data_supplier*') || request()->is('data_karyawan*');
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

                <li class="nav-item">
                    <a href="{{ url('/transaksi') }}" class="nav-link {{ request()->is('transaksi*') || request()->is('proses_transaksi*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-handshake"></i>
                        <p>Transaksi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/servis') }}" class="nav-link {{ request()->is('servis*') || request()->is('proses_servis*') || request()->is('pembayaran_servis*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-screwdriver-wrench"></i>
                        <p>Servis</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/arsip') }}" class="nav-link {{ request()->is('arsip*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-book"></i>
                        <p>Arsip</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/setting') }}" class="nav-link {{ request()->is('setting*') ? 'active' : '' }}">
                        <i class="nav-icon fa-solid fa-gears"></i>
                        <p>Setting</p>
                    </a>
                </li>

                
            </ul>
        </nav>
    </div>
</aside>