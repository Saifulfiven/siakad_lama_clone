@extends('layouts.app')

@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a>VALIDASI DATA</a></li>
	</ul>
@endsection

@section('content')

	<div id="content">
	
		<div class="row">
		
			<div class="col-md-12" >
				<section class="panel" style="min-height: 400px">

					<div class="panel-body">
						<div class="tabbable">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#nilai" data-toggle="tab">Nilai belum masuk</a></li>
								<li><a href="#transfer" data-toggle="tab">Mahasiswa Transfer</a></li>
								<li><a href="#umur" data-toggle="tab">Umur</a></li>
							</ul>
							<div class="tab-content">
								
								<div class="tab-pane fade in active" id="nilai">
									@include('beranda.validasi.nilai')
								</div>

								<div class="tab-pane fade" id="transfer">
									@include('beranda.validasi.mahasiswa-transfer')
								</div>
								
								<div class="tab-pane fade" id="umur">
									@include('beranda.validasi.umur')
								</div>

							</div>
						</div>
					</div>
				</section>
			</div>
				
		</div>
		<!-- //content > row-->
			
	</div>
	<!-- //content-->
@endsection