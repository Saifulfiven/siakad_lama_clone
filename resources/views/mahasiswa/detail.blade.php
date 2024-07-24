@extends('layouts.app')

@section('title','Detail Mahasiswa')
@php
    $mhs = $data['mhs'];
    $id_mahasiswa = $data['id_mahasiswa'];
    $nim = $data['nim'];
@endphp
@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel">
        <header class="panel-heading">
          Mahasiswa
        </header>
        
        <div class="panel-body" style="padding: 3px 3px">
            
            @include('mahasiswa.link-cepat')

            <div class="col-md-9">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                        @if ( Sia::akademik() )
                            <a href="{{ route('mahasiswa_add') }}" style="margin: 3px 3px" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> TAMBAH</a>
                            <a href="{{ route('mahasiswa_edit', ['id' => $mhs->id]) }}" style="margin: 3px 3px" class="btn btn-warning btn-sm pull-right"><i class="fa fa-pencil"></i> UBAH</a>
                        @endif
                    </div>
                </div>
                
                {{ Rmt::AlertSuccess() }}
                {{ Rmt::AlertError() }}
                {{ Rmt::AlertErrors($errors) }}

                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th width="130px">NIM</th><td>: {{ $nim }}</td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Nama</th><td>: {{ $mhs->gelar_depan }} {{ trim($mhs->nm_mhs) }}{{ !empty($mhs->gelar_belakang) ? ', '.$mhs->gelar_belakang : '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tempat, Tgl lahir</th><td>: {{ $mhs->tempat_lahir }}, {{ Rmt::formatTgl($mhs->tgl_lahir) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Kelamin</th><td>: {{ Sia::nmJenisKelamin($mhs->jenkel) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                       <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="90px">Nama Ibu</th><td>: {{ $mhs->nm_ibu }}</td>
                                    </tr>
                                    <tr>
                                        <th>Agama</th><td>: {{ $mhs->nm_agama }}</td>
                                    </tr>
                                    <tr>
                                        <th>HP</th><td>: {{ $mhs->hp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Info Nobel</th><td>: {{ $mhs->nm_info }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" data-provide="tabdrop">
                                <li <?= Request::get('tab_aktif') != 'doc' ? 'class="active"':'' ?>><a href="#mhs" data-toggle="tab">Alamat</a></li>
                                <li><a href="#ortu" data-toggle="tab">Orang Tua</a></li>
                                <li><a href="#wali" data-toggle="tab">Wali</a></li>
                                <li <?= Request::get('tab_aktif') == 'doc' ? 'class="active"':'' ?>><a href="#dokumen" data-toggle="tab">Dokumen</a></li>
                                <li><a href="#akun" data-toggle="tab">Akun</a></li>
                            </ul>
                            <div class="tab-content">
                            
                                <div class="tab-pane fade <?= Request::get('tab_aktif') != 'doc' ? 'in active':'' ?>" id="mhs">
                                    <div class="table-responsive">
                                        <table border="0" class="table table-striped" style="width:100%">
                                            <tbody class="detail-mhs">
                                                <tr>
                                                    <th width="160px">NIK</th>
                                                    <td width="50%">: {{ $mhs->nik }}</td>
                                                    <td rowspan="4" colspan="4" align="center">
                                                        <?php if ( !empty($mhs->foto_mahasiswa) ) { ?>
                                                            <img id="foto" src="{{ config('app.url-foto-mhs') }}/{{ $mhs->foto_mahasiswa }}" width="100">
                                                        <?php } else { ?>
                                                            <img id="foto" src="{{ url('resources') }}/assets/img/avatar6.png" width="100">
                                                        <?php } ?>
                                                        <br>
                                                        <br>
                                                        @if ( Sia::adminOrAkademik() || Sia::cs() )
                                                            <button type="button" id="btn-file" class="btn btn-xs btn-warning">Ubah gambar</button>
                                                            <form id="form-foto" action="{{ route('mahasiswa_updatefoto') }}" method="post" enctype="multipart/form-data">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" name="id" value="{{ $mhs->id }}">
                                                                <input type="hidden" name="nim" value="{{ Sia::nim($mhs->id) }}">
                                                                <input type="file" name="foto" id="field-foto" accept="image/*" style="display:none">
                                                                <div id="btn-upload" style="display: none">
                                                                    <button class="btn btn-xs btn-primary">Simpan</button>
                                                                    <button type="reset" id="btn-reset" class="btn btn-xs btn-default">Batal</button>
                                                                </div>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>NISN</th>
                                                    <td>: {{ $mhs->nisn }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama sekolah</th>
                                                    <td>: {{ $mhs->nm_sekolah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tahun Lulus sekolah</th>
                                                    <td>: {{ $mhs->tahun_lulus_sekolah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>NPWP</th>
                                                    <td>: {{ $mhs->npwp }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Kewarganegaraan</th>
                                                    <td>: {{ Sia::wargaNegara($mhs->kewarganegaraan) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Alamat</th>
                                                    <td colspan="5">: {{ $mhs->alamat }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Dusun</th>
                                                    <td>: {{ $mhs->des_kel }}</td>
                                                    <th width="10">RT</th>
                                                    <td>: {{ $mhs->rt }}</td>
                                                    <th width="10">RW</th>
                                                    <td>: {{ $mhs->rw }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Kelurahan</th>
                                                    <td>: {{ $mhs->des_kel }}</td>
                                                    <th>POS</th>
                                                    <td>: {{ $mhs->pos }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Kecamatan</th>
                                                    <td>: {{ Sia::nmWilayah($mhs->id_wil) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jenis Tinggal</th>
                                                    <td>: {{ $mhs->nm_jns_tinggal }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Alat transportasi</th>
                                                    <td>: {{ $mhs->nm_alat_transpor }}</td>
                                                </tr>
                                                <!-- <tr>
                                                    <th>HP</th>
                                                    <td>: {{ $mhs->hp }}</td>
                                                </tr> -->
                                                <tr>
                                                    <th>Email</th>
                                                    <td>: {{ $mhs->email }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <!-- //mhs -->

                                <!-- //wali -->
                                <div class="tab-pane fade" id="ortu">
                                    <div class="table-responsive">
                                        <h4 style="border-bottom: 1px solid #999">AYAH</h4>
                                        <table border="0" class="table table-striped" style="width:500px">
                                            <tbody class="detail-mhs">

                                                <tr>
                                                    <th width="160px">NIK</th>
                                                    <td>: {{ $mhs->nik_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nama</th>
                                                    <td>: {{ $mhs->nm_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tgl Lahir</th>
                                                    <td>: {{ empty($mhs->tgl_lahir_ayah) ? '' : Rmt::formatTgl($mhs->tgl_lahir_ayah) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>HP</th>
                                                    <td>: {{ $mhs->hp_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pendidikan</th>
                                                    <td>: {{ $mhs->pdk_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pekerjaan</th>
                                                    <td>: {{ $mhs->pkj_ayah }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Penghasilan</th>
                                                    <td>: {{ $mhs->phs_ayah }}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <h4 style="border-bottom: 1px solid #999;margin-top:10px">IBU</h4>
                                        <table border="0" class="table table-striped" style="width:500px">
                                            <tbody class="detail-mhs">
                                                <tr>
                                                    <th width="160px">NIK</th>
                                                    <td>: {{ $mhs->nik_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tgl Lahir</th>
                                                    <td>: {{ empty($mhs->tgl_lahir_ibu) ? '' : Rmt::formatTgl($mhs->tgl_lahir_ibu) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>HP</th>
                                                    <td>: {{ $mhs->hp_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pendidikan</th>
                                                    <td>: {{ $mhs->pdk_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pekerjaan</th>
                                                    <td>: {{ $mhs->pkj_ibu }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Penghasilan</th>
                                                    <td>: {{ $mhs->phs_ibu }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- //ortu -->
                                
                                <div class="tab-pane fade" id="wali">
                                    <div class="table-responsive">
                                        <table border="0" class="table table-striped" style="width:500px">
                                            <tbody class="detail-mhs">
                                                <tr>
                                                    <th width="160px">Nama</th>
                                                    <td>: {{ $mhs->nm_wali }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tgl Lahir</th>
                                                    <td>: {{ empty($mhs->tgl_lahir_wali) ? '' : Rmt::formatTgl($mhs->tgl_lahir_wali) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>HP</th>
                                                    <td>: {{ $mhs->hp_wali }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pendidikan</th>
                                                    <td>: {{ $mhs->pdk_wali }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pekerjaan</th>
                                                    <td>: {{ $mhs->pkj_wali }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Penghasilan</th>
                                                    <td>: {{ $mhs->phs_wali }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- //wali -->

                                <!-- Dokumen -->
                                <div class="tab-pane fade <?= Request::get('tab_aktif') == 'doc' ? 'in active':'' ?>" id="dokumen">
                                    @include('mahasiswa.dokumen.index')
                                </div>

                                <!-- Akun -->
                                <div class="tab-pane fade" id="akun">
                                    <form action="{{ route('mahasiswa_update_akun') }}" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="id" value="{{ $mhs->id_user }}">
                                        <input type="hidden" name="nama" value="{{ $mhs->nm_mhs }}">
                                        <input type="hidden" name="email" value="{{ $mhs->email }}">
                                        <input type="hidden" name="nim" value="{{ $nim }}">
                                        <input type="hidden" name="id_mhs" value="{{ $mhs->id }}">
                                        <div class="row">
                                            <div class="col-md-12">

                                                <div class="form-group">
                                                    <label class="control-label">Username <span>*</span></label>
                                                    <div>
                                                        <input type="text" class="form-control" name="username" value="{{ $mhs->username }}">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">Password</label>
                                                    <div>
                                                        <input type="text" class="form-control" name="password" placeholder="Kosongkan jika tak mengganti password">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label"></label>
                                                    <div>
                                                        <button class="btn btn-primary btn-sm" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN Akun</button>
                                                    </div>
                                                </div>

                                                <hr>
                                            </div>
                                        </div>


                                    </form>
                                </div>

                            </div>
                            <!-- //tab-content -->
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>

<div id="modal-add-dokumen" class="modal fade" data-width="700" style="top: 30%" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Tambahkan Dokumen</h4>
    </div>

    <div class="modal-body" style="padding: 0">
        <div class="tabbable tab-default">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#file" id="tab1" data-toggle="tab">Upload File</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="file">
                    <form action="{{ route('mahasiswa_doc_store') }}" enctype="multipart/form-data" method="post" class="dropzone" id="dropzone">
                        <input type="hidden" name="id_mhs" value="{{ $mhs->id }}">
                        {{ csrf_field() }}
                        <div class="fallback">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>

<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>
<script>

    $(function () {

        $('#nav-mini').trigger('click');

        $('#btn-file').click(function(){
            $('#field-foto').trigger('click');
        });

        $('#field-foto').change(function(){
            $('#btn-upload').show();
            $('#btn-file').hide();
            readURL(this);
        });

        $('#btn-reset').click(function(){
            $('#btn-file').show();
            $('#btn-upload').hide();
            $('#foto').attr('src', '{{ url('storage') }}/foto-mahasiswa/thumb/{{ $mhs->foto_mahasiswa }}');
        });
    });

    function readURL(input) {

      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('#foto').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      };
    }


    Dropzone.options.dropzone = {
        maxFilesize: 10,
        dictDefaultMessage: 'Seret file ke sini atau klik',
        acceptedFiles: ".xlsx,.xls,.csv,.docx,.pdf,.pptx,.ppt,.ppsx,.odp,.zip,.rar,.jpg,.png,.jpeg",
        success: function(file, response) 
        {
            location.href="{{ route('mahasiswa_detail', ['id' => $mhs->id]) }}?tab_aktif=doc";
        },
        error: function(file, response)
        {
            showMessage2('',response);
            this.removeAllFiles();
            return false;
        }
    };

    $(function(){
        $('table[data-provide="data-table"]').dataTable();

        $('.judul').editable({
            url: '{{ route('dsn_fm_update') }}',
            name: 'judul',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                return params;
            },
            success: function(response, newValue) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(response,value)
            {
                console.log(JSON.stringify(response));
                var respon = parseObj(response.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = response.statusText;
                }
                showMessage2('', pesan);
            }
        });

    });

    function hapus(id)
    {
        if ( confirm('Anda ingin menghapus file ini ?') ) {
            window.location.href="{{ route('mahasiswa_doc_delete') }}/"+id+"?id_mhs={{ $mhs->id }}";
        }

    }
</script>
@endsection