@extends('layouts.app')

@section('title','Ubah Mahasiswa Lulus/Keluar')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Ubah Mahasiswa Lulus/Keluar
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('lk_update') }}" id="form-lulus" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <!-- <input type="hidden" name="id_jenis_keluar" value="{{ $mhs->id_jenis_keluar }}"> -->

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
                                    {{ $mhs->nim .' - '.$mhs->nm_mhs }}
                                    <input type="hidden" id="id-mhs" name="mahasiswa" value="{{ $mhs->id_mhs_reg }}">
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label">Semester Lulus/Keluar<span>*</span></label>
                                <div>
                                    <select name="semester" class="form-control mw-2">
                                        @foreach( Sia::listSemester() as $sm )
                                            <option value="{{ $sm->id_smt }}" {{ $mhs->semester_keluar == $sm->id_smt ? 'selected':'' }}>{{ $sm->nm_smt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jenis Keluar <span>*</span></label>
                                <div>
                                    <select name="id_jenis_keluar" class="form-control mw-2">
                                        <option value="">--Pilih jenis keluar--</option>
                                        @foreach( Sia::jenisKeluar() as $jk )
                                            <option value="{{ $jk->id_jns_keluar }}" {{ $jk->id_jns_keluar == $mhs->id_jenis_keluar ? 'selected':'' }}>{{ $jk->ket_keluar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label">Tanggal Keluar <span>*</span></label>
                              <div>
                                <div class="row">
                                    <div class="input-group date form_datetime mw-2" style="padding-left: 14px" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                        <input type="text" class="form-control" name="tgl_keluar" value="{{ Carbon::parse($mhs->tgl_keluar)->format('d-m-Y') }}" autocomplete="off">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                              </div>
                            </div>

                            @if ( $mhs->id_jenis_keluar == 1 )

                                <?= Sia::TextfieldEdit('SK Yudisium','sk_yudisium', $mhs->sk_yudisium, false,'text','','mw-3') ?>

                                <div class="form-group">
                                  <label class="control-label">Tanggal SK Yudisium</label>
                                  <div>
                                    <div class="row">
                                        <div class="input-group date form_datetime mw-2" style="padding-left: 14px" data-picker-position="bottom-left" data-date-format="dd-mm-yyyy" >
                                            <input type="text" class="form-control" name="tgl_sk_yudisium" value="{{ Carbon::parse($mhs->tgl_sk_yudisium)->format('d-m-Y') }}" autocomplete="off">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                  </div>
                                </div>

                                <?= Sia::TextfieldEdit('IPK','ipk',str_replace('.',',',number_format($mhs->ipk,2)), false,'text','','ipk mw-1','onkeypress="return indeksPrestasi(event,this)" minlength="4"') ?>

                                <div class="form-group">
                                  <label class="control-label">Judul Skripsi</label>
                                  <div>
                                    <textarea name="judul_skripsi" class="form-control" rows="4">{{ $mhs->judul_skripsi }}</textarea>
                                  </div>
                                </div>

                            @else

                                <div class="form-group">
                                  <label class="control-label">Keterangan Keluar</label>
                                  <div>
                                    <textarea name="ket_keluar" class="form-control" rows="4">{{ $mhs->ket_keluar }}</textarea>
                                  </div>
                                </div>

                            @endif


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
<script>

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

</script>
@endsection