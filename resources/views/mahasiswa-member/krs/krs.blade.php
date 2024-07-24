<div class="table-responsive">

	<table class="table">
		<tr>
			<td width="120">NIM</td>
			<td>: {{ $mhs->nim }}</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>: {{ $mhs->mhs->nm_mhs }}</td>
		</tr>
		<tr>
			<td>Program Studi</td>
			<td>: {{ $mhs->prodi->nm_prodi .' ('.$mhs->prodi->jenjang.')' }}</td>
		</tr>
		@if ( !empty($mhs->id_konsentrasi) )
			<tr>
				<td>Konsentrasi</td>
				<td>: {{ $mhs->konsentrasi->nm_konsentrasi }}</td>
			</tr>
		@endif
		<tr>
			<td>Semester</td>
			<td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai) }}</td>
		</tr>
		<tr>
			<td>Tahun Akademik</td>
			<td>: {{ Sia::sessionPeriode('nama') }}</td>
		</tr>
		<tr>
			<td>Dosen PA</td>
			<td>: 
				@if ( !empty($mhs->dosenWali) )
					{{ $mhs->dosenWali->gelar_depan }} {{ $mhs->dosenWali->nm_dosen }}, {{ $mhs->dosenWali->gelar_belakang }}
				@else
					-
				@endif
			</td>
		</tr>
		<tr>
			<td colspan="2"></td>
		</tr>
	</table>

	<div style="max-width: 700px">
		<div class="alert alert-info">
			<p>Registrasi KRS anda telah selesai, apabila anda ingin mengubah 
				KRS silahkan konsultasi dengan Dosen Wali/PA 
			anda dengan menyertakan hasil cetakan KRS online.</p>
			<p>Silahkan <b>CETAK KRS</b> dan setor kebagian akademik setelah disetujui oleh dosen PA.</p>
		</div>

		<div class="pull-right">
			<a href="{{ route('mhs_krs_cetak') }}?jenis={{ $krs_stat['jenis'] }}" class="btn btn-primary btn-sm" target="_blank">
				<i class="fa fa-print"></i> CETAK KRS
			</a>&nbsp;
			<a href="{{ route('mhs_ksm_cetak') }}?jenis={{ $krs_stat['jenis'] }}" class="btn btn-primary btn-sm" target="_blank">
				<i class="fa fa-print"></i> CETAK KSM
			</a>
		</div>
		<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
		    <thead class="custom">
		    	<tr>
		    		<th colspan="4">Matakuliah yang diambil tahun akademik {{ Sia::sessionPeriode() }}</th>
		    	</tr>
		        <tr>
		            <th width="20px">No.</th>
		            <th>Kode matakuliah</th>
		            <th>Nama matakuliah</th>
		            <th>SKS</th>
		        </tr>
		    </thead>

		    <tbody align="center">
		    	@if ( count($krs) == 0 )
		    		<tr>
		    			<td colspan="4">Belum ada KRS</td>
		    		</tr>
		    	@endif

		        <?php $total_sks = 0 ?>
		        @foreach( $krs as $k )
		            <tr>
		                <td>{{ $loop->iteration }}</td>
		                <td align="left">{{ $k->kode_mk }}</td>
		                <td align="left">{{ $k->nm_mk }}</td>
		                <td>{{ $k->sks_mk }}</td>
		            </tr>
		            <?php $total_sks += $k->sks_mk ?>
		        @endforeach
		        <tr>
		            <th colspan="3">Total SKS</th>
		            <th>{{ $total_sks }}</th>
		        </tr>
		    </tbody>
		</table>
	</div>
</div>
