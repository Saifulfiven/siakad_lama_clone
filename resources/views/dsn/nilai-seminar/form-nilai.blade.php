{{ csrf_field() }}
<input type="hidden" name="jenis" value="{{ $jenis }}">
<input type="hidden" name="id_mhs_reg" value="{{ $mhs->id }}">
<input type="hidden" name="id_prodi" value="{{ $mhs->id_prodi }}">
<input type="hidden" name="id_smt" value="{{ $id_smt }}">

<table class="table table-bordered table-hover" style="font-size: 12pt">
	<thead class="custom">
		<tr>
			<th>No</th>
			<th style="text-align: left">Kriteria Penilaian</th>
			<th width="100">Nilai</th>
		</tr>
	</thead>
	
	<?php $ujian = $jenis == 'P' ? 'SEMINAR PROPOSAL' : 'SEMINAR HASIL PENELITIAN'; ?>

	<?php $ujian = $jenis == 'S' ? 'AKHIR TESIS / UJIAN TUTUP' : $ujian ?>

	@if ( count($nilai) == 0 )

		<input type="hidden" name="aksi" value="insert">

		@foreach( Sia::kriteriaPenilaian($mhs->id_prodi) as $key => $val )

			<tr>
				<td align="center">{{ $no = $loop->iteration }}</td>
				<td>{{ $val }}</td>
				<td align="center">
			    	<input type="text" name="nilai[<?= $key ?>]" class="form-control input-lg mw-1 number" required="">
			    </td>
			</tr>

		@endforeach

		@if ( $mhs->id_prodi == 61101 )
			<tr>
		        <td align="center">{{ $no + 1 }}</td>
		        <td align="left">{{ $ujian == 'AKHIR TESIS / UJIAN TUTUP' ? 'Ujian '.ucwords(strtolower($ujian)) : ucwords(strtolower($ujian)) }}</td>
		        <td>
		        	<input type="text" name="nilai[<?= $key + 1 ?>]" class="form-control input-lg mw-1 number" required="">
		        </td>
		    </tr>
	    @endif

	@else

		<input type="hidden" name="aksi" value="update">

		<?php $no = 1 ?>

		<?php $kriteria = Sia::kriteriaPenilaian($mhs->id_prodi); ?>

		@foreach( $nilai as $val )

			<tr>
				<td align="center">{{ $no++ }}</td>
				<td>
					
					@if ( isset($kriteria[$val->kriteria_penilaian]) )
						{{ $kriteria[$val->kriteria_penilaian] }}
					@else
						{{ $ujian == 'AKHIR TESIS / UJIAN TUTUP' ? 'Ujian '.ucwords(strtolower($ujian)) : ucwords(strtolower($ujian)) }}
					@endif
				</td>
				<td align="center">
			    	<input type="text" name="nilai[<?= $val->id ?>]" class="form-control input-lg mw-1 number" value="<?= $val->nilai ?>">
			    </td>
			</tr>

		@endforeach

	@endif

</table>

<div class="form-group" style="text-align: center;margin: 30px 0 10px 0">
	<button type="button" data-dismiss="modal" class="btn btn-info"><i class="fa fa-times"></i> Batal/tutup</button> &nbsp; 
	<button class="btn btn-theme" id="btn-submit-nilai"><i class="fa fa-save"></i> Simpan</button>
</div>