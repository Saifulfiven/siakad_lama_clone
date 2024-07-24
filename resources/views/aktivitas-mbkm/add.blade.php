@extends('layouts.app')

@section('title','Tambah Aktivitas MBKM')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Aktivitas MBKM
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <form action="{{ route('mbkm_store') }}" id="form-akm" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-10">

                            <div class="form-group">
                                <label class="control-label">Program Studi <span>*</span></label>
                                <div>
                                    <select name="prodi" class="form-control mw-3">
                                        <option value="">--Pilih Prodi--</option>
                                        @foreach( Sia::listProdi() as $r )
                                            <option value="{{ $r->id_prodi }}">{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Semester <span>*</span></label>
                                <div>
                                    {{ Sia::sessionPeriode('nama') }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Jenis Aktivitas <span>*</span></label>
                                <div>
                                    <select name="jenis_aktivitas" class="form-control mw-3" onchange="jenisAktivitas(this.value)">
                                        <option value="">-- Jenis Aktivitas --</option>
                                        @foreach( $jenis_aktivitas as $jk )
                                            <option value="{{ $jk->id }}">{{ $jk->nm_aktivitas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="jenis-pertukaran" style="display: none">
                                <label class="control-label">Jenis Pertukaran  <span>*</span></label>

                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="jenis_pertukaran" value="INTERNAL" checked=""> INTERNAL
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="jenis_pertukaran" value="EKSTERNAL"> EKSTERNAL 
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Judul <span>*</span></label>
                                <div>
                                    <textarea name="judul" rows="4" class="form-control mw-3"></textarea>
                                </div>
                            </div>

                            <?= Sia::Textfield('Lokasi','lokasi',false,'text','mw-3') ?>

                            <?= Sia::Textfield('Nomor Sk Tugas','sk_tugas',false,'text','mw-3') ?>

                            <?= Sia::Textfield('Tanggal Sk Tugas','tgl_sk',false,'date','mw-2') ?>

                            <div class="form-group">
                                <label class="control-label">Jenis Anggota</label>

                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="jenis_anggota" value="0" checked=""> Personal
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="jenis_anggota" value="1"> Kelompok
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Keterangan</label>
                                <div>
                                    <textarea name="keterangan" class="form-control mw-3"></textarea>
                                </div>
                            </div>


                        </div>

                        <div class="col-md-6">
                            <div class="row" style="border-top: 1px solid #eee;margin-bottom: 13px;padding-top: 10px;">
                                <div class="col-md-12">
                                    <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-danger btn-outline btn-sm"><i class="fa fa-times"></i> BATAL</a>
                                    <button class="btn btn-primary btn-sm pull-right" id="btn-submit-akm" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
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
<script>

    $(function(){

    });

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

    function submit(modul)
    {

        var options = {
            beforeSend: function() 
            {
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                
                var id = data.id;
                window.location.href='{{ route('mbkm_detail') }}/'+id;
            },
            error: function(data, status, message)
            {
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i class='fa fa-save'></i> SIMPAN");

                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('akm', pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('akm');

    function jenisAktivitas(value)
    {
        if ( value == 99 ) {
            $('#jenis-pertukaran').show();
        } else {
            $('#jenis-pertukaran').hide();
        }
    }
</script>
@endsection