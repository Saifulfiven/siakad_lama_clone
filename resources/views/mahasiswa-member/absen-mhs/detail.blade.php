@extends('layouts.app')

@section('title','Absensi Mahasiswa')

@section('heading')
<script>
function timer(end_time, modul)
{
	// Timer
    // Set the date we're counting down to
    var countDownDate = new Date(end_time).getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

    // Get today's date and time
    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    var days_ = days == 0 ? '' : days + " hari ";
    var hours_ = hours == 0 ? '' : hours + " jam ";
    var minutes_ = minutes == 0 ? '' : minutes + " menit ";

      // Output the result in an element with id="demo"
      document.getElementById(modul).innerHTML = days_ + hours_
      + minutes_ + seconds + " detik ";
        
      // If the count down is over, write some text 
      if (distance < 0) {
        clearInterval(x);
        window.location.reload();
        document.getElementById(modul).innerHTML = "EXPIRED";
      }
    }, 1000);

}
</script>
@endsection

@section('content')

<div id="content" class="content-bimbingan">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					<a href="{{ route('mhs_absensi') }}" class="btn btn-circle btn-default"><i class="fa fa-arrow-left"></i> </a> &nbsp;
					Absensi Mahasiswa
				</header>

				<div class="panel-body">

						{{ Rmt::AlertError() }}
						{{ Rmt::AlertSuccess() }}

						<div class="col-md-6" style="padding-left: 0;">

							<?php
								$absen_ = DB::table('nilai')
													->where('id_mhs_reg', Sia::sessionMhs())
													->where('id_jdk', Request::get('id_jdk'))
													->first();

								$absened = [
									'1' => $absen_->a_1,
									'2' => $absen_->a_2,
									'3' => $absen_->a_3,
									'4' => $absen_->a_4,
									'5' => $absen_->a_5,
									'6' => $absen_->a_6,
									'7' => $absen_->a_7,
									'8' => $absen_->a_8,
									'9' => $absen_->a_9,
									'10' => $absen_->a_10,
									'11' => $absen_->a_11,
									'12' => $absen_->a_12,
									'13' => $absen_->a_13,
									'14' => $absen_->a_14
								];
							?>

							@if ( empty($absen) )
								<div class="alert alert-danger" style="padding: 15px !important">
									Belum ada absensi yang tersedia, tunggu hingga dosen membuka absensi
								</div>
							@else
								<div class="well {{ $absened[$absen->pertemuan_ke] == 1 ? 'bg-theme-inverse' : 'bg-theme' }}">
									<div class="widget-tile">
										<section>
											<h5>{{ $absen->kode_mk }} <br>
												<strong>{{ $absen->nm_mk }}</strong>
											</h5>
											<h2 id="timer-{{ $absen->id_jdk }}"><i class="fa fa-spinner fa-spin"></i></h2>
											<div class="progress progress-xs progress-white progress-over-tile">
													<div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="1" aria-valuemax="1"></div>
											</div>
											<label class="progress-label label-white" style="font-size: 12px">
												<?= $absen->dosen ?>
											</label>

										</section>
										
										@if ( $absened[$absen->pertemuan_ke] == 0 )
											<script>
												timer('{{ $absen->end_time }}', 'timer-{{ $absen->id_jdk }}');
											</script>
											<button type="button" class="btn btn-default absen" onclick="store('{{ $absen->pertemuan_ke }}', '{{ $absen->id_jdk }}')"><i class="fa fa-check-square-o"></i> Absen Sekarang</button>
										@else
											<script>
												document.getElementById( 'timer-{{ $absen->id_jdk }}').innerHTML = '<i class="fa fa-check-square-o"></i> Hadir';
											</script>
											<!-- <a class="btn btn-success"><i class="fa fa-check-square-o"></i> HADIR</a> -->
										@endif

										<div class="hold-icon"><i class="fa fa-check-square-o"></i></div>
									</div>
									<h5>Absensi untuk pertemuan ke-{{ $absen->pertemuan_ke }}</h5>
								</div>
							@endif
						
						</div>



						<div class="col-md-4">
							<table class="table table-bordered table-striped table-hover">
								<tr><th colspan="2">RIWAYAT ABSENSI ANDA</th></tr>
								<tr>
									<th>Pertemuan</th>
									<th>Absensi</th>
								</tr>

								@for( $i = 1; $i <= 14; $i++ )
									<tr>
										<td align="center">{{ $i }}</td>
										<td align="center">
											@if ( $absened[$i] == 1 )
												<i class="fa fa-check-square-o" style="color: green"></i>
											@else
												<i class="fa fa-times" style="color: red"></i>
											@endif
										</td>
									</tr>
								@endfor
							</table>
						</div>

				</div>

			</section>

		</div>

	</div>

</div>

@endsection

@section('registerscript')
<script>
	function store(pertemuan, id_jdk)
	{

		$('.absen').html('<i class="fa fa-spinner fa-spin"></i> Mohon Tunggu');
		$('.absen').attr('disabled','');
		var token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '{{ route('mhs_absensi_store') }}',
            data: { pertemuan: pertemuan, id_jdk: id_jdk },
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            success: function(result){
                window.location.reload();
            },
            error: function(err,data,msg){
            	$('.absen').html('<i class="fa fa-check-square-o"></i> Absen Sekarang');
							$('.absen').removeAttr('disabled');
                alert('Gagal menyimpan, coba muat ulang halaman lalu ulangi kembali');
                console.log(err);
            }
        })
	}
</script>
@endsection