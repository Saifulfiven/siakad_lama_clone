@extends('layouts.app')

@section('title','Edit Dosen')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Ubah Dosen
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}

                <form action="{{ route('konsentrasi_update') }}" id="form-dosen" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    <input type="hidden" name="id" value="{{ $konsen->id }}">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-6">
                            <br>
                            <div class="form-group">
                                <label class="control-label">Semester</label>
                                <div>
                                    <select name="id_smt" class="form-control" style="max-width: 200px">
                                        @foreach( Sia::listSemester() as $smt )
                                            <option value="{{ $smt->id_smt }}" {{ $smt->id_smt == $konsen->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Mahasiswa</label>
                                <div>
                                    <input type="text" class="form-control" value="{{ $konsen->nim.' - '.$konsen->nm_mhs }}" disabled="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Kelas</label>
                                <div>
                                    <?php
                                        $kelas = Sia::listKelasKonsentrasi();
                                        $bagian = []
                                    ?>
                                    <select name="kelas" class="form-control" style="max-width: 200px">
                                        <option value="">Pilih kelas</option>
                                        @foreach( $kelas as $key => $val )
                                            @foreach( range('A', $val) as $bag )
                                                <option value="{{ $key }}-{{ $bag }}" {{ $key.'-'.$bag == $konsen->kelas ? 'selected':'' }}>{{ $key }}-{{ $bag }}</option>
                                            @endforeach
                                        @endforeach
                                        <option value="XII-G1" {{ 'XII-G1' == $konsen->kelas ? 'selected':'' }}>XII-G1</option>
                                        <option value="XII-H1" {{ 'XII-H1' == $konsen->kelas ? 'selected':'' }}>XII-H1</option>
                                        <option value="XII-H2" {{ 'XII-H2' == $konsen->kelas ? 'selected':'' }}>XII-H2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Pilih Konsentrasi</label>
                                <div>
                                    <select name="konsentrasi" class="form-control" style="max-width: 350px">
                                        <option value="">Pilih konsentrasi</option>
                                        @foreach( Sia::listKonsentrasi(61101) as $kon )
                                            <option value="{{ $kon->id_konsentrasi }}" {{ $kon->id_konsentrasi == $konsen->id_konsentrasi ? 'selected':'' }}>{{ $kon->nm_konsentrasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <hr>
                                <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> KEMBALI</a>
                                <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
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
    $(function () {
        'use strict';
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
    submit('dosen');

</script>
@endsection