@extends('layouts.app')

@section('title','Pengguna')

@section('content')
	<div id="overlay"></div>

	<div id="content">

		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						<b>PENGGUNA</b>
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertError() }}
							{{ Rmt::AlertSuccess() }}

							<table border="0" width="100%" style="margin-bottom: 10px">
								<tr>
									<td width="200px" style="padding-left: 3px">
										<select name="level" class="form-control mw-2" id="level">
	                                        <option value="all">Semua Level</option>
	                                        @foreach( Sia::listLevelUser() as $val )
	                                        	<option value="{{ $val }}" {{ Session::get('user_level') == $val ? 'selected':'' }}>{{ ucfirst($val) }}</option>
	                                        @endforeach
	                                    </select>
									</td>

									<td>
									</td>
									<td width="300px">
										<form action="" id="form-cari">
											<div class="input-group pull-right">
												<input type="text" class="form-control input-sm" name="search" value="{{ Request::get('search') }}">
												<div class="input-group-btn">
														<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
														<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
												</div>
											</div>
										</form>
									</td>
									<td width="110px">
										<a href="{{ route('users_add') }}" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
									</td>
								</tr>
							</table>
							
							<div class="table-responsive">
								<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
									<thead class="custom">
											<tr>
												<th width="20px">No.</th>
												<th>Nama</th>
												<th>Username</th>
												<th>Email</th>
												<th>Level</th>
												<th>Prodi</th>
												<th width="120px">Tools</th>
											</tr>
									</thead>
									<tbody align="center">
										@foreach( $users as $r )
											<tr>
												<td>{{ $loop->iteration - 1 + $users->firstItem() }}</td>
												<td align="left">{{ $r->nama }}</td>
												<td align="left">{{ $r->username }}</td>
												<td align="left">{{ $r->email }}</td>
												<td align="left">{{ $r->level }}</td>
												<td align="left">
													@foreach( $r->roles as $rol )
														<i class="fa fa-star"></i> {{ @$rol->prodi->jenjang .' '.@$rol->prodi->nm_prodi }}
													@endforeach
												</td>
												<td>
													<span class="tooltip-area">
														<a href="{{ route('users_relogin', ['id' => $r->id])}}" class="btn btn-primary btn-xs" title="Masuk"><i class="fa fa-sign-in"></i></a> &nbsp; &nbsp; 
														<a href="{{ route('users_edit', ['id' => $r->id])}}" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
														@if ( $r->username != Auth::user()->username )
															<a href="{{ route('users_delete', ['id' => $r->id])}}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
														@endif
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>

								@if ( $users->total() == 0 )
									&nbsp; Tidak ada data
								@endif

								@if ( $users->total() > 0 )
									<div class="pull-left">
										Jumlah data : {{ $users->total() }}
									</div>
								@endif

								<div class="pull-right"> 
									{{ $users->appends(['q' => Request::get('q')])->render() }}
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

@section('registerscript')
	<script>

	    $(document).ready(function(){

	        $('#reset-cari').click(function(){
	        	window.location.href= '{{ route('users') }}';
	        	
	        });

	        $('#level').change(function(){
	        	var lev = $(this).val();
	        	window.location.href= '{{ route('users') }}?level='+lev;
	        });
	    });

	</script>
@endsection