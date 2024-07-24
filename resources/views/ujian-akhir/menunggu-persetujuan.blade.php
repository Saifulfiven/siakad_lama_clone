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
					<span class="petunjuk btn btn-warning btn-xs" title="Mahasiswa yang menunggu persetujuan adalah mahasiswa yang belum dinyatakan valid oleh keuangan dan <br>belum valid NDC (Jika seminar hasil) dan <br>belum diisi ruangannya oleh prodi atau Belum disetujui oleh pembimbing & penguji">
                        <i class="fa fa-info" style="color: #fff"></i>
                    </span>
                </td>
				<td>
					<form action="" id="form-cari">
						<div class="input-group pull-right">
							<input type="text" class="form-control input-sm mw-2 pull-right" name="cari_c" value="{{ Request::get('cari_c') }}">
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
											class="btn btn-primary btn-xs btn-penguji" title="Lihat detail"><i class="fa fa-search-plus"></i></a>
									@endif
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