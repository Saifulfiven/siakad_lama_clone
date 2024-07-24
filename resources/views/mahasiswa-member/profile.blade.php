@extends('layouts.app')

@section('title','Profilku')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a><b>BIODATA</b></a></li>
    </ul>
@endsection

@section('content')
<div id="overlay"></div>

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel">
        <div class="panel-body" style="padding: 3px 3px">

            <div class="col-md-12">

                <form id="form-mahasiswa" class="form-horizontal" action="{{ route('mhs_update_profil') }}" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $mhs->id }}">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <div class="pull-left" style="padding-top: 7px"><b>BIODATA</b></div>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6" style="padding-right: 0">
                            <div class="form-group">
                              <label class="control-label">Nama</label>
                              <div>
                                  <input type="text" name="nama" value="{{ $mhs->nm_mhs }}" class="form-control" disabled="">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Tempat Lahir</label>
                              <div>
                                  <input type="text" name="tempat_lahir" value="{{ $mhs->tempat_lahir }}" class="form-control" disabled="">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Tanggal Lahir</label>
                              <div>
                                <input type="text" value="{{ Carbon::parse($mhs->tgl_lahir)->format('d-m-Y') }}" class="form-control mw-2" disabled="">
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Kelamin</label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    {{ $mhs->jenkel == 'L' ? 'Laki-laki':'Perempuan' }}
                                </label>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">Nama Ibu</label>
                              <div>
                                  <input type="text" name="nama_ibu" value="{{ $mhs->nm_ibu }}" class="form-control" disabled="">
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Agama</label>
                                <div>
                                    <select class="form-control" name="agama" disabled="">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach( $agama as $ag )
                                            <option value="{{ $ag->id_agama }}" {{ $mhs->id_agama == $ag->id_agama ? 'selected':'' }}>{{ $ag->nm_agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <p>Apabila terdapat data yang tidak sesuai (yang tidak bisa anda ubah) segera melapor ke bagian akademik</p>
                            </div>
                            
                        </div>
                        <div class="col-md-12">
                            <div class="tabbable">
                                <ul class="nav nav-tabs" data-provide="tabdrop">
                                    <li  <?= Request::get('tab_aktif') != 'doc' ? 'class="active"':'' ?>><a href="#mhs" data-toggle="tab">Alamat</a></li>
                                    <li><a href="#ortu" data-toggle="tab">Orang Tua</a></li>
                                    <li><a href="#wali" data-toggle="tab">Wali</a></li>
                                    <li><a href="#akun" data-toggle="tab">Akun</a></li>
                                    <li <?= Request::get('tab_aktif') == 'doc' ? 'class="active"':'' ?>><a href="#dokumen" data-toggle="tab">Dokumen</a></li>
                                </ul>
                                <div class="tab-content">
                                
                                    <div class="tab-pane fade <?= Request::get('tab_aktif') != 'doc' ? 'in active':'' ?>" id="mhs">
                                        <div class="table-responsive">
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="160px">NIK <span>*</span></td>
                                                    <td colspan="5"><input type="text" name="nik" value="{{ $mhs->nik }}" data-always-show="true" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control mw-2" maxlength="16"></td>
                                                </tr>
                                                <tr>
                                                    <td>NISN</td>
                                                    <td colspan="5"><input type="text" name="nisn" value="{{ $mhs->nisn }}" class="form-control mw-2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama sekolah</td>
                                                    <td colspan="5"><input type="text" name="nm_sekolah" value="{{ $mhs->nm_sekolah }}" class="form-control mw-3"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tahun Lulus sekolah</td>
                                                    <td colspan="5"><input type="text" name="thn_lulus_sekolah" value="{{ $mhs->tahun_lulus_sekolah }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control mw-1" maxlength="4"></td>
                                                </tr>
                                                <tr>
                                                    <td>NPWP</td>
                                                    <td colspan="5"><input type="text" name="npwp" value="{{ $mhs->npwp }}" class="form-control mw-2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kewarganegaraan <span>*</span></td>
                                                    <td colspan="5">
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                <input type="text" name="country" id="autocomplete-ajax" value="{{ Sia::wargaNegara($mhs->kewarganegaraan) }}" class="form-control mw-2">
                                                            </div>
                                                            <input type="hidden" name="kewarganegaraan" id="negara" value="{{ $mhs->kewarganegaraan }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td colspan="5"><input type="text" name="alamat" value="{{ $mhs->alamat }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Dusun</td>
                                                    <td colspan="5"><input type="text" name="dusun" value="{{ $mhs->des_kel }}" class="form-control mw-2"></td>
                                                </tr>
                                                <tr>
                                                    <td>RT</td>
                                                    <td colspan="5"><input type="text" name="rt" value="{{ $mhs->rt }}" class="form-control mw-1" maxlength="2"></td>
                                                </tr>
                                                <tr>
                                                    <td>RW</td>
                                                    <td colspan="5"><input type="text" name="rw" value="{{ $mhs->rw }}" class="form-control mw-1" maxlength="2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kelurahan <span>*</span></td>
                                                    <td colspan="5"><input type="text" name="kelurahan" value="{{ $mhs->des_kel }}" class="form-control mw-2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kecamatan <span>*</span></td>
                                                    <td colspan="5">
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <span id="spinner-autocomplete-1" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                <input type="text" id="autocomplete-kec" value="{{ Sia::nmWilayah($mhs->id_wil) }}" class="form-control">
                                                            </div>
                                                            <input type="hidden" name="kecamatan" id="kecamatan" value="{{ $mhs->id_wil }}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>POS</td>
                                                    <td colspan="5"><input type="text" name="pos" value="{{ $mhs->pos }}" class="form-control mw-1" maxlength="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Tinggal</td>
                                                    <td>
                                                        <select class="form-control" name="jenis_tinggal">
                                                            <option value="">-- Pilih jenis tinggal --</option>
                                                            @foreach( $jenisTinggal as $jg )
                                                                <option value="{{ $jg->id_jns_tinggal }}" {{ $mhs->jenis_tinggal == $jg->id_jns_tinggal ? 'selected':'' }}>{{ $jg->nm_jns_tinggal }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alat transportasi</td>
                                                    <td>
                                                        <select class="form-control" name="alat_transpor">
                                                            <option value="">-- Pilih alat transportasi --</option>
                                                            @foreach( $alatTranspor as $at )
                                                                <option value="{{ $at->id_alat_transpor }}" {{ $mhs->alat_transpor == $at->id_alat_transpor ? 'selected':'' }}>{{ $at->nm_alat_transpor }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>HP <span>*</span></td>
                                                    <td><input type="text" name="hp" value="{{ $mhs->hp }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><input type="email" name="email" value="{{ $mhs->email }}" class="form-control"></td>
                                                </tr>
                                            </table>
                                        </div>

                                    </div>
                                    <!-- //mhs -->

                                    <!-- //ortu -->
                                    <div class="tab-pane fade" id="ortu">
                                        <div class="table-responsive">
                                            <h4 style="border-bottom: 1px solid #999">AYAH</h4>
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="110px">NIK</td>
                                                    <td><input type="text" name="nik_ayah" value="{{ $mhs->nik_ayah }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" maxlength="16""></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama</td>
                                                    <td><input type="text" name="nama_ayah" value="{{ $mhs->nm_ayah }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    <td>
                                                        <div class="input-group date form_datetime col-md-10" style="padding-left: 0 !important" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                                            <input type="text" class="form-control" name="tgl_lahir_ayah" value="{{ empty($mhs->tgl_lahir_ayah) ? '' : Rmt::formatTgl($mhs->tgl_lahir_ayah) }}">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp_ayah" value="{{ $mhs->hp_ayah }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Pendidikan</td>
                                                    <td>
                                                        <select class="form-control" name="pdk_ayah">
                                                            <option value="">-- Pilih jenjang --</option>
                                                            @foreach( $pdk as $pd )
                                                                <option value="{{ $pd->id_pdk }}" {{ $mhs->id_pdk_ayah == $pd->id_pdk ? 'selected':'' }}>{{ $pd->nm_pdk }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pekerjaan</td>
                                                    <td>
                                                        <select class="form-control" name="pekerjaan_ayah">
                                                            <option value="">-- Pilih pekerjaan --</option>
                                                            @foreach( $pekerjaan as $pkj )
                                                                <option value="{{ $pkj->id_pekerjaan }}" {{ $mhs->id_pekerjaan_ayah == $pkj->id_pekerjaan ? 'selected':'' }}>{{ $pkj->nm_pekerjaan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Penghasilan</td>
                                                    <td>
                                                        <select class="form-control" name="penghasilan_ayah">
                                                            <option value="">-- Pilih penghasilan --</option>
                                                            @foreach( $penghasilan as $phs )
                                                                <option value="{{ $phs->id_penghasilan }}" {{ $mhs->id_penghasilan_ayah == $phs->id_penghasilan ? 'selected':'' }}>{{ $phs->nm_penghasilan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>

                                            <h4 style="border-bottom: 1px solid #999;margin-top:10px">IBU</h4>
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="110px">NIK</td>
                                                    <td><input type="text" name="nik_ibu" value="{{ $mhs->nik_ibu }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" maxlength="16""></td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp_ibu" value="{{ $mhs->hp_ibu }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    <td>
                                                        <div class="input-group date form_datetime col-md-10" style="padding-left: 0 !important" data-picker-position="top-left" data-date-format="dd-mm-yyyy" >
                                                            <input type="text" class="form-control" name="tgl_lahir_ibu" value="{{ empty($mhs->tgl_lahir_ayah) ? '' : Rmt::formatTgl($mhs->tgl_lahir_ayah) }}">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pendidikan</td>
                                                    <td>
                                                        <select class="form-control" name="pdk_ibu">
                                                            <option value="">-- Pilih jenjang --</option>
                                                            @foreach( $pdk as $pd )
                                                                <option value="{{ $pd->id_pdk }}" {{ $mhs->id_pdk_ibu == $pd->id_pdk ? 'selected':'' }}>{{ $pd->nm_pdk }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pekerjaan</td>
                                                    <td>
                                                        <select class="form-control" name="pekerjaan_ibu">
                                                            <option value="">-- Pilih pekerjaan --</option>
                                                            @foreach( $pekerjaan as $pkj )
                                                                <option value="{{ $pkj->id_pekerjaan }}" {{ $mhs->id_pekerjaan_ibu == $pkj->id_pekerjaan ? 'selected':'' }}>{{ $pkj->nm_pekerjaan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Penghasilan</td>
                                                    <td>
                                                        <select class="form-control" name="penghasilan_ibu">
                                                            <option value="">-- Pilih penghasilan --</option>
                                                            @foreach( $penghasilan as $phs )
                                                                <option value="{{ $phs->id_penghasilan }}" {{ $mhs->id_penghasilan_ibu == $phs->id_penghasilan ? 'selected':'' }}>{{ $phs->nm_penghasilan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- //ortu -->
                                    
                                    <div class="tab-pane fade" id="wali">
                                        <div class="table-responsive">
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="110px">Nama</td>
                                                    <td><input type="text" name="nama_wali" value="{{ $mhs->nm_wali }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp_wali" value="{{ $mhs->hp_wali }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    <td>
                                                        <div class="input-group date form_datetime col-md-10" style="padding-left: 0 !important" data-picker-position="top-left" data-date-format="dd-mm-yyyy" >
                                                            <input type="text" class="form-control" name="tgl_lahir_wali" value="{{ empty($mhs->tgl_lahir_wali) ? '': Rmt::formatTgl($mhs->tgl_lahir_wali,'d-m-Y') }}">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pendidikan</td>
                                                    <td>
                                                        <select class="form-control" name="pdk_wali">
                                                            <option value="">-- Pilih jenjang --</option>
                                                            @foreach( $pdk as $pd )
                                                                <option value="{{ $pd->id_pdk }}" {{ $mhs->id_pdk_wali == $pd->id_pdk ? 'selected':'' }}>{{ $pd->nm_pdk }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Pekerjaan</td>
                                                    <td>
                                                        <select class="form-control" name="pekerjaan_wali">
                                                            <option value="">-- Pilih pekerjaan --</option>
                                                            @foreach( $pekerjaan as $pkj )
                                                                <option value="{{ $pkj->id_pekerjaan }}" {{ $mhs->id_pekerjaan_wali == $pkj->id_pekerjaan ? 'selected':'' }}>{{ $pkj->nm_pekerjaan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Penghasilan</td>
                                                    <td>
                                                        <select class="form-control" name="penghasilan_wali">
                                                            <option value="">-- Pilih penghasilan --</option>
                                                            @foreach( $penghasilan as $phs )
                                                                <option value="{{ $phs->id_penghasilan }}" {{ $mhs->id_penghasilan_wali == $phs->id_penghasilan ? 'selected':'' }}>{{ $phs->nm_penghasilan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- //wali -->

                                    <!-- Akun -->
                                    <div class="tab-pane fade" id="akun">

                                        <div class="row">
                                            <div class="col-md-7">
                                                <div id="msg-akun"></div>

                                                <div class="form-group">
                                                    <label class="control-label">Username <span>*</span></label>
                                                    <div>
                                                        <input type="text" id="username" class="form-control" name="username" value="{{ $user->username }}">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">Password</label>
                                                    <div>
                                                        <input type="text" id="password" class="form-control" name="password" placeholder="Kosongkan jika tak mengganti password">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label"></label>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-sm" id="btn-submit-akun" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN AKUN</button>
                                                    </div>
                                                </div>

                                                <hr>
                                            </div>

                                            <div class="col-md-5">
                                                <div style="text-align: center">
                                                    <?php if ( !empty($mhs->foto_mahasiswa) ) { ?>
                                                        <img id="foto" src="{{ config('app.url-foto-mhs') }}/{{ $mhs->foto_mahasiswa }}" width="100">
                                                    <?php } else { ?>
                                                        <img id="foto" src="{{ url('resources') }}/assets/img/avatar.png" width="100">
                                                    <?php } ?>
                                                    <br>
                                                    <br>
                                                    <button type="button" id="btn-file" name="foto" class="btn btn-xs btn-warning">Ubah gambar</button>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade <?= Request::get('tab_aktif') == 'doc' ? 'in active':'' ?>" id="dokumen">
                                        @include('mahasiswa-member.dokumen')
                                    </div>

                                </div>
                                <!-- //tab-content -->
                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>

    </div>
  </div>
</div>

<form action="{{ route('mhs_update_foto') }}" id="form-upload-foto" enctype="multipart/form-data" method="post">
    {{ csrf_field() }}
    <input type="file" name="foto" id="inputFile" accept="image/*" style="display:none">
</form>

<div id="modal-error" class="modal fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Terjadi kesalahan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="ajax-message"></div>
        <hr>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
        </center>
    </div>
    <!-- //modal-body-->
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
                    <form action="{{ route('mhs_doc_store') }}" enctype="multipart/form-data" method="post" class="dropzone" id="dropzone">
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

<script>

    $(function () {
        
        'use strict';

        @if ( Session::has('success') )
            $.notific8('{{ Session::get('success') }}',{ life:5000,horizontalEdge:"bottom", theme:"success" ,heading:" INFO..."});
        @endif

        $('#form-mahasiswa').on('keyup keypress', function(e) {
          var keyCode = e.keyCode || e.which;
          if (keyCode === 13) { 
            e.preventDefault();
            return false;
          }
        });

        $('#btn-file').click(function(){
            $('#inputFile').trigger('click');
        });


        $("#inputFile").change(function(){
            readURL(this);
            $('#btn-file').html('<i class="fa fa-spin fa-spinner"></i>');
            $('#form-upload-foto').submit();
        });

        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('mahasiswa_negara') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSelect: function(suggestion) {
                $('#negara').val(suggestion.data);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete').hide();
            },
            onInvalidateSelection: function() {
            }
        });

        $('#autocomplete-kec').autocomplete({
            serviceUrl: '{{ route('mahasiswa_kecamatan') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSelect: function(suggestion) {
                $('#kecamatan').val(suggestion.data);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-1').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-1').hide();
            },
            onInvalidateSelection: function() {
            }
        });

        $('#btn-submit-akun').click(function(){
            var btn = $(this);
            btn.attr('disabled','');
            btn.html('<i class="fa fa-spinner fa-spin"></i> MENYIMPAN...');

            var username = $('#username').val();
            var password = $('#password').val();
            $.ajax({
                url: '{{ route('mhs_update_akun') }}',
                data: { username: username, password : password },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    $('#msg-akun').html('<p style="color: green">Berhasil menyimpan data</p>');
                    btn.removeAttr('disabled');
                    btn.html('SIMPAN AKUN');
                },
                error: function(err,data,msg)
                {
                    alert(msg);
                    console.log(msg);
                    btn.removeAttr('disabled');
                    btn.html('SIMPAN AKUN');
                }
            });

        });

    });

    Dropzone.options.dropzone = {
        maxFilesize: 10,
        dictDefaultMessage: 'Seret file ke sini atau klik',
        acceptedFiles: ".xlsx,.xls,.csv,.docx,.pdf,.pptx,.ppt,.ppsx,.odp,.zip,.rar,.jpg,.png,.jpeg",
        success: function(file, response) 
        {
            location.href="{{ url('/mhs/profil') }}?tab_aktif=doc";
        },
        error: function(file, response)
        {
            showMessage2('',response);
            this.removeAllFiles();
            return false;
        }
    };


    function readURL(input) {

      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('#foto').attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
      };
    }

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }
    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();

                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(data.msg);
                } else {
                    window.location.reload();
                }
                    
            },
            error: function(data, status, message)
            {
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage(pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('mahasiswa');
    submit('upload-foto');

</script>
@endsection