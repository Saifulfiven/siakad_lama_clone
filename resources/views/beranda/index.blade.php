@extends('layouts.app')

@section('heading')
<link type="text/css" rel="stylesheet" href="{{ url('resources') }}/assets/css/ui-lightness/jquery-ui-1.10.4.custom.css" />

<style type="text/css">
	h2 {
		font-size: 16px !important;
	}
</style>
@endsection

@section('content')
	
<div id="content">

	<?php
		switch( Auth::user()->level ) {
			case 'admin':
            case 'akademik':
            case 'keuangan':
            case 'ketua':
            case 'personalia':
            case 'cs':
            case 'ketua 1': ?>
				@include('beranda.dashboard')
			<?php break;
			case 'jurnal': ?>
				@include('beranda.jurnal')
			<?php break;

			case 'mahasiswa': ?>
				@include('beranda.mahasiswa')
			<?php break;

			case 'dosen': ?>
				@include('beranda.dosen')
			<?php break;

			case 'ndc': ?>
				@include('beranda.ndc')
			<?php break;

			default:
				echo '<h5>Hai, silahkan pilih menu yang ada pada samping kiri layar</h5>';
			break;
		}
	?>
		
</div>
<!-- //content-->
@endsection

@section('registerscript')
	<script src="{{ url('resources') }}/assets/js/jquery.PrintArea.js" type="text/javascript"></script>

	<!-- Library Chart-->
	<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/chart/chart.js"></script>
	
	<script>

		$(function(){
			/* Cetak keadaan mahasiswa */
			$('#cetak-keadaan-mhs').click(function(){
				var div = $(this);
				div.html('<i class="fa fa-spinner fa-spin"></i> Mencetak...');
				div.attr('disabled','');
				$('#konten-keadaan .table-responsive').printArea();
				setTimeout(function(){
					div.html('<i class="fa fa-print"></i> Cetak');
					div.removeAttr('disabled');
				}, 2000);
			});

			$('#cetak-keadaan-dosen').click(function(){
				var div = $(this);
				div.html('<i class="fa fa-spinner fa-spin"></i> Mencetak...');
				div.attr('disabled','');
				$('#konten-keadaan-dosen .table-responsive').printArea();
				setTimeout(function(){
					div.html('<i class="fa fa-print"></i> Cetak');
					div.removeAttr('disabled');
				}, 2000);
			});

		
			//////////     SPARKLINE CHART     //////////
			$('.sparkline[data-type="bar"]').each(function () {
					var thisSpark=$(this) , $data = $(this).data();
					$data.barColor = $.fillColor( thisSpark ) || "#6CC3A0";
					$data.minSpotColor = false;
					thisSpark.sparkline($data.data || "html", $data);
			});	
			$('.sparkline[data-type="pie"]').each(function () {
					var thisSpark=$(this) , $data = $(this).data();
					$data.barColor = $.fillColor( thisSpark ) || "#6CC3A0";
					$data.minSpotColor = false;
					thisSpark.sparkline($data.data || "html", $data);
			});	
			var sparklineCreate = function($resize) {
				$('.sparkline[data-type="line"]').each(function () {
						var thisSpark=$(this) , $data = $(this).data();
						$data.lineColor = $.fillColor( thisSpark ) || "#F37864";
						$data.fillColor = $.rgbaColor( ($.fillColor( thisSpark ) || "#F37864") , 0.1 );
						$data.width = $data.width || "100%";
						$data.lineWidth = $data.lineWidth || 3;
						$(this).sparkline($data.data || "html", $data);
						if($data.compositeForm){
							var thisComposite=$($data.compositeForm);
							$comData=thisComposite.data();
							$comData.composite = true;
							$comData.lineWidth = $data.lineWidth || 3;
							$comData.lineColor = $.fillColor( thisComposite ) || "#F37864";
							$comData.fillColor = $.rgbaColor( ($.fillColor( thisComposite ) || "#6CC3A0") , 0.1 );
							$(this).sparkline($comData.data , $comData);
						}
				});
			}
		});

	</script>

	@if ( Sia::mhs() || Sia::dsn() )
		<script>
			$(function(){
				getKalender();

				$('.detail-pengumuman').click(function(){
					var judul = $(this).data('judul');
					var konten = $(this).data('konten');
					$('#judul-pengumuman').html(judul);
					$('#konten-pengumuman').html(konten);
					$('#modal-pengumuman').modal('show');
				});



			});

			function getKalender(kategori=null)
			{
				$('#kalender').html('<i class="fa fa-spinner fa-spin"></i> Memuat Kalender...');
				$.ajax({
		    		url: '{{ route('mhs_kalender') }}',
		    		data : {kategori : kategori},
		    		success: function(result){
		    			$('#kalender').html(result);
		    		},
		    		error: function(data,status,msg){
		    			$('#kalender').html('Gagal mengambil kalender. Silahkan muat ulang halaman');
		    		}
		    	});
			}

		</script>
	@endif
@endsection