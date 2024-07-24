<div class="row">
	<div class="col-sm-8">

		<button type="button" class="btn btn-theme-inverse" onclick="keadaanMahasiswa()">KEADAAN MAHASISWA</button>

		<button type="button" class="btn btn-theme" onclick="keadaanDosen()">KEADAAN DOSEN</button>

		<section class="panel">
			<header class="panel-heading no-borders xs">
				<h2><strong>AKTIVITAS PERKULIAHAN</strong></h2>
				<label class="color">Periode  <strong> {{ Sia::sessionPeriode('nama') }}</strong></label>
			</header>		
			<div class="panel-body align-lg-center">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead class="custom">
							<tr>
								<th rowspan="2">No</th>
								<th rowspan="2">Program Studi</th>
								<th rowspan="2">Jumlah</th>
								<th colspan="6">Status Aktif Mahasiswa</th>
								<th rowspan="2">Rasio Dosen</th>
							</tr>
							<tr>
								<th>Aktif</th>
								<th>Cuti</th>
								<th>Kampus Merdeka</th>
								<th>Non Aktif</th>
                <th> - </th>
								<th>Double Degree</th>
							</tr>
						</thead>
						<tbody>
							<?php $total = [] ?>
							<?php $total_jml = 0 ?>
							<?php $unknown = 0 ?>
							@foreach( $prodi as $pr )

								<?php $jml_dosen = DB::table('dosen')
										->where('id_prodi', $pr->id_prodi)
										->where('jenis_dosen', 'DTY')
										->count(); 
									$jml_mhs = $pr->jml + $pr->lainnya;
									$rasio = $jml_mhs > 0 && $jml_dosen > 0 ? round($jml_mhs/$jml_dosen,1) : 0;
								?>
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td align="left">{{ $pr->jenjang .' '.$pr->nm_prodi }}</td>
									<td><a href="javascript:;" 
											onclick="detailAkmTotal(
												'{{ $pr->id_prodi }}',
												'{{ $pr->nm_prodi.' ('.$pr->jenjang.')' }}'
											)"><strong><u>{{ $jml_mhs }}</u></strong>
										</a>
									</td>
									<?php
									$total_jml += $pr->jml + $pr->lainnya;
									$unknown += $pr->lainnya;

									$status = DB::table('status_mhs as st')
			                            ->select('st.*',DB::raw('(SELECT COUNT(*) AS agr FROM aktivitas_kuliah as akm
			                            		left join mahasiswa_reg as m on akm.id_mhs_reg=m.id
			                                    where akm.status_mhs = st.id_stat_mhs
			                                    and akm.id_smt='.Sia::sessionPeriode().'
			                                	and m.id_prodi = \''.$pr->id_prodi.'\') as jml'))
			                            ->orderBy('nm_stat_mhs')->get(); ?>

									@foreach( $status as $st )
										<?php $total[$st->id_stat_mhs] = @$total[$st->id_stat_mhs] + $st->jml ?>
										<td><a href="javascript:;" 
											onclick="detailAkm(
												'{{ $st->id_stat_mhs }}',
												'{{ $st->nm_stat_mhs }}',
												'{{ $pr->id_prodi }}',
												'{{ $pr->nm_prodi.' ('.$pr->jenjang.')' }}'
											)"><strong><u>{{ $st->jml }}</u></strong>
											</a>
										</td>
									@endforeach
									<td><a href="javascript:;" 
											onclick="detailAkm(
												'x',
												'Tak Diketahui',
												'{{ $pr->id_prodi }}',
												'{{ $pr->nm_prodi.' ('.$pr->jenjang.')' }}'
											)"><strong><u>{{ $pr->lainnya }}</u></strong>
										</a>
									</td>

									<!-- Rasio -->
									<td>1 : {{ $rasio }}</td>
								</tr>
							@endforeach
							<tr>
								<td colspan="2"><b>Total</b></td>
								<td><b>{{ $total_jml }}</b></td>
								@foreach( $total as $tot )
									<td><b>{{ $tot }}</b></td>
								@endforeach
								<td><b>{{ $unknown }}</b></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="9"><small><i class="fa fa-question-"></i> Klik pada angka untuk melihat detail</small>
								</td>
							</tr>
						</tbody>
						
					</table>
				</div>
			</div>	
		</section>
	</div>

	<div class="col-sm-4">
		<section class="panel" style="margin-top: 36px">
			<header class="panel-heading no-borders xs">
				<h2><strong>MAHASISWA BARU</strong> </h2>
				<label class="color">Periode  <strong> {{ Sia::sessionPeriode('nama') }}</strong></label>
			</header>
      {{-- {{ dd($jml_maba) }} --}}
			<div class="panel-body align-lg-center">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="custom">
              <tr>
                <th>No.</th>
                <th>Jurusan</th>
                <th>Jumlah Mahasiswa</th>
              </tr>
            </thead>
            <tbody>
              @php
                  $i = 1;
                  $totalMahasiswa = 0;
              @endphp
              @foreach ($jml_maba as $jm)
              <tr>
                <td>{{ $i++ }}</td>
                <td align="left">{{ $jm->jenjang }} {{ $jm->nm_prodi }}</td>
                <td>{{ $jm->jml }}</td>
              </tr>
				  @php $totalMahasiswa += $jm->jml @endphp
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
			 <div style="padding: 5px;margin-left: 16px;margin-bottom: 16px"><b>Total :</b> {{ $totalMahasiswa }}</div>
		</section>
	</div>
	
	<div class="col-sm-8">
		<section class="panel">
			<header class="panel-heading no-borders xs">
				<h2><strong>MAHASISWA LULUS/KELUAR</strong> </h2>
				<label class="color">Periode  <strong> {{ Sia::sessionPeriode('nama') }}</strong></label>
			</header>		
			<div class="panel-body align-lg-center">
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead class="custom">
							<?php
							$field_prodi = DB::table('jenis_keluar')
										->select('ket_keluar')
										->where('id_jns_keluar', '<>', 0)
										->orderBy('id_jns_keluar')->get(); ?>

							<tr>
								<th rowspan="2">No</th>
								<th rowspan="2">Program Studi</th>
								<th rowspan="2">Jumlah</th>
								<th colspan="{{ count($field_prodi) }}">Status Keluar Mahasiswa</th>
							</tr>
							
							<tr>
							@foreach( $field_prodi as $fp )
								<th>{{ $fp->ket_keluar }}</th>
							@endforeach
							<tr>
						</thead>
						<tbody>
							<?php
								$total = [];
								$total_jml = 0;
							?>
							@foreach( $prodi2 as $pr )
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td align="left">{{ $pr->jenjang .' '.$pr->nm_prodi }}</td>
									<td>{{ $pr->jml }}</td>
									<?php
									$total_jml += $pr->jml;

									$status = DB::table('jenis_keluar as jk')
			                            ->select('id_jns_keluar',DB::raw('(SELECT COUNT(*) AS agr FROM mahasiswa_reg
			                                    where id_jenis_keluar = jk.id_jns_keluar
			                                    and semester_keluar='.Sia::sessionPeriode().'
			                                	and id_prodi = \''.$pr->id_prodi.'\') as jml'))
			                            ->where('jk.id_jns_keluar','<>',0)
			                            ->orderBy('id_jns_keluar')->get(); ?>
									@foreach( $status as $st )
										<?php @$total[$st->id_jns_keluar] += $st->jml ?>
										<td>{{ $st->jml }}</td>
									@endforeach
								</tr>
							@endforeach
							<tr>
								<td colspan="2"><b>Total</b></td>
{{--								<td><b>{{ $total_jml }}</b></td>--}}
								@foreach( $total as $to )
									<td><b>{{ $to }}</b></td>
								@endforeach
							</tr>
						</tbody>
						
					</table>
				</div>
			</div>	
		</section>
	</div>

	<div class="col-sm-4">
		<section class="panel">
			<header class="panel-heading no-borders xs">
				<h2><strong>IPK RATA-RATA</strong></h2>
				<label class="color">Periode  <strong> {{ Sia::sessionPeriode('nama') }}</strong></label>
			</header>
			<div class="panel-body align-lg-center">
				<table class="table table-bordered">
					<thead class="custom">
						<tr>
							<th>Prodi</th>
							<th>IPK Rata-rata</th>
						</tr>
					</thead>
					<tbody>
						@foreach( $ipk as $ip )
							<tr>
								<td align="left">{{ $ip->jenjang.' '.$ip->nm_prodi }}</td>
								<td>{{ $ip->ipk }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</section>
	</div>
</div>

<div id="modal-aktivitas" class="modal fade" tabindex="-1" style="top: 20%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Mahasiswa <strong><span id="akm"></span></strong></h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
    	<div id="konten-akm">
    		
    	</div>
    </div>
    <!-- //modal-body-->
    <div class="modal-footer">
    	<center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
        </center>
    </div>
</div>

<div id="modal-akm-total" class="modal fade" tabindex="-1" style="top: 15%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">AKTIVITAS <strong>PERKULIAHAN</strong></h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
    	<div id="konten-akm-total">
    		
    	</div>
    </div>
    <!-- //modal-body-->
    <div class="modal-footer">
    	<center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
        </center>
    </div>
</div>

<div id="modal-detail-ipk" class="modal fade" tabindex="-1" style="top: 20%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Data IPK <strong><span id="ipk"></span></strong></h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
    	<div id="konten-ipk">
    		
    	</div>
    </div>
    <!-- //modal-body-->
    <div class="modal-footer">
    	<center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
        </center>
    </div>
</div>

<div id="modal-keadaan-mahasiswa" class="modal fade container" style="top: 15%">
    <div class="modal-header">
        <h4 class="modal-title">Keadaan Mahasiswa
	        <div class="pull-right">
	        	<button type="button" id="cetak-keadaan-mhs" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> Cetak</button>
	        	<button type="button" class="btn btn-xs btn-danger" data-dismiss="modal"><i class="fa fa-times"></i></button>
	        </div>
        </h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
    	<div id="konten-keadaan">
    		
    	</div>
    </div>
    <!-- //modal-body-->
</div>

<div id="modal-keadaan-dosen" class="modal fade" data-width="900" style="top: 20%">
    <div class="modal-header">
        <h4 class="modal-title">Keadaan Dosen
		    <div class="pull-right">
		    	<button type="button" id="cetak-keadaan-dosen" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> Cetak</button>
		    	<button type="button" class="btn btn-xs btn-danger" data-dismiss="modal"><i class="fa fa-times"></i></button>
		    </div>
		</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
    	<div id="konten-keadaan-dosen">
    		
    	</div>
    </div>
</div>

<script>


	function detailAkm(id_status,status, id_prodi, prodi)
	{
		$('#akm').html(status);
		$('#modal-aktivitas').modal('show');
		$('#konten-akm').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
		$.ajax({
    		url: '{{ route('detail_akm') }}',
    		data: { id_prodi: id_prodi, prodi:prodi, status: id_status },
    		success: function(result){
    			$('#konten-akm').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
	}

	function detailAkmTotal(id_prodi, prodi)
	{
		$('#akm').html(status);
		$('#modal-akm-total').modal('show');
		$('#konten-akm-total').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
		$.ajax({
    		url: '{{ route('detail_akm_total') }}',
    		data: { id_prodi: id_prodi, prodi:prodi },
    		success: function(result){
    			$('#konten-akm-total').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
	}

	function keadaanMahasiswa(prodi = null)
	{
		if ( prodi === null ) {
			$('#modal-keadaan-mahasiswa').modal('show');
		}

		$('#konten-keadaan').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
		$.ajax({
    		url: '{{ route('keadaan_mhs') }}',
    		data: { prodi: prodi},
    		success: function(result){
    			$('#konten-keadaan').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
	}

	function keadaanDosen()
	{
		$('#modal-keadaan-dosen').modal('show');
		$('#konten-keadaan-dosen').html('<center><i class="fa fa-spinner fa-spin"></i></center>');

		$.ajax({
    		url: '{{ route('keadaan_dosen') }}',
    		success: function(result){
    			$('#konten-keadaan-dosen').html(result);
    		},
    		error: function(data,status,msg){
    			alert(msg);
    		}
    	});
	}

</script>