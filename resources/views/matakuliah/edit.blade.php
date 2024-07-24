@extends('layouts.app')

@section('title','Ubah Matakuliah')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Ubah Matakuliah
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>
                {{ Rmt::alertSuccess() }}

                <form action="{{ route('matakuliah_update') }}" id="form-matakuliah" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $mk->id }}">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('matakuliah') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> KEMBALI</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <?= Sia::TextfieldEdit('Kode Matakuliah <span>*</span>','kode_matakuliah', $mk->kode_mk, false,'text','','mw-2') ?>
                            <?= Sia::TextfieldEdit('Nama Matakuliah <span>*</span>','nama_matakuliah', $mk->nm_mk) ?>
                            <div class="form-group">
                                <label class="control-label">Program Studi <span>*</span></label>
                                <div>
                                    <select class="form-control mw-2" name="prodi" onchange="getKonsentrasi(this.value)">
                                        <option value="">-- Pilih program studi --</option>
                                        @foreach( Sia::listProdi() as $r )
                                            <option value="{{ $r->id_prodi }}" {{ $mk->id_prodi == $r->id_prodi ? 'selected':'' }}>{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" id="prodi2" value="{{ $mk->id_prodi }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">konsentrasi</label>
                                <div id="konsentrasi">
                                    <select class="form-control" name="konsentrasi">
                                        <option value="">-- Pilih konsentrasi --</option>
                                        <?php foreach( Sia::listKonsentrasi($mk->id_prodi) as $r ) { ?>
                                            <option value="<?= $r->id_konsentrasi ?>" {{ $r->id_konsentrasi == $mk->id_konsentrasi ? 'selected':'' }}><?= $r->nm_konsentrasi ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Matakuliah yg diganti</label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" style="font-size:13px" class="form-control" name="matakuliah_value" id="autocomplete-ajax" placeholder="Mata kuliah" value="{{ $mk_terganti }}">
                                        <input type="hidden" id="mk-terganti" name="mk_terganti" value="{{ $mk->mk_terganti }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jenis Matakuliah</label>
                                <div>
                                    <select class="form-control mw-3" name="jenis_mk">
                                        @foreach( Sia::jenisMatakuliah('array') as $key => $value )
                                            <option value="{{ $key }}" {{ $mk->jenis_mk == $key ? 'selected':'' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Kelompok Matakuliah <span>*</span></label>
                                <div>
                                    <select class="form-control mw-3" name="kelompok_mk">
                                        <option value="">-- Pilih kelompok matakuliah --</option> 
                                        @foreach( Sia::kelompokMatakuliah('array') as $key => $value )
                                            <option value="{{ $key }}" {{ $mk->kelompok_mk == $key ? 'selected':'' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::TextfieldEdit('SKS Tatap Muka','sks_tm',$mk->sks_tm, false,'number','','mw-1') ?>
                            <?= Sia::TextfieldEdit('SKS Praktikum','sks_prak',$mk->sks_prak, false,'number','','mw-1') ?>
                            <?= Sia::TextfieldEdit('SKS Praktek Lapangan','sks_prak_lap',$mk->sks_prak_lap, false,'number','','mw-1') ?>
                            <?= Sia::TextfieldEdit('SKS Simulasi','sks_sim',$mk->sks_sim, false,'number','','mw-1') ?>

                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Ada SAP</label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                 <input type="radio" name="a_sap" id="optionsRadios1" value="1" {{ $mk->a_sap == 1 ? 'checked':'' }}>
                                    Ya
                                </label>
                                &nbsp; &nbsp; &nbsp; 
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    <input type="radio" name="a_sap" id="optionsRadios2" value="0" {{ $mk->a_sap == 0 ? 'checked':'' }}>
                                    Tidak
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Ada Silabus</label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                 <input type="radio" name="a_silabus" id="optionsRadios1" value="1" {{ $mk->a_silabus == 1 ? 'checked':'' }}>
                                    Ya
                                </label>
                                &nbsp; &nbsp; &nbsp; 
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    <input type="radio" name="a_silabus" id="optionsRadios2" value="0" {{ $mk->a_silabus == 0 ? 'checked':'' }}>
                                    Tidak
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Ada Bahan Ajar</label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                 <input type="radio" name="a_bahan_ajar" id="optionsRadios1" value="1" {{ $mk->a_bahan_ajar == 1 ? 'checked':'' }}>
                                    Ya
                                </label>
                                &nbsp; &nbsp; &nbsp; 
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    <input type="radio" name="a_bahan_ajar" id="optionsRadios2" value="0" {{ $mk->a_bahan_ajar == 0 ? 'checked':'' }}>
                                    Tidak
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Ada Acara Praktek</label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                 <input type="radio" name="acara_praktek" id="optionsRadios1" value="1" {{ $mk->acara_praktek == 1 ? 'checked':'' }}>
                                    Ya
                                </label>
                                &nbsp; &nbsp; &nbsp; 
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    <input type="radio" name="acara_praktek" id="optionsRadios2" value="0" {{ $mk->acara_praktek == 0 ? 'checked':'' }}>
                                    Tidak
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="padding-top: 0">Ada Diktat</label>
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                 <input type="radio" name="a_diktat" id="optionsRadios1" value="1" {{ $mk->a_diktat == 1 ? 'checked':'' }}>
                                    Ya
                                </label>
                                &nbsp; &nbsp; &nbsp; 
                                <label style="margin-bottom: 0;padding-top: 3px;margin-left: 15px;cursor: pointer;">
                                    <input type="radio" name="a_diktat" id="optionsRadios2" value="0" {{ $mk->a_diktat == 0 ? 'checked':'' }}>
                                    Tidak
                                </label>
                            </div>

                            <div class="form-group">
                              <label class="control-label">Mulai efektif</label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-5" data-picker-position="top-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_mulai_efektif" value="{{ empty($mk->tgl_mulai_efektif) || $mk->tgl_mulai_efektif == '0000-00-00' ? '': Rmt::formatTgl($mk->tgl_mulai_efektif,'d-m-Y') }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label">Akhir efektif</label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime col-md-5" data-picker-position="top-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_akhir_efektif" value="{{ empty($mk->tgl_akhir_efektif) || $mk->tgl_akhir_efektif == '0000-00-00' ? '': Rmt::formatTgl($mk->tgl_akhir_efektif,'d-m-Y') }}">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Ujian Akhir</label>
                                <div>
                                    <select class="form-control mw-2" name="ujian_akhir">
                                        <option value="">-- jenis ujian akhir --</option>
                                        @foreach( Sia::jnsTugasAkhir() as $key => $val )
                                            <option value="{{ $key }}" {{ $key == $mk->ujian_akhir ? 'selected':'' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jenis Bayar</label>
                                <div>
                                    <select class="form-control mw-2" name="jenis_bayar">
                                        <option value="">-- jenis bayar --</option>
                                        @foreach( Sia::jenisBayar() as $jb )
                                            <option value="{{ $jb->id_jns_pembayaran }}" {{ $jb->id_jns_pembayaran == $mk->id_jenis_bayar ? 'selected':'' }}>{{ $jb->ket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>

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

    $(function(){
        autocompleteMk('{{ $mk->id_prodi }}');
    });

    function autocompleteMk(prodi) {
        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('matakuliah_list') }}?prodi='+prodi,
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
                $('#mk-terganti').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });
    }

    function getKonsentrasi(value)
    {
        $('#prodi2').val(value);
        if ( value != '' ) {
            $('#autocomplete-ajax').removeAttr('disabled');
            autocompleteMk(value);
        } else {
            $('#autocomplete-ajax').attr('disabled','');
            $('#mk-terganti').val('');
        }

        $('#konsentrasi').html('<i class="fa fa-spinner fa-spin"></i>');
        $.ajax({
            url: '{{ route('mahasiswa_konsentrasi') }}',
            data: { prodi: value },
            success: function(result){
                $('#konsentrasi').html(result);
            },
            error: function(err,data,msg){
                alert(msg);
            }
        });
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
    submit('matakuliah');

</script>
@endsection