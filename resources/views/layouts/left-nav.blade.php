	<nav id="menu" data-search="close">
	<!-- <nav id="menu" class="mm-menu mm-horizontal mm-ismenu mm-hassearch mm-current mm-opened"> -->
		<?php $level = Auth::user()->level ?>

		<ul>
			<li><a href="{{ url('beranda') }}"><i class="icon fa fa-laptop"></i> DASHBOARD 
				</a>
			</li>

			@if ( $level == 'dosen' )

				<li><span><i class="icon fa fa-book"></i> PEDOMAN</span>
					<ul>
						<li><a href="{{ route('dsn_rps', ['prodi' => 61201]) }}"><i class="icon fa fa-book"></i> RPS </a></li>
						<li><a href="https://drive.google.com/file/d/10BqlAV5V6cs1IUyHVHvxiVbnjBV6j685/view" target="_blank"><i class="icon fa fa-book"></i> PEDOMAN TESIS </a></li>
						<li><a href="https://drive.google.com/open?id=0B8kRHIYibi54T0JqMzNKSUxaU3c" target="_blank"><i class="icon fa fa-book"></i> PEDOMAN SKRIPSI </a></li>
					</ul>
				</li>
				<li><a href="{{ route('dsn_jadwal') }}"><i class="icon fa fa-calendar"></i> E-LEARNING / MATAKULIAH </a></li>
				<li><a href="{{ route('dsn_bim') }}"><i class="icon fa fa-ticket"></i> BIMBINGAN SKRIPSI / TESIS </a></li>
				<li><a href="{{ route('dsn_pgj') }}"><i class="icon fa fa-ticket"></i> PENGUJIAN SKRIPSI / TESIS </a></li>
				<li><a href="{{ route('dsn_approv_seminar') }}"><i class="icon fa fa-file"></i> PERSETUJUAN SEMINAR/UJIAN </a></li>
				<li><a href="{{ route('dsn_seminar') }}"><i class="icon fa fa-star"></i> PENILAIAN UJIAN SEMINAR </a></li>
				<li><a href="{{ route('dsn_kegiatan') }}"><i class="icon fa fa-archive"></i>  DOKUMEN KEGIATAN</a></li>
				<li><a href="{{ route('dsn_fm') }}"><i class="icon fa fa-file"></i> FILE MANAGER </a></li>
				<li><a href="{{ route('dsn_profil') }}"><i class="icon fa fa-user"></i> PROFIL </a></li>

			@endif

			@if ( $level == 'mahasiswa' )
				<li><a href="{{ route('mhs_pembayaran') }}"><i class="icon fa fa-money"></i> PEMBAYARAN </a></li>
				<li><a href="{{ route('mhs_konfir') }}"><i class="icon fa fa-file-text"></i> KONFIRMASI PEMBAYARAN </a></li>
				<li><span>
					<i class="icon fa fa-th-list"></i> PERKULIAHAN
				</span>
					<ul>
						<li><a href="{{ route('mhs_krs') }}"><i class="icon fa fa-list"></i> ISI KRS </a></li>
						<li><a href="{{ route('mhs_lms') }}"><i class="icon fa fa-globe"></i> E-LEARNING </a></li>
						<li><a href="{{ route('mhs_jdk') }}"><i class="icon fa fa-calendar"></i> JADWAL KULIAH </a></li>
						<li><a href="{{ route('mhs_absensi') }}"><i class="icon fa fa-check-square-o"></i> ABSENSI </a></li>
						<li><a href="{{ route('mhs_jdu') }}"><i class="icon fa fa-calendar"></i> JADWAL UJIAN </a></li>
						<li><a href="{{ route('mhs_kartu_ujian') }}"><i class="icon fa fa-credit-card"></i> KARTU UJIAN </a></li>
						<li><a href="{{ route('mhs_khs') }}"><i class="icon fa fa-star"></i> KHS </a></li>
						<li><a href="{{ route('mhs_transkrip') }}"><i class="icon fa fa-file"></i> TRANSKRIP NILAI </a></li>
						<li><a href="{{ route('mhs_kues') }}"><i class="icon fa fa-star-half"></i> KUESIONER </a></li>
					</ul>
				</li>
				<li><a href="{{ route('mhs_bim') }}"><i class="icon fa fa-ticket"></i> BIMBINGAN SKRIPSI / TESIS </a></li>
				<li><a href="{{ route('mhs_seminar') }}"><i class="icon fa fa-file"></i> PENDAFTARAN SEMINAR/UJIAN </a></li>
				<li><a href="{{ route('daftar_ijazah') }}"><i class="icon fa fa-file"></i> PENGAMBILAN IJAZAH </a></li>
				<li><a href="{{ route('mhs_rps', ['prodi' => 61201]) }}"><i class="icon fa fa-book"></i> RPS </a></li>
				@if ( Sia::sessionMhs('prodi') == '61101' )
					<li><a href="https://drive.google.com/file/d/10BqlAV5V6cs1IUyHVHvxiVbnjBV6j685/view" target="_blank"><i class="icon fa fa-book"></i> PEDOMAN TESIS </a></li>
				@else
					<li><a href="https://drive.google.com/open?id=0B8kRHIYibi54T0JqMzNKSUxaU3c" target="_blank"><i class="icon fa fa-book"></i> PEDOMAN SKRIPSI </a></li>
				@endif

				@if ( Sia::sessionMhs('prodi') == '61101' )
					<li><a href="{{ route('mhs_konsentrasi') }}"><i class="icon fa fa-bullseye"></i> PILIH KONSENTRASI </a></li>
				@endif
				<li><a href="{{ route('mhs_jurnal') }}"><i class="icon fa fa-book"></i> UPLOAD JURNAL </a></li>
				@if ( Sia::sessionMhs('prodi') != '61101' )
					<li><a href="{{ route('m_pkm') }}"><i class="icon fa fa-flask"></i> DAFTAR PKM </a></li>
				@endif
				<li><a href="{{ route('mhs_profil') }}"><i class="icon fa fa-user"></i> PROFIL </a></li>
				<li><a href="{{ url('/logout') }}"
                    	onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i class="icon fa fa-sign-out"></i> KELUAR </a></li>
				<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			@endif

			@if ( in_array($level, ['admin','akademik','ketua 1', 'cs', 'personalia','jurusan','keuangan','jurnal','pustakawan']) )
				<li>
					<span>
						<i class="icon  fa fa-users"></i> MAHASISWA 
					</span>
					<ul>
						<li><a href="{{ route('mahasiswa') }}">MAHASISWA </a></li>
						@if ($level == 'admin')
							<li>
								<a href="{{route('mhsKip')}}">BEASISWA</a>
							</li>
						@endif
						@if ( in_array($level, ['admin','akademik', 'cs']) )
							<li><a href="{{ route('maba') }}">CALON MABA </a></li>
						@endif
					</ul>
				</li>
			@endif
			
			@if ( in_array($level, ['ndc']) )
				<li><a href="{{ route('val_ndc') }}"><i class="icon  fa fa-check-square"></i> OLAH DATA / VALIDASI </a></li>
			@endif

			@if ( in_array($level, ['jurusan', 'keuangan', 'pustakawan']) )
				<li><a href="{{ route('val_ijazah') }}"><i class="icon  fa fa-check-square"></i> VAL. PENGAMBILAN IJAZAH </a></li>
			@endif

			@if ( $level == 'keuangan' )
				<li><a href="{{ route('keu') }}"><i class="icon fa fa-dollar"></i> PEMBAYARAN KULIAH </a></li>
				<li><a href="{{ route('keu_konfir') }}"><i class="icon fa fa-file-text"></i> KONFIRMASI PEMBAYARAN </a></li>
				<li><a href="{{ route('seminar') }}"><i class="icon fa fa-file"></i> VALIDASI SEMINAR </a></li>
				<li><a href="{{ route('keu_tunggakan') }}"><i class="icon fa fa-dollar"></i> MAHASISWA MENUNGGAK </a></li>
				<li><a href="{{ route('briva_akun') }}"><i class="icon fa fa-credit-card"></i> AKUN BRIVA </a></li>
				<li><a href="{{ route('ku') }}"><i class="icon fa fa-credit-card"></i> KARTU UJIAN </a></li>
				<li><a href="{{ route('ms_bank') }}"><i class="icon fa fa-th-list"></i> MASTER BANK </a></li>
				<li><a href="{{ route('ms_biaya') }}"><i class="icon fa fa-wrench"></i> SETTING BIAYA KULIAH </a></li>
				<li><a href="{{ route('pot') }}"><i class="icon fa fa-cut"></i> POTONGAN BIAYA KULIAH </a></li>
			@endif

			@if ( $level == 'admin' )
				<li><span><i class="icon fa fa-dollar"></i> KEUANGAN</span>
					<ul>
						<li><a href="{{ route('keu') }}"><i class="icon fa fa-dollar"></i> PEMBAYARAN KULIAH </a></li>
						<li><a href="{{ route('keu_konfir') }}"><i class="icon fa fa-file-text"></i> KONFIRMASI PEMBAYARAN </a></li>
						<li><a href="{{ route('seminar') }}"><i class="icon fa fa-file"></i> VALIDASI SEMINAR </a></li>
						<li><a href="{{ route('keu_tunggakan') }}"><i class="icon fa fa-dollar"></i> MAHASISWA MENUNGGAK </a></li>
						<li><a href="{{ route('briva_akun') }}"><i class="icon fa fa-credit-card"></i> AKUN BRIVA </a></li>
						<li><a href="{{ route('ku') }}"><i class="icon fa fa-credit-card"></i> KARTU UJIAN </a></li>
						<li><a href="{{ route('ms_bank') }}"><i class="icon fa fa-th-list"></i> MASTER BANK </a></li>
						<li><a href="{{ route('ms_biaya') }}"><i class="icon fa fa-wrench"></i> SETTING BIAYA KULIAH </a></li>
						<li><a href="{{ route('pot') }}"><i class="icon fa fa-cut"></i> POTONGAN BIAYA KULIAH </a></li>
					</ul>
				</li>
			@endif

			@if ( $level == 'admin' || $level == 'akademik' || $level == 'karyawan' || $level == 'cs' || $level == 'jurusan' || $level == 'keuangan' || $level == 'pengawas'  )
				<li><a href="{{ route('ku_status') }}"><i class="icon fa fa-check-square"></i> CEK KARTU UJIAN </a></li>
			@endif

			@if ( in_array($level, ['admin','akademik','ketua 1', 'cs', 'personalia','jurusan']) )
				<li><span><i class="icon fa fa-user-md"></i> DOSEN</span>
					<ul>
						<li><a href="{{ route('dosen') }}">DATA DOSEN</a></li>
						<li><a href="{{ route('dosen_kegiatan') }}">KEGIATAN DOSEN</a></li>
					</ul>
				</li>
				<li><span><i class="icon fa fa-th-list"></i> PERKULIAHAN</span>
					<ul>
						@if ( $level == 'admin' || $level == 'akademik')
							<li><a href="{{ route('matakuliah') }}">MATAKULIAH</a></li>
							<li><a href="{{ route('kurikulum') }}">KURIKULUM</a></li>
						@endif
						<li><a href="{{ route('kelas') }}">KELAS MAHASISWA</a></li>
						<li><a href="{{ route('jdk') }}">JADWAL & KRS</a></li>
						@if ( in_array($level, ['admin','akademik','jurusan']) )
							<li><a href="{{ route('daftar_sp') }}">PENDAFTARAN SP</a></li>
						@endif
						<li><a href="{{ route('mhs_krs_lap') }}">LAPORAN KRS MAHASISWA</a></li>
						<li><a href="{{ route('status_krs') }}">STATUS KRS </a></li>
						<li><a href="{{ route('ua') }}?jenis=P">UJIAN AKHIR</a></li>
						@if ( in_array($level,['admin','akademik','ketua 1','jurusan']))
							<li><a href="{{ route('nil') }}">NILAI</a></li>
						@endif
						<li><a href="{{ route('akm') }}">AKTIVITAS PERKULIAHAN</a></li>
						<li><a href="{{ route('mbkm') }}">AKTIVITAS KAMPUS MERDEKA</a></li>
						<li><a href="{{ route('konversi_mbkm') }}">KONVERSI KAMPUS MERDEKA</a></li>
						<li><a href="{{ route('lk') }}">LULUS / KELUAR</a></li>
					</ul>
				</li>
				<li><a href="{{ route('absen') }}"><i class="icon fa fa-bar-chart-o"></i> KEHADIRAN MAHASISWA </a></li>

				@if ( Sia::role('admin|akademik|cs') )
					<li><a href="{{ route('konsentrasi') }}"><i class="icon fa fa-ticket"></i> PILIH KONSENTRASI </a></li>
				@endif

				@if ( $level == 'admin' || $level == 'akademik')
					<li><span><i class="icon fa fa-th-list"></i> MASTER DATA</span>
						<ul>
							<li><a href="{{ route('m_fakultas') }}">FAKULTAS</a></li>
							<li><a href="{{ route('m_prodi') }}">PROGRAM STUDI</a></li>
							<li><a href="{{ route('m_konsentrasi') }}">KONSENTRASI</a></li>
							<li><a href="{{ route('m_skalanilai') }}">SKALA NILAI</a></li>
							<li><a href="{{ route('m_kelas') }}">KELAS</a></li>
							<li><a href="{{ route('m_jamkuliah') }}">JAM KULIAH</a></li>
							<li><a href="{{ route('m_ruangan') }}">RUANGAN KULIAH</a></li>
							@if ( $level == 'admin' )
								<li><a href="{{ route('m_semester') }}">SEMESTER</a></li>
							@endif
						</ul>
					</li>

					@if ( $level == 'admin' )

						<li><span><i class="icon fa fa-cog"></i> PENGATURAN</span>
							<ul>
								<li><a href="{{ route('ja') }}">JADWAL AKADEMIK</a></li>
								<li><a href="{{ route('set') }}">SISTEM</a></li>
							</ul>
						</li>

					@else

						<li><a href="{{ route('set') }}"><i class="icon fa fa-cog"></i> PENGATURAN SISTEM </a></li>

					@endif
				@endif
				<li><a href="{{ route('validasi') }}"><i class="icon fa fa-info"></i> VALIDASI DATA </a></li>
			@endif
			
			@if ( $level == 'admin' )
				<li><a href="{{ route('users') }}"><i class="icon fa fa-user"></i> PENGGUNA </a></li>
			@endif

			@if ( Auth::user()->naik_smt == 1 )
				<li><a href="{{ route('naik_smt') }}"><i class="icon fa fa-star"></i> NAIK SEMESTER </a></li>
			@endif

			@if ( $level == 'admin' || $level == 'akademik' || $level == 'cs' || $level == 'jurusan' )
				<li><span><i class="icon fa fa-th-list"></i> KUESIONER</span>
					<ul>
						<li><a href="{{ route('kues_hasil') }}">HASIL </a></li>
						<li><a href="{{ route('kues_jadwal') }}">KUESIONER</a></li>
						<li><a href="{{ route('kues_komponen_isi') }}">KOMPONEN DATA</a></li>
						<li><a href="{{ route('kues_komponen') }}">KOMPONEN</a></li>
					</ul>
				</li>
			@endif

			{{-- @if ( in_array($level, ['admin','akademik', 'cs']) )
				<li><a href="{{ route('maba') }}"><i class="icon fa fa-plus-square"></i> MAHASISWA BARU </a></li>
			@endif --}}

			@if ( in_array($level, ['admin','akademik','ketua 1', 'jurusan']) )
				<li><a href="{{ route('lms') }}"><i class="icon fa fa-globe"></i> E-LEARNING </a></li>
			@endif
			@if ( in_array($level, ['admin','akademik']) )
				<!-- <li><a href="{{ route('materi') }}"><i class="icon fa fa-book"></i> MATERI PASCA </a></li> -->
			@endif

			@if ( in_array($level, ['admin','akademik']) )
				<li><a href="{{ route('pkm') }}"><i class="icon fa fa-flask"></i> PESERTA PKM </a></li>
			@endif

			@if ( $level == 'admin' || $level == 'akademik' || $level == 'karyawan' || $level == 'cs' || $level == 'jurusan' || $level == 'keuangan' || $level == 'ketua 1' || $level == 'personalia' )
				<li><a href="{{ url('informasi') }}"><i class="icon fa fa-info-circle"></i> INFORMASI </a></li>
			@endif

		</ul>
	</nav>
	<!-- //nav left menu-->