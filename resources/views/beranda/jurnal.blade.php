<?php

$jumlah_mahasiswa = App\Mahasiswareg::where('semester_mulai','>', 20171)
					->where('id_prodi', 61101)
					->count();

$jml_setor = App\Mahasiswareg::where('semester_mulai','>', 20171)
				->where(function($q){
					$q->where('jurnal_file','<>','')
						->whereNotNull('jurnal_file')
						->where('jurnal_file','<>', '0')
						->orWhere('jurnal_approved', '1');
				})
				->where('id_prodi', 61101)
				->count();

$jml_approved = App\Mahasiswareg::where('jurnal_approved', '1')
				->where('semester_mulai','>', 20171)
				->where('id_prodi', 61101)
				->count();
$belum_disetujui = $jml_setor - $jml_approved;

?>
<div class="row">
	<div class="col-md-4">
		<div class="well bg-theme">
			<div class="widget-tile">
				<section>
					@if ( $belum_disetujui == 0 )
						<h5><strong><i class="fa fa-check"></i></strong>  </h5>
						<h2>SEMUA TELAH DISETUJUI</h2>
					@else
						<h5><strong>{{ $belum_disetujui }} JURNAL</strong>  </h5>
						<h2>BELUM DISETUJUI</h2>
					@endif
					<div class="progress progress-xs progress-white progress-over-tile">
							<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="{{ $jml_approved }}" aria-valuemax="{{ $jml_setor }}"></div>
					</div>
					<label class="progress-label label-white" style="font-size: 12px">
						@if ( $belum_disetujui != 0 )
							<a href="{{ route('mahasiswa') }}?jurnal=unapproved" style="color: #fff;border-bottom: 1px solid #fff">Klik di sini untuk melihat</a>
						@else
							&nbsp;
						@endif
					</label>
				</section>
				<div class="hold-icon"><i class="fa fa-bookmark-o"></i></div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="well bg-primary">
			<div class="widget-tile">
				<section>
					<h5><strong>Jumlah Mahasiswa</strong> Wajib Jurnal </h5>
					<h2>{{ $jumlah_mahasiswa }}</h2>
					<div class="progress progress-xs progress-white progress-over-tile">
						<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="{{ $jml_setor }}" aria-valuemax="{{ $jumlah_mahasiswa }}"></div>
					</div>
					<label class="progress-label label-white" style="font-size: 12px"> Sebanyak {{ $jml_setor }} mahasiswa telah menyetor </label>
				</section>
				<div class="hold-icon"><i class="fa fa-bar-chart-o"></i></div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="well bg-inverse">
				<div class="widget-tile">
					<section>
							<h5><strong>Jumlah Mahasiswa</strong> Menyetor Jurnal </h5>
							<h2>{{ $jml_setor }}</h2>
							<div class="progress progress-xs progress-white progress-over-tile">
									<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="{{ $jml_approved }}" aria-valuemax="{{ $jml_setor }}"></div>
							</div>
							<label class="progress-label label-white" style="font-size: 12px">
								<a href="{{ route('mahasiswa') }}?jurnal=approved" style="color: #fff;border-bottom: 1px solid #fff">Sebanyak {{ $jml_approved }} jurnal TELAH DISETUJUI</a>
							</label>
					</section>
					<div class="hold-icon"><i class="fa fa-bar-chart-o"></i></div>
				</div>
		</div>
	</div>

</div>