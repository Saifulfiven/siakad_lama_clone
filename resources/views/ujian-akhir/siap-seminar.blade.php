<div class="tab-pane fade in active">

	<div class="col-md-12">

		{{ Rmt::AlertError() }}
		{{ Rmt::AlertSuccess() }}

		<table border="0" width="100%" style="margin-bottom: 10px">
			<tr>
				<td width="20">TA</td>
				<td width="170">
					<select class="form-custom mw-2" onchange="filter('smt', this.value)">
						@foreach( Sia::listSemester() as $smt )
							<option value="{{ $smt->id_smt }}" {{ Session::get('ua_semester') == $smt->id_smt ? 'selected' : '' }}>{{ $smt->nm_smt }}</option>
	                    @endforeach
					</select>
				</td>
				<td width="210">
					<select class="form-custom mw-2" onchange="filter('prodi', this.value)" id="list-prodi">
						@foreach( Sia::listProdi() as $pr )
	                    	<option value="{{ $pr->id_prodi }}" {{ Session::get('ua_prodi') == $pr->id_prodi ? 'selected' : '' }}>{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
	                    @endforeach
					</select>
				</td>
				<td>
					<span class="petunjuk btn btn-warning btn-xs" title="Mahasiswa yang Siap Seminar adalah mahasiswa yang  dinyatakan valid oleh keuangan dan  NDC (Jika seminar hasil) dan <br>Telah diisi ruangannya oleh prodi">
                        <i class="fa fa-info" style="color: #fff"></i>
                    </span>
                </td>
				<td>
					<form action="" id="form-cari">
						<div class="input-group pull-right">
							<input type="text" class="form-control input-sm mw-2 pull-right" name="cari_d" value="{{ Request::get('cari_d') }}">
							<div class="input-group-btn">
								<a href="#" class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></a>
								<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</form>
				</td>

			</tr>
		</table>
		
		<div class="table-responsive">
			<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
				<thead class="custom">
					<tr>
						<th width="20px">No.</th>
						<th>NIM</th>
						<th>Nama</th>
						<th>Pembimbing</th>
						<th>Nilai</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody align="center">
					@foreach($mahasiswa as $r)
						<tr>
							<td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
							<td width="100"> {{ $r->nim }}</td>
							<td align="left"> {{ $r->nm_mhs }}</td>
							<td align="left">{{ $r->pembimbing }}</td>
							<td>
								@if ( $r->id_prodi == '61101' && ( Session::get('ua_jenis') == 'P' || Session::get('ua_jenis') == 'H') )
									-
								@else
									<?php $jenis = Session::get('ua_jenis'); ?>

									@if ( $jenis <> 'S' )
										<?php
											$cek_krs = DB::table('nilai')
							                            ->where('id_jdk', $r->id_jdk)
							                            ->where('id_mhs_reg', $r->id_mhs_reg)
							                            ->count(); ?>
							            @if ( $cek_krs > 0 )
							            	{{ $r->nilai_huruf }}
							            @else
											{{ $jenis == 'P' ? $r->nil_mid : $r->nil_final }}
										@endif
									@else
										{{ $r->nilai_huruf }}
									@endif
								@endif
							</td>
							<td>
								<span class="tooltip-area">
								@if ( Sia::akademikOrJurusan() || Sia::admin() )
									@if ( Sia::canAction($r->id_smt) )
										<a href="javascript:;"
											data-id="{{ $r->id_mhs_reg }}"
											data-nim="{{ $r->nim }}"
											data-nama="{{ $r->nm_mhs }}"
											class="btn btn-primary btn-xs btn-penguji" title="Penguji"><i class="fa fa-users"></i></a> &nbsp;
										<a href="javascript:;"
											data-id="{{ $r->id_mhs_reg }}"
											data-nim="{{ $r->nim }}"
											data-nama="{{ $r->nm_mhs }}"
											data-jdk="{{ $r->id_jdk }}"
											class="btn btn-warning btn-xs btn-nilai" title="Nilai"><i class="fa fa-star"></i></a> &nbsp; 
									@endif
								@endif

								<?php $s2 = Session::get('ua_prodi') == '61101' ? true : false; ?>
								<?php $jj = $s2 ? 's2' : 's1' ?>

									<div class="btn-group">
										<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Aksi <span class="caret"></span></button>
										<ul class="dropdown-menu pull-right align-xs-right" role="menu">
											<li>
												<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=undangan&jenjang={{ $jj }}&nomor="
													title="Cetak undangan seminar" target="_blank">Cetak undangan seminar</a>
											</li>

											@if ( $s2 )
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=daftar-hadir-penguji"
														title="Cetak daftar hadir dosen penguji" target="_blank">Cetak daftar hadir penguji</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=daftar-hadir-ujian"
														title="Cetak daftar hadir ujian" target="_blank">Cetak daftar hadir ujian</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=nilai-ujian"
														title="Cetak nilai ujian" target="_blank">Cetak nilai ujian</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=saran"
														title="Cetak lembar saran ujian" target="_blank">Cetak lembar saran perbaikan</a>
												</li>
											
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=berita-acara"
														title="Cetak berita acara" target="_blank">Cetak berita acara</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=rekapitulasi"
														title="Cetak rekapitulasi nilai ujian" target="_blank">Cetak Rekapitulasi Nilai</a>
												</li>

											@else
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&jenjang=s1&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=berita-acara"
														title="Cetak berita acara" target="_blank">Cetak berita acara</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=penilaian"
														title="Cetak daftar penilaian" target="_blank">
														Daftar Penilaian
													</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=saran&jenjang=s1"
														title="Cetak lembar saran ujian" target="_blank">Cetak lembar saran perbaikan</a>
												</li>
											@endif

											@if ( $s2 && Request::get('jenis') == 'S' )
												<li>
													<a href="{{ route('ua_cetak_pernyataan') }}?jenis={{ Session::get('ua_jenis') }}&id_mhs_reg={{ $r->id_mhs_reg }}"
														title="Cetak surat pernyataan tesis" target="_blank">Cetak Surat Pernyataan Tesis</a>
												</li>
											@endif
										</ul>
									</div>

									@if ( !$s2 && $r->sks_mk == 6 )
				<!-- 						<div class="btn-group">
											<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Proposal <span class="caret"></span></button>
											<ul class="dropdown-menu pull-right align-xs-right" role="menu">
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=P&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=undangan&jenjang={{ $jj }}&nomor="
														title="Cetak undangan seminar" target="_blank">Cetak undangan seminar</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=P&jenjang=s1&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=berita-acara"
														title="Cetak berita acara" target="_blank">Cetak berita acara</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=P&id_mhs_reg={{ $r->id_mhs_reg }}"
														title="Cetak daftar penilaian" target="_blank">
														Daftar Penilaian
													</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=P&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=saran&jenjang=s1"
														title="Cetak lembar saran ujian" target="_blank">Cetak lembar saran perbaikan</a>
												</li>
											</ul>
										</div>

										<div class="btn-group">
											<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Seminar Hasil <span class="caret"></span></button>
											<ul class="dropdown-menu pull-right align-xs-right" role="menu">
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=P&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=undangan&jenjang={{ $jj }}&nomor="
														title="Cetak undangan seminar" target="_blank">Cetak undangan seminar</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=H&jenjang=s1&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=berita-acara"
														title="Cetak berita acara" target="_blank">Cetak berita acara</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=H&id_mhs_reg={{ $r->id_mhs_reg }}"
														title="Cetak daftar penilaian" target="_blank">
														Daftar Penilaian
													</a>
												</li>
												<li>
													<a href="{{ route('ua_berita_acara') }}?jenis=H&id_mhs_reg={{ $r->id_mhs_reg }}&cetak=saran&jenjang=s1"
														title="Cetak lembar saran ujian" target="_blank">Cetak lembar saran perbaikan</a>
												</li>
											</ul>
										</div> -->
									@endif

								</span>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			
			<div class="pull-left">
				Jumlah data : {{ $mahasiswa->total() }}
			</div>

			<div class="pull-right"> 
				<?php $query_string = [ 'jenis' => $get_jenis, 'tab' => $tab_aktif] ?>

				{!! $mahasiswa->appends($query_string)->links() !!}
			</div>

		</div>
	</div>

</div>