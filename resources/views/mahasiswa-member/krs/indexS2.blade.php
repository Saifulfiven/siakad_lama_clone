@extends('layouts.app')

@section('title','Registrasi KRS')

@section('content')

<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel" style="min-height: 550px">
				<header class="panel-heading">
	              REGISTRASI KRS {{ $krs_stat['jenis'] == 2 ? 'SEMESTER PENDEK' : '' }}
	            </header>
				<div class="panel-body">

					@if ( empty($msg) )

						@if ( $krs_stat['status'] == 'unlock' )

							@if ( Sia::sessionMhs('prodi') != 61101 && Sia::sessionMhs('jenis_daftar') != 1 )

								<div class="alert bg-danger" style="padding: 10px !important">
									<b>Hai {{ Sia::sessionMhs('nama') }},</b><br>
										Mohon Maaf atas ketidaknyamanan ini, KRS Online baru bisa dipergunakan
									 oleh Mahasiswa Reguler (Bukan Transfer) angkatan 2018 ke atas.
									 Namun anda tetap bisa melihat KRS-an anda pada semester berjalan di halaman ini. 
									<p>Peringatan ini akan hilang setelah masa KRS berakhir.</p>
								</div>

								@include('mahasiswa-member.krs.krs')

							@else

								<div class="col-md-12">
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
											<td>: {{ $mhs->prodi->nm_prodi.' ('.$mhs->prodi->jenjang.')' }}</td>
										</tr>
										@if ( !empty($mhs->id_konsentrasi) )
											<tr>
												<td>Konsentrasi</td>
												<td>: {{ $mhs->konsentrasi->nm_konsentrasi }}</td>
											</tr>
										@endif
										<?php $posisi_smstr = Sia::posisiSemesterMhs($mhs->semester_mulai) ?>
										<tr>
											<td>Semester</td>
											<td>: {{ $posisi_smstr }}</td>
										</tr>
									</table>
									<br>
									@if ( $posisi_smstr > 12 && $posisi_smstr < 14 )
										<div class="alert alert-warning">
											Saat ini anda telah melewati semester 12 (Masa studi 6 Tahun). 
											Maksimal Masa studi menurut peraturan DIKTI adalah 7 Tahun, 
											Mohon segera menyelesaikan semua matakuliah anda.
										</div>
									@elseif ( $posisi_smstr > 14 )
										<div class="alert alert-danger">
											Masa Studi Anda telah lewat sehingga pengisian KRS dinonaktifkan. Silahkan bicarakan dengan ketua jurusan anda.
										</div>
									@endif
								</div>

								@if ( $posisi_smstr <= 14 )
								<div class="col-md-6">
									<h4 id="title-form"><b>Matakuliah tersedia</b></h4>
									<br>
									<div class="table-responsive">
										<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-hover" style="font-size: 12px">
											<thead class="custom">
												<tr>
													<th width="20px">No.</th>
													<th>Matakuliah</th>
													<th>Smstr</th>
													<th>SKS</th>
													<th>Tools</th>
												</tr>
											</thead>
											<tbody>
												<?php $no = 1 ?>
												<?php $mk_arr = [] ?>
												<?php $nm_mk = '' ?>
												<?php $sks = $sks_diambil ?>

												@if ( count($matakuliah) > 0 )

													@foreach( $matakuliah as $r )

														<?php

															if ( strtolower(trim($r->nm_mk)) == $nm_mk ) continue;
															
															if ( $no == 1 ) {
																$smt = $r->smt;
															}

														?>
														<tr>
															<td align="center">{{ $no++ }}</td>
															<td>{{ $r->nm_mk }} 
																<?= $r->asal == 'error' ? '<small style="color: red">ulang</small>': '' ?>
																<?= $r->jenis_mk == 'B' ? '<br><small style="color: #0090d9">Matakuliah pilihan</small>': '' ?>
															</td>
															<td align="center">{{ $r->smt }}</td>
															<td align="center">{{ $r->sks_mk }}</td>
															<td align="center">
																<a href="javascript:;"
																	onclick="ambil('{{ $r->id_mkur }}', '{{ Sia::sessionMhs() }}')"
																	id="{{ $r->id_mkur }}"
																	class="btn btn-primary btn-xs">Ambil</a>
															</td>

														</tr>

														<?php $mk_arr[] = ['id_mkur' => $r->id_mkur, 'sks' => $r->sks_mk]; ?>

														<?php $nm_mk = strtolower(trim($r->nm_mk)) ?>

														<?php $sks += $r->sks_mk ?>

													@endforeach

												@endif

												<?php $kode_mk_ = '' ?>

												@if ( count($mk_error) > 0 )
													@if ( $sks < 18 )

														@foreach( $mk_error as $r )
															
															<?php

																if ( $kode_mk_ == $r->kode_mk ) continue;

																$free_sks = 18 - $sks;

																// if ( $free_sks <= 0 || $r->sks_mk + $sks > 18 ) continue;

																$sks += $r->sks_mk;

															?>
															<tr>
																<td align="center">{{ $no++ }}</td>
																<td>{{ $r->nm_mk }}
																	<?= $r->asal == 'error' ? '<small style="color: red"> ulang</small>': '' ?>
																	<?= $r->jenis_mk == 'B' ? '<small style="green: red"> pilihan</small>': '' ?>
																</td>
																<td align="center">{{ $r->smt }}</td>
																<td align="center">{{ $r->sks_mk }}</td>
																<td align="center">
																	<a href="javascript:;"
																		onclick="ambil('{{ $r->id_mkur }}', '{{ Sia::sessionMhs() }}')"
																		id="{{ $r->id_mkur }}"
																		class="btn btn-primary btn-xs">Ambil</a>
																</td>
															</tr>

															<?php $kode_mk_ = $r->kode_mk;  ?>

														@endforeach

													@endif

												@endif

												@if ( count($matakuliah) != 0 )
													<tr><td colspan="5" align="center">
															<form action="{{ route('mhs_krs_store_tmp_arr') }}" id="form-get-all" method="post">
																{{ csrf_field() }}
																<input type="hidden" name="matakuliah" value="{{ json_encode($mk_arr) }}">
																<button class="btn btn-primary btn-xs"><i class="fa fa-save"></i> AMBIL SEMUA MATAKULIAH</button>
															</form>
														</td>
													</tr>
												@endif

												@if ( count($mk_arr) == 0 )
													<tr><td colspan="5">Tidak ada matakuliah yang bisa ditampilkan</td></tr>
												@endif
											</tbody>
										</table>

									</div>
								</div>

								<div class="col-md-6">

									<h4 id="title-form"><b>Matakuliah diambil</b></h4>
									<br>
									<div class="table-responsive">
										<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-hover" style="font-size: 12px">
											<thead class="custom">
												<tr>
													<th width="20px">No.</th>
													<th>Matakuliah</th>
													<th>Smstr</th>
													<th>SKS</th>
													<th>Tools</th>
												</tr>
											</thead>
											<tbody>
												@if ( count($matakuliah_diambil) > 0 )
													<?php $total_sks = 0 ?>
													@foreach( $matakuliah_diambil as $r )
														<tr>
															<td align="center">{{ $loop->iteration }}</td>
															<td>{{ $r->nm_mk }}
																<?= $r->jenis_mk == 'B' ? '<br><small style="color: #0090d9">Matakuliah pilihan</small>': '' ?>
															</td>
															<td align="center">{{ $r->smt }}</td>
															<td align="center">{{ $r->sks_mk }}</td>
															<td align="center">
																<span class="tooltip-area">
																	<a href="{{ route('mhs_krs_delete_tmp', ['id' => $r->id]) }}" title="Tidak jadi" onclick="return confirm('Anda ingin membatalkan mengambil matakuliah ini.?')" class="btn btn-danger btn-xs">Batal</a>
																</span>
															</td>
														</tr>
														<?php $total_sks += $r->sks_mk ?>
													@endforeach
													<tr>
														<td colspan="3" align="center"><strong>Total SKS</strong></td>
														<td align="center"><strong>{{ $total_sks }}</strong></td>
														<td></td>
													</tr>
												@else
													<tr>
														<td colspan="6">Belum ada data</td>
													</tr>
												@endif
											</tbody>
										</table>

									</div>

									@if (count($matakuliah_diambil) > 0)
									<hr>
										<center>
											<a href="javascript:;" onclick="store()" id="btn-store" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> SIMPAN KRS</a>
											<p><small>Setelah menyimpan anda tidak bisa lagi mengubahnya</small></p>
										</center>
									@endif
				                </div>
				                @endif
				            
				            @endif

		                @elseif ( $krs_stat['status'] == 'lock' )
		                	
		                	@include('mahasiswa-member.krs.krs')

		                @else
		                	<div class="alert alert-danger">
		                		Anda belum menyelesaikan pembayaran pada periode {{ Sia::sessionPeriode('nama') }}. Jika anda merasa telah melakukan pembayaran mohon tunggu hingga bagian keuangan memasukkan/menvalidasi data pembayaran anda.
		                	</div> 
		                @endif

		            @else
		            	<div class="alert alert-info">
		            		{{ $msg }}
		            	</div>
		            @endif
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
$(function(){
   
   	@if ( $krs_stat == 'unlock' ) 
	    if (window.matchMedia("(min-width: 990px)").matches) {
			$('#nav-mini').trigger('click');
	    }
    @endif

    @if ( Session::has('success') )
        $.notific8('{{ Session::get('success') }}',{ life:5000,horizontalEdge:"top", theme:"success" ,heading:" INFO..."});
    @endif

    @if ( Session::has('error') )
        $.notific8('{{ Session::get('error') }}',{ life:5000,horizontalEdge:"top", theme:"danger" ,heading:" INFO..."});
    @endif
});

function ambil(id_mkur,id_mhs_reg)
{

	$('#'+id_mkur).attr('disabled','');
	$('#'+id_mkur).html('<i class="fa fa-spinner fa-spin"></i>');
	$.ajax({
		url: '{{ route('mhs_krs_store_tmp') }}',
		data: { id_mkur: id_mkur, id_mhs_reg: id_mhs_reg },
		success: function(result){
			if ( result.error == 1 ) {
				alert(result.msg);
				$('#'+id_mkur).removeAttr('disabled');
				$('#'+id_mkur).html('Ambil');
			} else {
				window.location.reload();
			}
		},
		error: function(data,status,msg){
			$('#'+id_mkur).removeAttr('disabled');
			$('#'+id_mkur).html('Ambil');
			alert(msg.error);
		}
	});
}

function store()
{
	var konfirmasi = confirm('Anda tidak akan bisa lagi mengubah setelah menyimpan. Lanjutkan?');

	if ( konfirmasi ) {
		var btn = $('#btn-store');
		btn.attr('disabled','');
		btn.html('<i class="fa fa-spinner fa-spin"></i> MENYIMPAN...');
		$.ajax({
			url: '{{ route('mhs_krs_store') }}',
			data : {jenis: '{{ $krs_stat['jenis'] }}'},
			success: function(result){
				if ( result.error == 1 ) {
					alert(result.msg);
					btn.removeAttr('disabled');
					btn.html('<i class="fa fa-save"></i> SIMPAN');
				} else {
					window.location.reload();
				}
			},
			error: function(data,status,msg){
				btn.removeAttr('disabled');
				btn.html('<i class="fa fa-save"></i> SIMPAN');
				var respon = parseObj(data.responseJSON);
	            var pesan = '';
	            for ( i = 0; i < respon.length; i++ ){
	                pesan += "- "+respon[i]+"<br>";
	            }
	            if ( pesan == '' ) {
	                pesan = message;
	            }
	            alert(pesan);
			}
		});
	}
}

</script>
@endsection