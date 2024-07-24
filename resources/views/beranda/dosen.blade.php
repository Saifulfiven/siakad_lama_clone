<div class="row">
	<?php if ( Hash::check('12345678', Auth::user()->password) ) { ?>
		<div class="col-md-12">
			<div class="well bg-theme">
				Anda masih memakai password default, untuk alasan keamanan
				kami sangat menganjurkan anda untuk mengganti password. 
				<a href="{{ route('dsn_profil') }}" style="color: #fff;"><u>Ganti Password</u></a>
			</div>
		</div> 
	<?php }	?>

	<div class="col-md-6">

		<?php
			$pengumuman = DB::table('x_pengumuman')->where('kategori', 'dosen')
				->orderBy('created_at','desc')
				->take(11)->get();

			$news = [];
			foreach( $pengumuman as $pe ) {
				$news = ['tgl' => $pe->created_at, 'judul' => $pe->judul, 'konten' => $pe->konten];
				break;
			}
			
		?>
		@if ( count($news) > 0 )

			<div class="well bg-primary">
				<div class="widget-tile">
					<section>
						<h5><strong>Pengumuman Terbaru</strong></h5><hr>
						<h5>{{ $news['judul'] }}</h5>
						{!! $news['konten'] !!}
					</section>
				</div>
			</div>

		@endif

		<section class="panel">
			<header class="panel-heading">
				<h2><strong>Pengumuman</strong> teratas</h2>
			</header>
			<ul class="list-group">
				@foreach( $pengumuman as $pe )
					<?php $no = $loop->iteration; ?>
					<?php if ( $no == 1 ) continue; ?>
					<li class="list-group-item">
						<a href="javascript:;" class="detail-pengumuman" data-judul="{{ $pe->judul }}" data-konten="{{ $pe->konten }}">
							{{ Carbon::parse($pe->created_at)->format('d-m-Y') }} - 
							{{ $pe->judul }}
						</a>
					</li>
				@endforeach

				@if ( count($pengumuman) == 1 )
					<li class="list-group-item">Belum ada data</li>
				@endif
			</ul>
		</section>

	</div>

	<div class="col-md-6">
		<div class="well bg-theme-inverse">
			<div class="widget-tile">
				<section>
					<h5><strong>Download Panduan Siakad</strong></h5><hr>
					<a href="https://drive.google.com/drive/folders/1DSJNjErRQ5mist3LsklRHiG-WgXOpEBX?usp=sharing" class="btn btn-default" target="_blank">DIREKTORI PANDUAN SIAKAD</a>
				</section>
			</div>
		</div>

		<section class="panel">
			<header class="panel-heading no-borders xs">
				<h2><b>Kalender</b> Akademik</h2>
				<label class="color">Periode  <strong> {{ substr(Sia::sessionPeriode('nama'),0,9) }}</strong></label>
			</header>		
			<div class="panel-body">
				<div id="kalender"></div>
			</div>	
		</section>
	</div>
</div>

<div id="modal-pengumuman" class="modal fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="judul-pengumuman">Detail Pengumuman</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
    	<p id="konten-pengumuman">Konten Pengumuman</p>
    </div>
    <!-- //modal-body-->
    <div class="modal-footer">
    	<center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-primary">Tutup</button>
        </center>
    </div>
</div>