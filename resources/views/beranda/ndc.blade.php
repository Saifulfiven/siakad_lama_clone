<?php

$menunggu = DB::table('seminar_pendaftaran as sp')
            ->join('seminar_file as sf', 'sp.id', 'sf.id_seminar')
            ->where('sp.jenis', 'H')
            ->where('sp.id_smt', Sia::sessionPeriode())
            ->where('sf.jenis_file', 'olah-data')
            ->where('sp.validasi_ndc', '0')
            ->groupBy('sp.id_mhs_reg')
            ->count();

$disetujui = DB::table('seminar_pendaftaran as sp')
            ->join('seminar_file as sf', 'sp.id', 'sf.id_seminar')
            ->where('sp.jenis', 'H')
            ->where('sp.id_smt', Sia::sessionPeriode())
            ->where('sf.jenis_file', 'olah-data')
            ->where('sp.validasi_ndc', '1')
            ->groupBy('sp.id_mhs_reg')
            ->count();

?>
<div class="row">
	<div class="col-md-6">
		<div class="well {{ $menunggu == 0 ? '': 'bg-theme' }}">
			<div class="widget-tile">
				<section>

					<h5><strong>Menunggu Persetujuan</strong> {{ Sia::sessionPeriode('nama') }}</h5>
					<h2 style="font-size: 30px !important"> {{ $menunggu }}</h2>

					<div class="progress progress-xs progress-white progress-over-tile">
							<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="{{ $menunggu }}" aria-valuemax="{{ $menunggu }}"></div>
					</div>
					<label class="progress-label label-white" style="font-size: 12px">
						@if ( $menunggu != 0 )
							<a href="{{ route('val_ndc_filter') }}?modul=status&val=0" style="color: #fff;border-bottom: 1px solid #fff">Klik di sini untuk melihat</a>
						@else
							&nbsp;
						@endif
					</label>
				</section>
				<div class="hold-icon"><i class="fa fa-refresh"></i></div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="well bg-primary">
			<div class="widget-tile">
				<section>

					<h5><strong>TELAH DISETUJUI</strong> {{ Sia::sessionPeriode('nama') }} </h5>
					<h2 style="font-size: 30px !important"> {{ $disetujui }}</h2>

					<div class="progress progress-xs progress-white progress-over-tile">
							<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="{{ $disetujui }}" aria-valuemax="{{ $disetujui }}"></div>
					</div>
					<label class="progress-label label-white" style="font-size: 12px">
						@if ( $disetujui != 0 )
							<a href="{{ route('val_ndc_filter') }}?modul=status&val=1" style="color: #fff;border-bottom: 1px solid #fff">Klik di sini untuk melihat</a>
						@else
							&nbsp;
						@endif
					</label>
				</section>
				<div class="hold-icon"><i class="fa fa-check-square-o"></i></div>
			</div>
		</div>
	</div>

</div>