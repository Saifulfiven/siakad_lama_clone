@extends('layouts.app')

@section('title','Tambah Mahasiswa Lulus/Keluar')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Mahasiswa Lulus/Keluar
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('lk_store') }}" id="form-lulus" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}

                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10">

                            <div class="form-group">
                                <label class="control-label">Mahasiswa <span>*</span></label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-mhs" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" required="" id="autocomplete-mhs" name="nama_mhs">
                                        <input type="hidden" id="id-mhs" name="mahasiswa">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label">Semester Lulus/Keluar<span>*</span></label>
                                <div>
                                    <select name="semester" class="form-control mw-2">
                                        @foreach( Sia::listSemester() as $sm )
                                            <option value="{{ $sm->id_smt }}" {{ Sia::sessionPeriode() == $sm->id_smt ? 'selected':'' }}>{{ $sm->nm_smt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jenis Keluar <span>*</span></label>
                                <div>
                                    <select name="jenis_keluar" onchange="jenisKeluar(this.value)" class="form-control mw-2">
                                        <option value="">--Pilih jenis keluar--</option>
                                        @foreach( Sia::jenisKeluar() as $jk )
                                            <option value="{{ $jk->id_jns_keluar }}">{{ $jk->ket_keluar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label">Tanggal Keluar <span>*</span></label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime mw-2" style="padding-left: 14px" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_keluar" autocomplete="off">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>

                            <div id="lulus">
                                <?= Sia::Textfield('SK Yudisium','sk_yudisium',false,'text','mw-3') ?>

                                <div class="form-group">
                                  <label class="control-label">Tanggal SK Yudisium</label>
                                  <div>
                                    <div class="row">
                                        <div class="input-group date form_datetime mw-2" style="padding-left: 14px" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                            <input type="text" class="form-control" name="tgl_sk_yudisium" autocomplete="off">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                  </div>
                                </div>

                                <?= Sia::Textfield('IPK','ipk',false,'text','mw-1','onkeypress="return indeksPrestasi(event,this)" minlength="4"') ?>

                                <div class="form-group">
                                  <label class="control-label">Judul Skripsi</label>
                                  <div>
                                    <textarea name="judul_skripsi" class="form-control" rows="4"></textarea>
                                  </div>
                                </div>
                            </div>

                            <div id="not-lulus" style="display: none">
                                <div class="form-group">
                                  <label class="control-label">Keterangan Keluar</label>
                                  <div>
                                    <textarea name="ket_keluar" class="form-control" rows="4"></textarea>
                                  </div>
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
        $('#autocomplete-mhs').autocomplete({
            serviceUrl: '{{ route('lk_mhs') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-mhs').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-mhs').hide();
            },
            onSelect: function(suggestion) {
                $('#id-mhs').val(suggestion.data);
                getIpk(suggestion.data);
                getJudul(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });
    });

    function getIpk(id_mhs_reg)
    {
        $.ajax({
            url: '{{ route('lk_add') }}',
            data: { id_mhs_reg: id_mhs_reg },
            success: function(result){
                $('input[name="ipk"]').val(result.msg);
            },
            error: function(data,status,msg){
                alert('Gagal mengisi IPK: '+msg);
            }
        });
    }

    function getJudul(id_mhs_reg)
    {
        $.ajax({
            url: '{{ route('lk_add') }}',
            data: { id_mhs_reg: id_mhs_reg, judul:1 },
            success: function(result){
                $('textarea[name="judul_skripsi"]').val(result.msg);
            },
            error: function(data,status,msg){
                alert('Gagal mengisi Judul Skripsi: '+msg);
            }
        });
    }

    function indeksPrestasi(evt, ele){

        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode( key );
        var value = ele.value + key;

        if ( value.length == 5 ) return false;

        var regex = /^\d+(,\d{0,2})?$/;
        if( !regex.test(value) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
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
                    window.location.href='{{ route('lk') }}';
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
    submit('lulus');

    function jenisKeluar(value)
    {
        if ( value == 1 ) {
            $('#lulus').show();
            $('#not-lulus').hide();
        } else {
            $('#lulus').hide();
            $('#not-lulus').show();
        }
    }


</script>
@endsection