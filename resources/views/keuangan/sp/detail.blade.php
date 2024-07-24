@extends('layouts.app')

@section('title','Detail Pembayaran Kuliah Praktek')

@section('topMenu')
	<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
		<li class="h-seperate"></li>
		<li><a>PEMBAYARAN SEMESTER PENDEK</a></li>
	</ul>
@endsection

@section('content')
	<div id="overlay"></div>

	<div id="content">
	
		<div class="row">
				
			<div class="col-md-12">
				<section class="panel">
					<header class="panel-heading">
						Detail pembayaran semester pendek
						<button onclick="window.history.back()" class="btn btn-success btn-xs pull-right">KEMBALI</button>
					</header>

					<div class="panel-body">

						<div class="col-md-12">

							{{ Rmt::AlertSuccess() }}
							{{ Rmt::AlertError() }}
							
							<?php $total_bayar = 0; ?>

							<div class="table-responsive">

								<!-- Data mahasiswa -->

		                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
		                                <tbody class="detail-mhs">
		                                    <tr>
		                                        <th width="130px">NIM</th>
		                                        <td>: {{ $mhs->nim }} </td>
		                                    </tr>
		                                    <tr>
		                                        <th width="130px">Nama</th>
		                                        <td>: {{ $mhs->nm_mhs }} </td>
		                                    </tr>
		                                    <tr>
		                                        <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
		                                    </tr>
	                                        <tr>
		                                        <th>Semester</th><td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai, Request::get('smt')) }}</td>
		                                    </tr>
		                                </tbody>
		                            </table>

					            <!-- History pembayaran -->
					            	<div class="col-md-12">
					            		<a href="{{ route('keu_cetak_sp_detail', ['id' => $mhs->id_mhs_reg]) }}?smt={{ Request::get('smt') }}&bayarsp=99"
											target="_blank" class="btn btn-primary btn-xs pull-right"><i class="fa fa-print"></i> Cetak</a>
							            <table class="table table-bordered">
							                <thead class="custom">
							                    <tr>
							                        <th colspan="6">History Pembayaran</th>
							                    </tr>
							                    <tr>
							                        <th width="10">No</th>
							                        <th>Tanggal Bayar</th>
							                        <th>Tempat Bayar</th>
							                        <th>Nama Bank</th>
							                        <th>Ket</th>
							                        <th>Jml bayar</th>
							                    </tr>
							                </thead>
							                <?php if ( $pembayaran->count() == 0 ) { ?>
							                    <tr><td colspan="6">Tidak ada history</td></tr>
							                <?php } else { ?>
							                    <tbody align="center">

							                    	<?php $loop = 1 ?>
							                        <?php foreach( $pembayaran as $pmb ) { ?>
							                            <tr>
							                            	<td>{{ $loop++ }}</td>
							                                <td><?= Carbon::parse($pmb->tgl_bayar)->format('d/m/Y') ?></td>
							                                <td>{{ $pmb->jenis_bayar }}</td>
							                                <td>{{ $pmb->nm_bank }}</td>
							                                <td>{{ $pmb->ket }}</td>
							                                <td align="left">Rp <?= Rmt::rupiah($pmb->jml_bayar) ?></td>
							                            </tr>
							                            <?php $total_bayar += $pmb->jml_bayar ?>
							                        <?php } ?>

							                        <tr>
							                            <td colspan="5" align="right" style="padding-right: 10px"><b>TOTAL</b></td>
							                            <td align="left"><b>Rp <?= Rmt::rupiah($total_bayar) ?></b></td>
							                        </tr>

							                    </tbody>
							                <?php } ?>
							            </table>
							        </div>
						        <!-- End -->

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