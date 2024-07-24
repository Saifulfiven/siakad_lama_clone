@extends('layouts.app')

@section('title','Ubah Mahasiswa')


@section('content')
<div id="overlay"></div>

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
                <div class="ajax-message"></div>

                <form id="form-mahasiswa" class="form-horizontal" action="{{ route('mahasiswa_update') }}" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $mhs->id }}">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" style="padding-right: 0">
                            <div class="form-group">
                              <label class="control-label">Nama <span>*</span></label>
                              <div>
                                  <input type="text" name="nama" value="{{ $mhs->nm_mhs }}" class="form-control">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Gelar Depan</label>
                              <div>
                                  <input type="text" name="gelar_depan" value="{{ $mhs->gelar_depan }}" class="form-control">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label">Gelar Belakang</label>
                              <div>
                                  <input type="text" name="gelar_belakang" value="{{ $mhs->gelar_belakang }}" class="form-control">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Tempat Lahir <span>*</span></label>
                              <div>
                                  <input type="text" name="tempat_lahir" value="{{ $mhs->tempat_lahir }}" class="form-control">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Tanggal Lahir <span>*</span></label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-10" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_lahir" value="{{ Carbon::parse($mhs->tgl_lahir)->format('d-m-Y') }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Kelamin <span>*</span></label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                 <input type="radio" {{ $mhs->jenkel == 'L' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios1" value="L">
                                    Laki-laki
                                </label>
                                &nbsp; &nbsp; &nbsp; 
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    <input type="radio" {{ $mhs->jenkel == 'P' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios2" value="P">
                                    Perempuan
                                </label>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">Nama Ibu <span>*</span></label>
                              <div>
                                  <input type="text" name="nama_ibu" value="{{ $mhs->nm_ibu }}" class="form-control">
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Agama <span>*</span></label>
                                <div>
                                    <select class="form-control" name="agama">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach( $agama as $ag )
                                            <option value="{{ $ag->id_agama }}" {{ $mhs->id_agama == $ag->id_agama ? 'selected':'' }}>{{ $ag->nm_agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Info Nobel</label>
                                <div>
                                    <select class="form-control" name="info_nobel">
                                        <option value="">-- Info Nobel dari mana --</option>
                                        @foreach( $infoNobel as $in )
                                            <option value="{{ $in->id_info_nobel }}" {{ $mhs->id_info_nobel == $in->id_info_nobel ? 'selected':'' }}>{{ $in->nm_info }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jnsMhsKip" class="control-label">Jenis Beasiswa</label>
                                <div>
                                    <select name="jnsMhsKip" id="jnsMhsKip" class="form-control">
                                        <option value="">- Select -</option>
                                        <option value="1" {{ $mhs->jns_beasiswa == 1 ? 'selected' : '' }}>Mandiri</option>
                                        <option value="2" {{ $mhs->jns_beasiswa == 2 ? 'selected' : '' }}>Mahasiswa PaGi Nobel</option>
                                        <option value="3" {{ $mhs->jns_beasiswa == 3 ? 'selected' : '' }}>Mahasiswa KIP</option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-12">
                            <div class="tabbable">
                                <ul class="nav nav-tabs" data-provide="tabdrop">
                                    <li class="active"><a href="#mhs" data-toggle="tab">Alamat</a></li>
                                    <li><a href="#ortu" data-toggle="tab">Orang Tua</a></li>
                                    <li><a href="#wali" data-toggle="tab">Wali</a></li>
                                </ul>
                                <div class="tab-content">
                                
                                    <div class="tab-pane fade in active" id="mhs">
                                        <div class="table-responsive">
                                            <table border="0" class="table-hover table-form" style="width:100%">
                                                <tr>
                                                    <td width="160px">NIK <span>*</span></td>
                                                    <td width="50%"><input type="text" name="nik" value="{{ $mhs->nik }}" data-always-show="true" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" maxlength="16"></td>
                                                </tr>
                                                <tr>
                                                    <td>NISN</td>
                                                    <td><input type="text" name="nisn" value="{{ $mhs->nisn }}" class="form-control mw-2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama sekolah</td>
                                                    <td><input type="text" name="nm_sekolah" value="{{ $mhs->nm_sekolah }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tahun Lulus sekolah</td>
                                                    <td><input type="text" name="thn_lulus_sekolah" value="{{ $mhs->tahun_lulus_sekolah }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control mw-1" maxlength="4"></td>
                                                </tr>
                                                <tr>
                                                    <td>NPWP</td>
                                                    <td><input type="text" name="npwp" value="{{ $mhs->npwp }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kewarganegaraan <span>*</span></td>
                                                    <td>
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                <input type="text" name="country" id="autocomplete-ajax" value="{{ Sia::wargaNegara($mhs->kewarganegaraan) }}" class="form-control">
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
                                                    <td><input type="text" name="dusun" value="{{ $mhs->des_kel }}" class="form-control"></td>
                                                    <td>RT</td>
                                                    <td><input type="text" name="rt" value="{{ $mhs->rt }}" class="form-control" maxlength="2"></td>
                                                    <td>RW</td>
                                                    <td><input type="text" name="rw" value="{{ $mhs->rw }}" class="form-control" maxlength="2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kelurahan <span>*</span></td>
                                                    <td><input type="text" name="kelurahan" value="{{ $mhs->des_kel }}" class="form-control"></td>
                                                    <td>POS</td>
                                                    <td><input type="text" name="pos" value="{{ $mhs->pos }}" class="form-control" maxlength="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kecamatan <span>*</span></td>
                                                    <td>
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
                                                    <td>HP</td>
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

                                    <!-- //wali -->
                                    <div class="tab-pane fade" id="ortu">
                                        <div class="table-responsive">
                                            <h4 style="border-bottom: 1px solid #999">AYAH</h4>
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="160px">NIK</td>
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
                                                    <td width="160px">NIK</td>
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
                                                    <td width="160px">Nama</td>
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
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>

    $(function () {
        
        'use strict';

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


        $('#nav-mini').trigger('click');
    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

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
                    window.location.href='{{ route('mahasiswa_detail',['id' => $mhs->id]) }}';
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

</script>
@endsection