{{-- Sidebar Start --}}
<aside class="left-sidebar">
    {{-- Sidebar scroll --}}
    <div class="scroll-sidebar" data-simplebar>
        <div class="d-flex align-items-center justify-content-center">
            <a href="{{ route('dashboard.index') }}" class="text-nowrap logo-img ms-0 ms-md-1">
                <img src="/backend/assets/images/logos/logo.png" width="100" alt="">
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        {{-- Sidebar navigation --}}
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="mb-4 pb-2">

                {{-- MENU UTAMA (SAMA UNTUK SEMUA) --}}
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="{{ route('dashboard.index') }}"
                        aria-expanded="false">
                        <span class="aside-icon p-2 bg-light-primary rounded-3">
                            <i class="ti ti-layout-dashboard fs-7 text-primary"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Dashboard</span>
                    </a>
                </li>

                {{-- =============================================== --}}
                {{-- |             MENU KHUSUS ADMIN             | --}}
                {{-- =============================================== --}}
                @if (Auth::user()->role == 'admin')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
                        <span class="hide-menu">Master Data</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link sidebar-link warning-hover-bg" href="{{ route('meja_billiard.index') }}"
                            aria-expanded="false">
                            <span class="aside-icon p-2 bg-light-warning rounded-3">
                                <i class="ti ti-article fs-7 text-warning"></i>
                            </span>
                            <span class="hide-menu ms-2 ps-1">Kelola Meja Billiard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link sidebar-link danger-hover-bg" href="{{ route('pelanggan.index') }}"
                            aria-expanded="false">
                            <span class="aside-icon p-2 bg-light-danger rounded-3">
                                <i class="ti ti-users fs-7 text-danger"></i>
                            </span>
                            <span class="hide-menu ms-2 ps-1">Data Pelanggan</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
                        <span class="hide-menu">Transaksi</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link sidebar-link success-hover-bg" href="{{ route('admin.booking.index') }}"
                            aria-expanded="false">
                            <span class="aside-icon p-2 bg-light-success rounded-3">
                                <i class="ti ti-clipboard-text fs-7 text-success"></i>
                            </span>
                            <span class="hide-menu ms-2 ps-1">Data Booking</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link sidebar-link indigo-hover-bg" href="{{ route('settings.index') }}"
                            aria-expanded="false">
                            <span class="aside-icon p-2 bg-light-indigo rounded-3">
                                <i class="ti ti-settings fs-7 text-indigo"></i>
                            </span>
                            <span class="hide-menu ms-2 ps-1">Pengaturan</span>
                        </a>
                    </li>
                @endif


                {{-- =============================================== --}}
                {{-- |           MENU KHUSUS PELANGGAN           | --}}
                {{-- =============================================== --}}
                @if (Auth::user()->role == 'pelanggan')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
                        <span class="hide-menu">Menu</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link sidebar-link warning-hover-bg" href="{{ route('booking_meja.index') }}"
                            aria-expanded="false">
                            <span class="aside-icon p-2 bg-light-warning rounded-3">
                                <i class="ti ti-table fs-7 text-warning"></i>
                            </span>
                            <span class="hide-menu ms-2 ps-1">Booking Meja</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link sidebar-link success-hover-bg" href="{{ route('booking.history') }}"
                            aria-expanded="false">
                            <span class="aside-icon p-2 bg-light-success rounded-3">
                                <i class="ti ti-history fs-7 text-success"></i>
                            </span>
                            <span class="hide-menu ms-2 ps-1">Riwayat Booking Saya</span>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
        {{-- End Sidebar navigation --}}
    </div>
    {{-- End Sidebar scroll --}}
</aside>
{{-- Sidebar End --}}
