@extends('layouts.app')

@section('title','Tambah Mahasiswa')


@section('content')
<div id="overlay"></div>
<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel">
        <header class="panel-heading">
            Tambah Mahasiswa
        </header>
          
        <div class="panel-body" style="padding-top: 3px">

            <div class="ajax-message"></div>

            <form action="{{ route('mahasiswa_store') }}" enctype="multipart/form-data" id="form-mahasiswa" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                {{ csrf_field() }}
                <input type="hidden" name="id_maba">
                <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                    <div class="col-md-12">
                        @if ( !Sia::pascasarjana() )
                            <div class="pull-left" style="margin-top: 5px">
                                <b>Semester {{ Sia::SessionPeriode('nama') }}</b>
                                &nbsp; <button type="button" class="btn btn-theme btn-xs" onclick="getPmb()">Ambil dari PMB</button>
                            </div>
                        @endif
                        <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> BATAL</a>
                        <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        @if ( Sia::pascasarjana() )

                            <div class="form-group">
                                <label class="control-label">Alumni Nobel?</label>
                                <div>
                                    <select class="form-control mw-1" onchange="alumni(this.value)">
                                        <option value="0">Bukan</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="alumni" style="display: none">
                                <div class="form-group">
                                  <label class="control-label">NIM</label>
                                  <div>
                                        <div class="input-group">
                                            <input type="text" class="form-control col-md-10" id="nim">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" id="cari-nim" type="button"><i class="fa fa-search"></i>  Tampilkan</button>
                                            </span>
                                        </div>
                                  </div>
                                </div>
                                <div id="search-result"></div>
                            </div>

                        @endif

                        <div class="not-alumni">
                            <?= Sia::Textfield('Nama <span>*</span>','nama') ?>
                            <div class="form-group">
                              <label class="control-label">Gelar Depan</label>
                              <div>
                                  <input type="text" name="gelar_depan" value="{{ old('gelar_depan') }}" class="form-control">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label">Gelar Belakang</label>
                              <div>
                                  <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang') }}" class="form-control">
                              </div>
                            </div>

                            <?= Sia::Textfield('Tempat Lahir <span>*</span>','tempat_lahir') ?>
                            <div class="form-group">
                              <label class="control-label">Tanggal Lahir <span>*</span></label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-10" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_lahir" value="{{ old('tgl_lahir') }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jenis Kelamin <span>*</span></label>
                                <div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ old('jenis_kelamin') == 'L' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios1" value="L" checked>
                                            Laki-laki
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" {{ old('jenis_kelamin') == 'P' ? 'checked':'' }} name="jenis_kelamin" id="optionsRadios2" value="P">
                                            Perempuan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Nama Ibu <span>*</span></label>
                              <div>
                                  <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" class="form-control">
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Agama <span>*</span></label>
                                <div>
                                    <select class="form-control" name="agama">
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach( $agama as $ag )
                                            <option value="{{ $ag->id_agama }}">{{ $ag->nm_agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="not-alumni">
                        <div class="col-md-6">
                            <div class="form-group">
                              <label class="control-label">Tgl Masuk <span>*</span></label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-10" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_masuk" value="{{ old('tgl_masuk') }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jenis Pendaftaran <span>*</span></label>
                                <div>
                                    <select class="form-control" onchange="jenisPendaftaran(this.value)" name="jns_pendaftaran">
                                        <option value="">-- Pilih jenis pendaftaran --</option>
                                        @foreach( $jnsPendaftaran as $jp )
                                            <option value="{{ $jp->id_jns_pendaftaran }}">{{ $jp->nm_jns_pendaftaran }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="pindahan" style="display:none">
                                <div class="form-group">
                                    <label class="control-label">Perguruan tinggi asal <span>*</span></label>
                                    <div style="position: relative">
                                        <div class="input-icon right"> 
                                            <span id="spinner-autocomplete-pt" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                            <input type="text" id="autocomplete-ajax-pt" class="form-control">
                                        </div>
                                        <input type="hidden" name="id_perguruan_tinggi" id="pt-asal">
                                    </div>
                                </div>
                                <div class="form-group">
                                  <label class="control-label">Program studi asal  <span>*</span></label>
                                  <div id="prodi-asal">
                                        <select class="form-control" disabled="">
                                            <option value="">-- Pilih perguruan tinggi dahulu --</option>
                                        </select>
                                  </div>
                                </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label">Total Biaya Masuk  <span>*</span></label>
                              <div>
                                  <input type="number" name="biaya_masuk" value="{{ old('biaya_masuk') }}" class="form-control">
                              </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jalur Pendaftaran</label>
                                <div>
                                    <select class="form-control" name="jalur_pendaftaran">
                                        <option value="">-- Pilih jalur pendaftaran --</option>
                                        @foreach( $jalurMasuk as $jm )
                                            <option value="{{ $jm->id_jalur_masuk }}">{{ $jm->nm_jalur_masuk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Program Studi <span>*</span></label>
                                <div>
                                    <select class="form-control" name="prodi" onchange="getKonsentrasi(this.value)">
                                        <option value="">-- Pilih program studi --</option>
                                        @foreach( $prodi as $pr )
                                            <option value="{{ $pr->id_prodi }}">{{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">konsentrasi</label>
                                <div id="konsentrasi">
                                    <select class="form-control" disabled="">
                                        <option value="">-- Pilih konsentrasi --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Info Nobel</label>
                                <div>
                                    <select class="form-control" name="info_nobel">
                                        <option value="">-- Info Nobel dari mana --</option>
                                        @foreach( $infoNobel as $in )
                                            <option value="{{ $in->id_info_nobel }}">{{ $in->nm_info }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            
                            
                        </div>

                        <div class="col-md-12">
                            <div class="tabbable">
                                <ul class="nav nav-tabs" data-provide="tabdrop">
                                    <li class="active"><a href="#mhs" data-toggle="tab">Alamat</a></li>
                                    <li><a href="#akademik" data-toggle="tab">Akademik</a></li>
                                    <li><a href="#ortu" data-toggle="tab">Orang Tua</a></li>
                                    <li><a href="#wali" data-toggle="tab">Wali</a></li>
                                </ul>
                                <div class="tab-content">
                                
                                    <div class="tab-pane fade in active" id="mhs">
                                        <div class="table-responsive">
                                            <table border="0" class="table-hover table-form" style="width:100%">
                                                <tr>
                                                    <td width="160px">NIK <span>*</span></td>
                                                    <td width="50%"><input type="text" name="nik" value="{{ old('nik') }}" data-always-show="true" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" maxlength="16"></td>
                                                </tr>
                                                <tr>
                                                    <td>NISN</td>
                                                    <td><input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control mw-2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama sekolah</td>
                                                    <td><input type="text" name="nm_sekolah" value="{{ old('nm_sekolah') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tahun Lulus sekolah</td>
                                                    <td><input type="text" name="thn_lulus_sekolah" value="{{ old('thn_lulus_sekolah') }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control mw-1" maxlength="4"></td>
                                                </tr>
                                                <tr>
                                                    <td>NPWP</td>
                                                    <td><input type="text" name="npwp" value="{{ old('npwp') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kewarganegaraan <span>*</span></td>
                                                    <td>
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                <input type="text" name="country" id="autocomplete-ajax" value="Indonesia" class="form-control">
                                                            </div>
                                                            <input type="hidden" name="kewarganegaraan" id="negara" value="ID">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Alamat</td>
                                                    <td colspan="5"><input type="text" name="alamat" value="{{ old('alamat') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Dusun</td>
                                                    <td><input type="text" name="dusun" value="{{ old('dusun') }}" class="form-control"></td>
                                                    <td>RT</td>
                                                    <td><input type="text" name="rt" value="{{ old('rt') }}" class="form-control" maxlength="2"></td>
                                                    <td>RW</td>
                                                    <td><input type="text" name="rw" value="{{ old('rw') }}" class="form-control" maxlength="2"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kelurahan <span>*</span></td>
                                                    <td><input type="text" name="kelurahan" value="{{ old('kelurahan') }}" class="form-control"></td>
                                                    <td>POS</td>
                                                    <td><input type="text" name="pos" value="{{ old('pos') }}" class="form-control" maxlength="5"></td>
                                                </tr>
                                                <tr>
                                                    <td>Kecamatan <span>*</span></td>
                                                    <td>
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <span id="spinner-autocomplete-1" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                <input type="text" id="autocomplete-kec" class="form-control">
                                                            </div>
                                                            <input type="hidden" name="kecamatan" id="kecamatan">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Jenis Tinggal</td>
                                                    <td>
                                                        <select class="form-control" name="jenis_tinggal">
                                                            <option value="">-- Pilih jenis tinggal --</option>
                                                            @foreach( $jenisTinggal as $jg )
                                                                <option value="{{ $jg->id_jns_tinggal }}">{{ $jg->nm_jns_tinggal }}</option>
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
                                                                <option value="{{ $at->id_alat_transpor }}">{{ $at->nm_alat_transpor }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp" value="{{ old('hp') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td><input type="email" name="email" value="{{ old('email') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Foto</td>
                                                    <td><input type="file" name="foto" value="{{ old('foto') }}" class="form-control"></td>
                                                </tr>
                                            </table>
                                        </div>

                                    </div>
                                    <!-- //mhs -->
                                    
                                    <!-- //akademik -->
                                    <div class="tab-pane fade" id="akademik">
                                        <div class="table-responsive">
                                            <h4 style="border-bottom: 1px solid #999">Data Akademik</h4>
                                            <p>Apabila mahasiswa telah tersimpan kemudian ingin mengubah data akademik, masuk ke <b>detail mahasiswa -> history pendidikan</b></p>
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="160">Kurikulum <span>*</span></td>
                                                    <td id="kurikulum">
                                                        <select class="form-control" disabled="">
                                                            <option value="">-- Pilih kurikulum</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="160">Waktu Kuliah <span>*</span></td>
                                                    <td>
                                                        <select class="form-control" name="waktu_kuliah">
                                                            <option value="">-- Pilih waktu kuliah --</option>
                                                            @foreach( Sia::waktuKuliah() as $val )
                                                                <option value="{{ $val }}">{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Dosen PA <span>*</span></td>
                                                    <td>
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <span id="spinner-autocomplete-pa" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                <input type="text" id="autocomplete-ajax-pa" class="form-control">
                                                            </div>
                                                            <input type="hidden" name="dosen_pa" id="dosen-pa">
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Kode Kelas <span>*</span></td>
                                                    <td>
                                                        <div style="position: relative">
                                                            <div class="input-icon right"> 
                                                                <input type="text" name="kode_kelas" data-always-show="true" class="form-control mw-1" maxlength="5" required>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </table>
                                            <br>
                                            <br>
                                            <br>
                                        </div>
                                    </div>

                                    <!-- //wali -->
                                    <div class="tab-pane fade" id="ortu">
                                        <div class="table-responsive">
                                            <h4 style="border-bottom: 1px solid #999">AYAH</h4>
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="160px">NIK</td>
                                                    <td><input type="text" name="nik_ayah" value="{{ old('nik_ayah') }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" maxlength="16""></td>
                                                </tr>
                                                <tr>
                                                    <td>Nama</td>
                                                    <td><input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    <td>
                                                        <div class="input-group date form_datetime col-md-10" style="padding-left: 0 !important" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                                            <input type="text" class="form-control" name="tgl_lahir_ayah" value="{{ old('tgl_lahir_ayah') }}">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp_ayah" value="{{ old('hp_ayah') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Pendidikan</td>
                                                    <td>
                                                        <select class="form-control" name="pdk_ayah">
                                                            <option value="">-- Pilih jenjang --</option>
                                                            @foreach( $pdk as $pd )
                                                                <option value="{{ $pd->id_pdk }}" {{ old('pdk_ayah') == $pd->id_pdk ? 'selected':'' }}>{{ $pd->nm_pdk }}</option>
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
                                                                <option value="{{ $pkj->id_pekerjaan }}" {{ old('pekerjaan_ayah') == $pkj->id_pekerjaan ? 'selected':'' }}>{{ $pkj->nm_pekerjaan }}</option>
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
                                                                <option value="{{ $phs->id_penghasilan }}" {{ old('penghasilan_ayah') == $phs->id_penghasilan ? 'selected':'' }}>{{ $phs->nm_penghasilan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>

                                            <h4 style="border-bottom: 1px solid #999;margin-top:10px">IBU</h4>
                                            <table border="0" class="table-hover table-form" style="width:500px">
                                                <tr>
                                                    <td width="160px">NIK</td>
                                                    <td><input type="text" name="nik_ibu" value="{{ old('nik_ibu') }}" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" maxlength="16""></td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp_ibu" value="{{ old('hp_ibu') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    <td>
                                                        <div class="input-group date form_datetime col-md-10" style="padding-left: 0 !important" data-picker-position="top-left" data-date-format="dd-mm-yyyy" >
                                                            <input type="text" class="form-control" name="tgl_lahir_ibu" value="{{ old('tgl_lahir_ibu') }}">
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
                                                                <option value="{{ $pd->id_pdk }}" {{ old('pdk_ibu') == $pd->id_pdk ? 'selected':'' }}>{{ $pd->nm_pdk }}</option>
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
                                                                <option value="{{ $pkj->id_pekerjaan }}" {{ old('pekerjaan_ibu') == $pkj->id_pekerjaan ? 'selected':'' }}>{{ $pkj->nm_pekerjaan }}</option>
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
                                                                <option value="{{ $phs->id_penghasilan }}" {{ old('penghasilan_ibu') == $phs->id_penghasilan ? 'selected':'' }}>{{ $phs->nm_penghasilan }}</option>
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
                                                    <td><input type="text" name="nama_wali" value="{{ old('nama_wali') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>HP</td>
                                                    <td><input type="text" name="hp_wali" value="{{ old('hp_wali') }}" class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>Tgl Lahir</td>
                                                    <td>
                                                        <div class="input-group date form_datetime col-md-10" style="padding-left: 0 !important" data-picker-position="top-left" data-date-format="dd-mm-yyyy" >
                                                            <input type="text" class="form-control" name="tgl_lahir_wali" value="{{ old('tgl_lahir_wali') }}">
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
                                                                <option value="{{ $pd->id_pdk }}" {{ old('pdk_wali') == $pd->id_pdk ? 'selected':'' }}>{{ $pd->nm_pdk }}</option>
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
                                                                <option value="{{ $pkj->id_pekerjaan }}" {{ old('pekerjaan_wali') == $pkj->id_pekerjaan ? 'selected':'' }}>{{ $pkj->nm_pekerjaan }}</option>
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
                                                                <option value="{{ $phs->id_penghasilan }}" {{ old('penghasilan_wali') == $phs->id_penghasilan ? 'selected':'' }}>{{ $phs->nm_penghasilan }}</option>
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
                </div>

            </form>

        </div>

    </div>
  </div>
</div>

<div id="modal-pmb" class="modal fade" data-width="900" tabindex="-1" style="top: 15%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Data Calon Maba</h4>
    </div>
    <div class="modal-body" id="form-pmb">
        
    </div>
    <div class="modal-footer">
        <button class="btn btn-theme btn-sm" data-dismiss="modal">Keluar</button>
    </div>
</div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>
    $('#jns_pendaftaran').on('change', function() {
      if( this.value.trim() != 1  ) {
        $('#pindahan').show();
      } else {
        $('#pindahan').hide();
      }
    });

    $(function () {
        'use strict';

        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('mahasiswa_negara') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete').hide();
            },
            onSelect: function(suggestion) {
                $('#negara').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#autocomplete-ajax-pt').autocomplete({
            serviceUrl: '{{ route('get_pt') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-pt').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-pt').hide();
            },
            onSelect: function(suggestion) {
                $('#pt-asal').val(suggestion.data);
                console.log(suggestion.data)
                getProdiAsal(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#autocomplete-ajax-pa').autocomplete({
            serviceUrl: '{{ route('jdk_dosen') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-pa').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-pa').hide();
            },
            onSelect: function(suggestion) {
                $('#dosen-pa').val(suggestion.data);
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
            onSearchStart: function(data) {
                $('#spinner-autocomplete-1').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-1').hide();
            },
            onSelect: function(suggestion) {
                $('#kecamatan').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#cari-nim').click(function(){
            var nim = $('#nim').val();
            var btn = $(this);
            btn.html('<i class="fa fa-spinner fa-spin"></i> Mencari..');
            btn.attr('disabled','');
            $.ajax({
                url: '{{ route('mahasiswa_cari_nim') }}',
                data: {nim:nim},
                success: function(data){
                    $('#search-result').html(data);
                    btn.removeAttr('disabled');
                    btn.html('<i class="fa fa-search"></i> Tampilkan');
                },
                error: function(data,status,msg){
                    alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
                    btn.removeAttr('disabled');
                    btn.html('<i class="fa fa-search"></i> Tampilkan');
                }
            });
        })
        $('#nav-mini').trigger('click');

        $('body').on('click', '.ambil', function(){
            var div = $(this);
            $('input[name="nama"]').val(div.data('nama'));
            $('select[name="prodi"]').val(div.data('prodi'));
            getKonsentrasi(div.data('prodi'));
            $('input[name="id_maba"]').val(div.data('id'));
            $('input[name="nik"]').val(div.data('ktp'));
            $('input[name="alamat"]').val(div.data('alamat'));
            $('input[name="kecamatan"]').val(div.data('kecamatan'));
            $('input[name="kelurahan"]').val(div.data('kelurahan'));
            $('input[name="hp"]').val(div.data('hp'));
            $('input[name="tempat_lahir"]').val(div.data('tempat_lahir'));
            $('input[name="tgl_lahir"]').val(div.data('tgl_lahir'));

            var jenkel = div.data('jenkel');
            $('input[name="jenis_kelamin"][value="'+jenkel+'"]').prop('checked', true);

            $('select[name="agama"]').val(div.data('agama'));
            $('select[name="jenis_tinggal"]').val(div.data('jenis_tinggal'));
            $('input[name="nm_sekolah "]').val(div.data('slta'));
            $('input[name="thn_lulus_sekolah "]').val(div.data('tahun_lulus'));
            $('input[name="nama_ayah"]').val(div.data('ayah'));
            $('input[name="hp_ayah"]').val(div.data('hp_ayah'));
            $('select[name="pekerjaan_ayah"]').val(div.data('pekerjaan_ayah'));
            $('input[name="nik_ibu"]').val(div.data('nik_ibu'));
            $('input[name="nama_ibu"]').val(div.data('ibu'));
            $('input[name="hp_ibu"]').val(div.data('hp_ibu'));
            $('select[name="pekerjaan_ibu"]').val(div.data('pekerjaan_ibu'));
            $('select[name="penghasilan_ayah"]').val(div.data('penghasilan_ortu'));
            $('input[name="nik_ayah"]').val(div.data('nik_ayah'));
            $('input[name="tgl_masuk"]').val(div.data('tgl_daftar'));
            $('select[name="jns_pendaftaran"]').val(1);

            $('#modal-pmb').modal('hide');
        });
    });

    function getPmb()
    {
        $('#form-pmb').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
        $('#modal-pmb').modal('show');
        $.ajax({
            url: '{{ route('mahasiswa_pmb') }}',
            success: function(data){
                $('#form-pmb').html(data);
            },
            error: function(data,err,status)
            {
                var pesan = data.responseJSON.msg;
                $('#form-pmb').html('<center><p>'+pesan+'. Coba ulangi lagi.</p></center>');
            }
        });
    }

    function jenisPendaftaran(value)
    {
        if( value != 1  ) {
            $('#pindahan').show();
        } else {
            $('#pindahan').hide();
        }
    }

    function alumni(val)
    {
        if ( val == '0' ) {
            $('.not-alumni').show();
            $('#btn-submit').removeAttr('disabled');
            $('.alumni').hide();
        } else {
            $('.not-alumni').hide();
            $('#btn-submit').attr('disabled','');
            $('.alumni').show();
        }
    }
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
                    window.location.href='{{ route('mahasiswa_detail') }}/'+data.msg;
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

    function getProdiAsal(id_pt_asal)
    {
        $('#prodi-asal').html('<i class="fa fa-spinner fa-spin"></i> mengambil program studi');
        $.ajax({
            url: '{{ route('get_all_prodi') }}',
            data: { id_pt: id_pt_asal },
            success: function(result){
                $('#prodi-asal').html(result);
            },
            error: function(err,data,msg){
                alert(msg);
            }
        });

    }

    function getKonsentrasi(value)
    {
        $('#konsentrasi').html('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: '{{ route('mahasiswa_konsentrasi') }}',
            data: { prodi: value },
            success: function(result){
                $('#konsentrasi').html(result);
                getKurikulum(value);
            },
            error: function(err,data,msg){
                alert(msg);
            }
        });

    }

    function getKurikulum(prodi)
    {
        $.ajax({
            url: '{{ route('mahasiswa_get_kurikulum') }}',
            data: { prodi: prodi },
            success: function(result){
                $('#kurikulum').html(result);
            },
            error: function(err,data,msg){
                alert('Gagal mengambil kurikulum'+msg);
            }
        });
    }
</script>
@endsection