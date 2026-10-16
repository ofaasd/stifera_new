@php
    $isImpersonasi = session()->has('impersonasi_admin');
@endphp

<style>
    /* ── Anti-Cache Inline Styles for Sidebar ── */
    .deznav, .deznav-scroll, [data-sidebar-style="overlay"] .deznav {
        background-color: #0057bb !important;
    }
    .deznav a, .deznav span, .deznav .nav-text, .deznav .metismenu a, .deznav .metismenu li a,
    [data-sidebar-style="overlay"] .deznav a, [data-sidebar-style="overlay"] .deznav .nav-text,
    [data-sidebar-style="overlay"] .deznav .metismenu > li > a,
    [data-sidebar-style="overlay"] .deznav .metismenu ul a {
        color: #ffffff !important;
    }
    .deznav i, .deznav .menu-icon, .deznav .menu-icon i,
    [data-sidebar-style="overlay"] .deznav i, [data-sidebar-style="overlay"] .deznav .menu-icon i {
        color: #ffffff !important;
    }
    .deznav svg path, .deznav g [fill],
    [data-sidebar-style="overlay"] .deznav svg path, [data-sidebar-style="overlay"] .deznav g [fill] {
        fill: #ffffff !important;
    }
    /* ── PENANDA MENU PARENT DENGAN SUBMENU (CHEVRON DROPDOWN INDICATOR) ── */
    .deznav .metismenu .has-arrow, .deznav .has-arrow, [data-sidebar-style="overlay"] .deznav .metismenu .has-arrow {
        position: relative !important;
        padding-right: 38px !important;
    }
    .deznav .metismenu .has-arrow:after, .deznav .has-arrow:after, [data-sidebar-style="overlay"] .deznav .metismenu .has-arrow:after {
        content: "" !important;
        display: inline-block !important;
        position: absolute !important;
        right: 18px !important;
        top: 50% !important;
        width: 8px !important;
        height: 8px !important;
        border-style: solid !important;
        border-width: 0 2px 2px 0 !important;
        border-color: #ffffff !important;
        opacity: 0.85 !important;
        transform: translateY(-50%) rotate(-45deg) !important;
        -webkit-transform: translateY(-50%) rotate(-45deg) !important;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease !important;
        -webkit-transition: -webkit-transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease !important;
        pointer-events: none !important;
    }
    .deznav .metismenu .has-arrow[aria-expanded="true"]:after,
    .deznav .metismenu li.mm-active > a.has-arrow:after,
    [data-sidebar-style="overlay"] .deznav .metismenu .has-arrow[aria-expanded="true"]:after,
    [data-sidebar-style="overlay"] .deznav .metismenu li.mm-active > a.has-arrow:after {
        transform: translateY(-50%) rotate(45deg) !important;
        -webkit-transform: translateY(-50%) rotate(45deg) !important;
        opacity: 1 !important;
        border-color: #ffffff !important;
    }
    .deznav .metismenu ul, [data-sidebar-style="overlay"] .deznav .metismenu ul, .deznav .metismenu li > ul {
        background-color: rgba(0, 0, 0, 0.22) !important;
        border-left: none !important;
        border: none !important;
    }
    /* ── HILANGKAN SEMUA ANIMASI GARIS MEMANJANG (HORIZONTAL & VERTIKAL) ── */
    .deznav a:before,
    .deznav a:hover:before,
    .deznav .metismenu a:before,
    .deznav .metismenu a:hover:before,
    .deznav .metismenu li a:before,
    .deznav .metismenu li a:hover:before,
    .deznav .metismenu > li > a:before,
    .deznav .metismenu > li:hover > a:before,
    .deznav .metismenu > li.mm-active > a:before,
    .deznav .metismenu ul a:before,
    .deznav .metismenu ul a:after,
    .deznav .metismenu ul a:hover:before,
    .deznav .metismenu ul a:hover:after,
    .deznav .metismenu ul li a:before,
    .deznav .metismenu ul li a:after,
    .deznav .metismenu ul li a:hover:before,
    .deznav .metismenu ul li a:hover:after,
    [data-sidebar-style="full"] .deznav .metismenu ul a:before,
    [data-sidebar-style="full"] .deznav .metismenu ul a:hover:before,
    [data-sidebar-style="overlay"] .deznav .metismenu ul a:before,
    [data-sidebar-style="overlay"] .deznav .metismenu ul a:hover:before,
    [data-sidebar-style="mini"] .deznav .metismenu ul a:before,
    [data-sidebar-style="mini"] .deznav .metismenu ul a:hover:before,
    [data-sidebar-style="compact"] .deznav .metismenu ul a:before,
    [data-sidebar-style="compact"] .deznav .metismenu ul a:hover:before {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
        transition: none !important;
        -webkit-transition: none !important;
        animation: none !important;
    }
    .deznav .metismenu ul a,
    .deznav .metismenu ul a:hover,
    [data-sidebar-style="full"] .deznav .metismenu ul a,
    [data-sidebar-style="full"] .deznav .metismenu ul a:hover,
    [data-sidebar-style="overlay"] .deznav .metismenu ul a,
    [data-sidebar-style="overlay"] .deznav .metismenu ul a:hover {
        padding-left: 20px !important;
        transition: background-color 0.2s ease, color 0.2s ease !important;
    }
    @media (max-width: 991.98px) {
        .deznav, [data-sidebar-style="overlay"] .deznav {
            position: fixed !important;
            top: 0 !important;
            left: -330px !important;
            width: 290px !important;
            max-width: 85vw !important;
            height: 100vh !important;
            z-index: 99999 !important;
            background-color: #0057bb !important;
            box-shadow: 10px 0 35px rgba(0, 0, 0, 0.4) !important;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: block !important;
        }
        #main-wrapper.menu-toggle .deznav, body.menu-toggle .deznav, .menu-toggle .deznav,
        [data-sidebar-style="overlay"] #main-wrapper.menu-toggle .deznav {
            left: 0 !important;
        }
    }
</style>
<div class="deznav">
    <!-- Mobile Header with Close Button -->
    <div class="sidebar-mobile-header d-md-none">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" style="height: 30px; width: auto; object-fit: contain;" onerror="this.src='{{ asset('images/favicon.png') }}'">
            <span class="brand-text">SIAKAD STIFERA</span>
        </div>
        <button type="button" class="sidebar-mobile-close-btn" aria-label="Tutup Menu">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <div class="deznav-scroll grid-menu">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{ route('mahasiswa.home') }}" aria-expanded="false">
                    <div class="menu-icon">
                        <i class="fa fa-home"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            

            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <div class="menu-icon">
                        <i class="feather icon-layers"></i>
                    </div>
                    <span class="nav-text">Akademik</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('mhs/krs') }}">Kartu Rencana Studi</a></li>
                    <li><a href="{{ url('mhs/khs') }}">Kartu Hasil Study</a></li>
                    <li><a href="{{ url('mhs/ujian') }}">Kartu Ujian</a></li>
                    <li><a href="{{ url('mhs/daftar_nilai') }}">Daftar Nilai</a></li>
                    <li><a href="{{ url('mhs/matakuliah') }}">Mata kuliah</a></li>
                    <li><a href="{{ url('mhs/keuangan') }}">Keuangan</a></li>
                    <li><a href="{{ route('mahasiswa.yudisium.index') }}">Pengajuan Yudisium</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ url('mhs/presensi') }}" aria-expanded="false">
                    <div class="menu-icon">
                        <i class="fa fa-key"></i>
                    </div>
                    <span class="nav-text">Presensi</span>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <div class="menu-icon">
                        <i class="feather icon-user"></i>
                    </div>
                    <span class="nav-text">Biodata</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('mahasiswa.profile.edit') }}">Profile</a></li>
                    <li><a href="{{ url('mhs/dashboard/ganti_password') }}">Ganti Password</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ url('mhs/input_krs') }}" aria-expanded="false">
                    <div class="menu-icon">
                        <i class="feather icon-layers"></i>
                    </div>
                    <span class="nav-text">Input KRS</span>
                </a>
            </li>

            <li>
                <a href="{{ url('mhs/masukan') }}" aria-expanded="false">
                    <div class="menu-icon">
                        <i class="feather icon-layers"></i>
                    </div>
                    <span class="nav-text">Kritik &amp; Saran</span>
                </a>
            </li>

            @if($isImpersonasi)
                <li>
                    <a href="javascript:void(0);" aria-expanded="false" onclick="event.preventDefault(); document.getElementById('impersonasi-stop-form').submit();">
                        <div class="menu-icon">
                            <i class="feather icon-log-out"></i>
                        </div>
                        <span class="nav-text">Kembali Ke Admin</span>
                    </a>
                </li>
            @else
                <li>
                    <a href="javascript:void(0);" aria-expanded="false" onclick="event.preventDefault(); document.getElementById('mahasiswa-logout-form').submit();">
                        <div class="menu-icon">
                            <i class="feather icon-log-out"></i>
                        </div>
                        <span class="nav-text">Logout</span>
                    </a>
                </li>
            @endif
        </ul>

        <form id="mahasiswa-logout-form" action="{{ route('mahasiswa.logout') }}" method="POST" class="d-none">
            @csrf
        </form>

        <form id="impersonasi-stop-form" action="{{ route('mahasiswa.impersonasi.stop') }}" method="POST" class="d-none">
            @csrf
        </form>

        <div class="mode-btn d-flex align-items-center justify-content-between">
            <div class="d-mode">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_4_82)">
                        <path
                            d="M12.025 23.3407L8.62955 20.0479H3.95118V15.3728L0.584229 12L3.95208 8.62704V3.94519H8.6272L12.025 0.572266L15.3731 3.94497H20.055V8.62694L23.4277 12L20.0549 15.3704V20.0488H15.3728L12.025 23.3407ZM12.025 18.3445C13.7812 18.3445 15.2745 17.7251 16.5049 16.4863C17.7353 15.2474 18.3506 13.7439 18.3506 11.9757C18.3506 10.2214 17.7348 8.72844 16.5034 7.49684C15.2719 6.26524 13.7791 5.64944 12.025 5.64944V18.3445ZM12.025 20.9538L14.6609 18.347H18.3513V14.6568L21.0098 12L18.3493 9.33697V5.64874H14.6645L12.025 2.99022L9.34323 5.64874H5.65298V9.33547L2.9962 12L5.65545 14.6592V18.3445H9.31575L12.025 20.9538Z"
                            fill="#6F767E" />
                    </g>
                    <defs>
                        <clipPath id="clip0_4_82">
                            <rect width="24" height="24" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
                <span class="ms-2">Dark Mode</span>
            </div>
            <div class="dz-layout light">
                <i class="fas fa-sun sun"></i>
                <i class="fas fa-moon moon"></i>
            </div>
        </div>
    </div>
</div>
