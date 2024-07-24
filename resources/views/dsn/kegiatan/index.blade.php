@extends('layouts.app')

@section('title','Kegiatan Dosen')

@section('content')
<div id="overlay"></div>

<div id="content">

	<div class="row">
			
		<div class="col-md-12">
			<section class="panel">
				<header class="panel-heading">
					Dokumen Kegiatan
				</header>

				<div class="panel-body">
					
					<div class="table-responsive">

						<table border="0" width="100%" style="margin-bottom: 10px;min-width: 800px">
							<tr>
								<td colspan="6"><p><b><i class="fa fa-filter"></i> Filter</b>
									@if ( Session::has('kegiatan') && !empty(Session::get('kegiatan')) )
										<span class="tooltip-area">
											<a href="{{ route('dsn_kegiatan_filter') }}?remove=1" class="btn btn-xs btn-warning btn-round" title="Hilangkan Filter">
												ON
											</a>
										</span>
									@endif
								</p></td>
							</tr>
							<tr>
								<td width="200">
									<select class="form-control input-sm mw-2" onchange="filter('kategori', this.value)">
										<option value="all">Semua Kategori</option>
										@foreach( Rmt::katKegiatanDosen() as $key => $val )
											<option value="{{ $key }}" {{ Session::get('kegiatan.kategori') == $key ? 'selected':'' }}>{{ $val }}</option>
										@endforeach
									</select>
								</td>
								<td width="10"></td>
								<td width="100">
									<select class="form-control input-sm mw-2" onchange="filter('smt', this.value)">
										<option value="all">Semester</option>
										@foreach( $semester as $smt )
											<option value="{{ $smt->smt }}" {{ Session::get('kegiatan.smt') == $smt->smt ? 'selected':'' }}>{{ $smt->smt }}</option>
										@endforeach
									</select>
								</td>
								<td width="10"></td>
								<td width="100">
									<select class="form-control input-sm mw-2" onchange="filter('tahun', this.value)">
										<option value="all">Tahun</option>
										@foreach( $tahun as $thn )
											<option value="{{ $thn->tahun }}" {{ Session::get('kegiatan.tahun') == $thn->tahun ? 'selected':'' }}>{{ $thn->tahun }}</option>
										@endforeach
									</select>
								</td>
								<td></td>
								<td width="250px">
									<form action="{{ route('dsn_kegiatan_filter') }}" id="form-cari">
										<input type="hidden" name="filter_cari" value="1">
										<div class="input-group pull-right">
											<input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('kegiatan.cari') }}" placeholder="Cari nama kegiatan">
											<div class="input-group-btn">
												<button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
												<button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
											</div>
										</div>
									</form>
								</td>
								<td width="210">
									<button class="btn btn-primary btn-sm add-dokumen pull-right"><i class="fa fa-plus"></i> Tambahkan Dokumen Kegiatan</button>
								</td>
							</tr>
						</table>

						<hr>

						<table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="dataTable">
							<thead class="custom">
								<tr>
									<th width="20px">No.</th>
									<th>Nama Kegiatan</th>
									<th>Kategori</th>
									<th>File</th>
									<th>Tanggal</th>
									<th>Tahun</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								@foreach( $kegiatan as $kg )
									<tr>
										<td align="center">{{ $loop->iteration - 1 + $kegiatan->firstItem() }}</td>
										<td>{{ $kg->nama_kegiatan }}</td>
										<td>{{ Rmt::katKegiatanDosen($kg->id_kategori) }}</td>
										<td>
											<div class="icon-resources">
                                                <?php $icon = Rmt::icon($kg->file); ?>
                                                <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                                {{ str_limit($kg->file, 20)}}
                                            </div>
										</td>
										<td align="center">{{ Carbon::parse($kg->tgl_kegiatan)->format('d/m/Y') }}</td>
										<td align="center">{{ $kg->tahun }}</td>
										<td align="center">
                      {{-- test --}}
											<span class="tooltip-area">
												<a href="{{ route('dsn_kegiatan_viewdok', ['id' => $kg->id, 'id_dosen' => $dosen->id, 'file' => $kg->file]) }}" class="btn btn-sm btn-primary" target="_blank" title="Lihat dokumen">
														<i class="fa fa-search-plus"></i>
												</a> &nbsp;

												<a href="javascript:void(0)" class="btn btn-sm btn-warning" onclick="edit('{{ $kg->id }}')" title="Ubah">
														<i class="fa fa-pencil"></i>
												</a> &nbsp;

												<a href="" class="btn btn-sm btn-default" target="_blank" title="Cetak SK">
														<i class="fa fa-print"></i>
												</a> &nbsp;

												<a href="{{ route('dsn_kegiatan_delete', ['id' => $kg->id]) }}" 
													onclick="return confirm('Anda yakin ingin menghapus data ini')"
													class="btn btn-sm btn-danger" title="Hapus">
														<i class="fa fa-times"></i>
												</a>
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>

						@if ( $kegiatan->total() == 0 )
							&nbsp; Tidak ada data
						@endif

						@if ( $kegiatan->total() > 0 )
							<div class="pull-left">
								Jumlah data : {{ $kegiatan->total() }}
							</div>
						@endif

						<div class="pull-right"> 
							{{ $kegiatan->render() }}
						</div>

						@if ( $kegiatan->total() > 0 )
							<div class="clearfix"></div>
							<hr>
							<a href="{{ route('dsn_kegiatan_download', ['id_dosen' => $dosen->id]) }}" class="btn btn-sm btn-success" target="_blank"><i class="fa fa-download"></i> Download Semua Data</a>
						@endif
					</div>
				</div>
			</section>
		</div>
		
	</div>
	<!-- //content > row-->
		
</div>
<!-- //content-->

<div id="modal-add" class="modal fade" data-width="600" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Tambah Dokumen Kegiatan</h4>
    </div>
    <div class="modal-body">

        <div class="col-md-12">
            <form action="{{ route('dsn_kegiatan_store') }}" class="form-horizontal" data-collabel="3" data-alignlabel="left" id="form-add" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group">
                    <label class="control-label">Kategori <span>*</span></label>
                    <div>
                        <select name="kategori" class="form-control">
                            <option value="">Pilih Kategori</option>
                            @foreach( Rmt::katKegiatanDosen() as $key => $val )
                                <option value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Nama Kegiatan <span>*</span></label>
                    <div>
                        <input type="text" name="nama_kegiatan" class="form-control" required="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Tanggal Kegiatan <span>*</span></label>
                    <div>
                        <input type="date" name="tanggal_kegiatan" class="form-control mw-2" required="">
                    </div>
                </div>

                <div class="form-group">
				    <label class="control-label">Semester Akademik <span>*</span></label>
				    <div>
				        <select name="smt" class="form-control mw-2">
				            @foreach( Sia::listSemester('filter') as $res )
				                <option value="{{ $res->id_smt }}">{{ $res->nm_smt }}</option>
				            @endforeach
				        </select>
				    </div>
				</div>

                <div class="form-group">
                    <label class="control-label" for="lampiran" >Lampiran File</label>
                    <div>
                        <input type="file" name="file" id="lampiran" class="form-control">
                    </div>
                </div>

                <div class="form-group offset" style="margin: 20px 0 30px -10px">

                    <div>
                        <button type="submit" id="btn-submit-add" class="btn btn-theme btn-sm"><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; 
                        <button type="reset" data-dismiss="modal" class="btn btn-sm"><i class="fa fa-times"></i>  Batal</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<div id="modal-edit" class="modal fade" data-width="600" tabindex="-1" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Ubah Materi</h4>
    </div>
    <div class="modal-body">
        <div class="col-md-12">
            <form enctype="multipart/form-data" action="{{ route('dsn_kegiatan_update') }}" role="form" id="form-edit" method="post">
            
            </form>
        </div>
    </div>
</div>


@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

<script>
	$(document).ready(function() {

		if(!window.matchMedia("(max-width: 767px)").matches){
			
	        $('#nav-mini').trigger('click');
	    }

		$('#reset-cari').click(function(){
        	var q = $('input[name="cari"]').val();
        	$('input[name="cari"]').val('');
        	if ( q.length > 0 ) {
        		$('#form-cari').submit();
        	}
        	
        });

        @if ( Session::has('success') )
            showSuccess('{{ Session::get('success') }}');
        @endif

        $('.add-dokumen').click(() => {
            $('#modal-add').modal('show');
        });

        let options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');
                $("#btn-submit-add").attr('disabled','');
                $("#btn-submit-add").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2('add', data.msg);
                } else {
                    window.location.reload();
                }
            },
            error: function(data, status, message)
            {
                let respon = parseObj(data.responseJSON);
                let pesan = '';
                for ( let i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                $("#close-error").show();
                showMessage2('add', pesan);
            }
        }; 

        $('#form-add').ajaxForm(options);


        $.ajaxSetup ({
            // Disable caching of AJAX responses
            cache: false
        });

        // Update ajax
        let options2 = {
            beforeSend: function() 
            {

                $('body').modalmanager('loading');
                $("#btn-submit-edit").attr('disabled','');
                $("#btn-submit-edit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2('edit', data.msg);
                } else {
                    window.location.reload();
                }
            },
            error: function(data, status, message)
            {
                let respon = parseObj(data.responseJSON);
                let pesan = '';
                for ( let i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                $("#close-error").show();
                showMessage2('edit', pesan);
            }
        }; 

        $('#form-edit').ajaxForm(options2);
	    
	});

    function edit(id)
    {
        let $modal = $('#modal-edit');

        $('body').modalmanager('loading');
          setTimeout(function(){
            $modal.find("#form-edit").load('{{ route('dsn_kegiatan_edit') }}/'+id, '', function(){
              $modal.modal({backdrop: 'static', keyboard: false});
            });
        }, 500);
    }

    function filter(modul, value)
    {
        window.location.href = '{{ route('dsn_kegiatan_filter') }}?modul='+modul+'&val='+value;
    }
</script>
@endsection