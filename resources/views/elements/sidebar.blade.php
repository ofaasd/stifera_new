<style>
    /* Memperbaiki bug white-on-white text saat sidebar di toggle berbagai mode */
    #main-wrapper.menu-toggle .deznav .metismenu li > ul,
    body.menu-toggle .deznav .metismenu li > ul,
    .menu-toggle .deznav .metismenu li > ul,
    [data-sidebar-style="mini"] .deznav .metismenu li > ul,
    [data-sidebar-style="compact"] .deznav .metismenu li > ul,
    [data-sidebar-style="icon-hover"] .deznav .metismenu li > ul {
        background-color: #0057bb !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }
    
    #main-wrapper.menu-toggle .deznav .metismenu li:hover > a > .nav-text,
    body.menu-toggle .deznav .metismenu li:hover > a > .nav-text,
    .menu-toggle .deznav .metismenu li:hover > a > .nav-text,
    [data-sidebar-style="mini"] .deznav .metismenu li:hover > a > .nav-text,
    [data-sidebar-style="compact"] .deznav .metismenu li:hover > a > .nav-text,
    [data-sidebar-style="icon-hover"] .deznav .metismenu li:hover > a > .nav-text {
        background-color: #0057bb !important;
        color: #fff !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
    }
    
    #main-wrapper.menu-toggle .deznav .metismenu li > ul a,
    body.menu-toggle .deznav .metismenu li > ul a,
    .menu-toggle .deznav .metismenu li > ul a,
    [data-sidebar-style="mini"] .deznav .metismenu li > ul a,
    [data-sidebar-style="compact"] .deznav .metismenu li > ul a,
    [data-sidebar-style="icon-hover"] .deznav .metismenu li > ul a {
        color: #fff !important;
    }
    
    #main-wrapper.menu-toggle .deznav .metismenu li > ul a:hover,
    body.menu-toggle .deznav .metismenu li > ul a:hover,
    .menu-toggle .deznav .metismenu li > ul a:hover,
    [data-sidebar-style="mini"] .deznav .metismenu li > ul a:hover,
    [data-sidebar-style="compact"] .deznav .metismenu li > ul a:hover,
    [data-sidebar-style="icon-hover"] .deznav .metismenu li > ul a:hover {
        background-color: rgba(255, 255, 255, 0.15) !important;
    }
</style>
<div class="deznav">
			<div class="deznav-scroll grid-menu">
				<ul class="metismenu" id="menu">
				@foreach($menus as $menu)
					@php
						$menuName = strtolower(trim((string) ($menu->nama_menu ?? '')));
						$isKepegawaianMenu = $menuName === 'kepegawaian';
						$hasJamKerjaDosen = $menu->children->contains(function ($item) {
							$url = trim(strtolower((string) ($item->url ?? '')));
							$name = trim(strtolower((string) ($item->nama_menu ?? '')));

							return $url === 'simpeg/absensi/jam_kerja_master' || $name === 'jam kerja dosen';
						});
						$hasSuratIzin = $menu->children->contains(function ($item) {
							$url = trim(strtolower((string) ($item->url ?? '')));
							$name = trim(strtolower((string) ($item->nama_menu ?? '')));

							return $url === 'simpeg/suratizin2' || $name === 'surat izin';
						});
						$hasMeninggalkanPekerjaan = $menu->children->contains(function ($item) {
							$url = trim(strtolower((string) ($item->url ?? '')));
							$name = trim(strtolower((string) ($item->nama_menu ?? '')));

							return $url === 'simpeg/meninggalkanpekerjaan' || $name === 'izin meninggalkan pekerjaan';
						});
						$isMasterMenu = in_array($menuName, ['master', 'master data', 'data master']);
						$hasSubmenu = $menu->children->count() > 0 || $isKepegawaianMenu || $isMasterMenu;
					@endphp
					<li>
						@if($hasSubmenu)
							{{-- Menu dengan Submenu --}}
							<a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
								<div class="menu-icon">
									{!! $menu->icon !!}
								</div>
								<span class="nav-text">{{ $menu->nama_menu }}</span>
							</a>
							<ul aria-expanded="false">
								@foreach($menu->children as $child)
									@php
										$childName = strtolower(trim((string) ($child->nama_menu ?? '')));
										$isPengaturanUjian = $childName === 'pengaturan ujian';
										$childUrl = $isPengaturanUjian
											? url('master/pengaturan-ujian')
											: ($child->url != '#' ? url($child->url) : 'javascript:void(0);');
									@endphp
									<li>
										<a href="{{ $childUrl }}">
											{{ $child->nama_menu }}
										</a>
									</li>
									@if(trim(strtolower((string) ($child->url ?? ''))) === 'master/kurikulum')
										<li>
											<a href="{{ url('master/matriks') }}">Matriks Kurikulum</a>
										</li>
									@endif
								@endforeach
								@php
									$isAkademikMenu = $menuName === 'akademik';
									$hasPengaturanUjian = $menu->children->contains(function ($item) {
										return trim((string) ($item->url ?? '')) === 'master/pengaturan-ujian';
									});
									$isPmbMenu = $menuName === 'pmb';
									$hasSoalPmb = $menu->children->contains(function ($item) {
										$url = trim(strtolower((string) ($item->url ?? '')));

										return $url === 'pmb/soal';
									});
									$hasRombel = $menu->children->contains(function ($item) {
										$url = trim(strtolower((string) ($item->url ?? '')));
										$name = trim(strtolower((string) ($item->nama_menu ?? '')));

										return $url === 'master/rombel' || $name === 'rombel';
									});
									$hasKuesioner = $menu->children->contains(function ($item) {
										$url = trim(strtolower((string) ($item->url ?? '')));
										$name = trim(strtolower((string) ($item->nama_menu ?? '')));

										return $url === 'akademik/kuesioner' || $name === 'kuesioner';
									});
									$hasMasterCpl = $menu->children->contains(function ($item) {
										$url = trim(strtolower((string) ($item->url ?? '')));
										return $url === 'master/cpl';
									});
								@endphp
								@if($isAkademikMenu && !$hasRombel)
									<li>
										<a href="{{ url('master/rombel') }}">Rombel</a>
									</li>
								@endif
								@if($isAkademikMenu && !$hasKuesioner)
									<li>
										<a href="{{ url('akademik/kuesioner') }}">Kuesioner</a>
									</li>
								@endif
								@if($isPmbMenu && !$hasSoalPmb)
									<li>
										<a href="{{ url('pmb/soal') }}">Soal PMB</a>
									</li>
								@endif
								@if($isKepegawaianMenu && !$hasJamKerjaDosen)
									<li>
										<a href="{{ url('simpeg/absensi/jam_kerja_master') }}">Jam Kerja Dosen</a>
									</li>
								@endif
								@if($isKepegawaianMenu && !$hasSuratIzin)
									<li>
										<a href="{{ url('simpeg/SuratIzin2') }}">Surat Izin</a>
									</li>
								@endif
								@if($isKepegawaianMenu && !$hasMeninggalkanPekerjaan)
									<li>
										<a href="{{ url('simpeg/MeninggalkanPekerjaan') }}">Izin Meninggalkan Pekerjaan</a>
									</li>
								@endif
								@if($isMasterMenu && !$hasMasterCpl)
									<li>
										<a href="{{ url('master/cpl') }}">Master CPL</a>
									</li>
								@endif
							</ul>
						@else
							{{-- Menu Tunggal (Tanpa Submenu) --}}
							<a href="{{ url($menu->url) }}" aria-expanded="false">
								<div class="menu-icon">
									{!! $menu->icon !!}
								</div>
								<span class="nav-text">{{ $menu->nama_menu }}</span>
							</a>
						@endif
					</li>
				@endforeach
			</ul>
				
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