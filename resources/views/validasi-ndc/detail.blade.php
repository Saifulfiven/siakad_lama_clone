<div class="modal-body">
	
	<table class="table table-bordered">
		<tr>
			<th>File</th>
			<th>Keterangan</th>
		</tr>

		@foreach( $files as $val )
			<tr>
				<td align="center">
					<a href="{{ config('app.url-file-seminar') }}/{{ $seminar->nim }}/{{ $val->file }}" title="Lihat/Download" target="_blank">
						<?php $icon = Rmt::icon($val->file); ?>
				        <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
				    </a>
				</td>
				<td>{{ $val->ket }}</td>
			</tr>
		@endforeach
	
		@if ( $files->count() == 0 )
			<tr>
				<td colspan="2" align="center">Belum ada file diupload</td>
			</tr>
		@endif

	</table>

	<div class="alert alert-info"><i class="fa fa-info-circle"></i> Klik pada icon file untuk mengunduh</div>
</div>

<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn btn-default pull-left"><i class="fa fa-times"></i> Tutup</button>

	@if ( $seminar->validasi_ndc != 1 )
		<a href="{{ route('val_ndc_proses', ['id' => $seminar->id, 'disetujui' => 1]) }}"
			onclick="return confirm('Anda ingin menyetujui mahasiswa ini')"
			class="btn btn-success"><i class="fa fa-check-square"></i> Setujui</a>
	@else

		<a href="{{ route('val_ndc_proses', ['id' => $seminar->id, 'disetujui' => 0]) }}"
			onclick="return confirm('Anda ingin membatalkan persetujuan mahasiswa ini')"
			class="btn btn-danger"><i class="fa fa-ban"></i> Batalkan Persetujuan</a>

	@endif

</div>