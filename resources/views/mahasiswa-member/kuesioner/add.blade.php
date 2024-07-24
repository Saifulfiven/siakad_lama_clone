@extends('layouts.app')

@section('title','Kuesioner')

@section('content')

<div id="content">
    <div class="row">

        <div class="col-md-12">

            {{ Rmt::AlertSuccess() }}

            <div class="row">

                <div class="col-md-12" style="padding-bottom: 40px">
                    <form action="{{ route('mhs_kues_store') }}" method="post" id="form-kues">

                        {{ csrf_field() }}
                        <input type="hidden" name="id_dosen" value="{{ $dosen->id }}">
                        <input type="hidden" name="id_jdk" value="{{ Request::get('id_jdk') }}">
                        <input type="hidden" name="id_mk" value="{{ $mk->id }}">
                        <input type="hidden" name="kode_kls" value="{{ Request::get('kls') }}">
                        <input type="hidden" name="ruangan" value="{{ Request::get('rgn') }}">
                        <input type="hidden" name="id_kues_jadwal" value="{{ Request::get('kues_jadwal') }}">

                        <section class="panel">
                            <header class="panel-heading xs">
                                <h2>Pengisian Kuesioner</h2>
                            </header>
                            <div class="panel-body">
                                
                                <table>
                                    <tr>
                                        <td width="150">Nama Matakuliah</td>
                                        <td width="250">: {{ $mk->nm_mk }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nama Dosen</td>
                                        <td>: {{ Sia::namaDosen($dosen->gelar_depan, $dosen->nm_dosen, $dosen->gelar_belakang) }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">Kelas</td>
                                        <td>: {{ Request::get('kls') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="100">Ruangan</td>
                                        <td>: {{ Request::get('rgn') }}</td>
                                    </tr>

                                </table>

                                <br>
                                
                                <div class="alert alert-info">
                                    <b>PETUNJUK</b>
                                    <ol style="list-style: decimal;padding-left: 20px">
                                        <li>Isilah kuesioner ini sesuai yang Saudara alami.
                                             Masukan Saudara sangat berguna untuk meningkatkan
                                         kualitas perkuliahan</li>
                                        <li>Klik/Tap pada skor yang Saudara pilih</li>
                                    </ol>
                                </div>
                            </div>
                        </section>

                        @foreach( $komponen as $ko )
                            <section class="panel">
                                <header class="panel-heading xs">
                                    <h2 style="font-size: 16px"><strong>{{ $ko->judul }}</strong></h2>
                                </header>
                                <div class="panel-body" style="padding-top: 0">
                                    <?php
                                        $isi = DB::table('kues_komponen_isi')
                                                ->where('id_komponen', $ko->id)
                                                ->orderBy('urutan')
                                                ->get();
                                    ?>
                                    <ol style="list-style: decimal;padding-left: 10px;">
                                        @foreach( $isi as $is )

                                            @if ( $ko->jenis == 'pg' )

                                                <input type="hidden" name="pg" value="true">

                                                <li style="margin-top: 10px">

                                                    <div style="font-size: 16px;padding-bottom: 5px"> {!! $is->pertanyaan !!}</div>

                                                    <ul>
                                                        <li>
                                                            <label style="display: inline-flex;">
                                                                <input type="radio" name="penilaian[{{ $is->id}}]" value="5">
                                                                <b style="margin-left: 10px;padding-top: 2px">Sangat baik</b>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label style="display: inline-flex;">
                                                                <input type="radio" name="penilaian[{{ $is->id}}]" value="4">
                                                                <b style="margin-left: 10px;padding-top: 2px">Baik</b>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label style="display: inline-flex;">
                                                                <input type="radio" name="penilaian[{{ $is->id}}]" value="3">
                                                                <b style="margin-left: 10px;padding-top: 2px">Cukup</b>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label style="display: inline-flex;">
                                                                <input type="radio" name="penilaian[{{ $is->id}}]" value="2">
                                                                <b style="margin-left: 10px;padding-top: 2px">Kurang</b>
                                                            </label>
                                                        </li>
                                                        <li>
                                                            <label style="display: inline-flex;">
                                                                <input type="radio" name="penilaian[{{ $is->id}}]" value="1">
                                                                <b style="margin-left: 10px;padding-top: 2px">Sangat tidak baik/tidak pernah</b>
                                                            </label>
                                                        </li>
                                                        
                                                    </ul>
                                                </li>
                                            @else

                                                <input type="hidden" name="pg" value="true">
                                                
                                                <li style="margin-top: 10px">

                                                    <div style="font-size: 15px;padding-bottom: 5px"> {!! $is->pertanyaan !!}</div>

                                                    <ul>
                                                        <li>
                                                            <textarea name="penilaian_text[{{ $is->id }}]" class="form-control"></textarea>
                                                        </li>
                                                    </ul>
                                                </li>

                                            @endif
                                        @endforeach
                                    </ol>
                                </div>
                            </section>
                        @endforeach

                        <button class="btn btn-sm btn-primary btn-submit"><i class="fa fa-save"></i> SIMPAN KUESIONER</button>
                    
                    </form>

                </div>

            </div>

        </div>

    </div>
</div>

<div id="modal-error" class="modal fade" tabindex="-1" style="top:30%">
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

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

$(document).ready(function () {
    $('input').iCheck({
        radioClass: 'iradio_square-green'
    });

    var options = {
        beforeSend: function() 
        {
            $('#overlay').show();
            $(".btn-submit").attr('disabled','');
            $(".btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
        },
        success:function(data, status, message) {
            if ( data.error == 1 ) {
                showMessage(data.msg);
            } else {
                window.location.href = '{{ route('mhs_kues') }}';
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

    $('#form-kues').ajaxForm(options);
});


function showMessage(pesan)
{
    $('#overlay').hide();
    $('.ajax-message').html(pesan);
    $('#modal-error').modal('show');

    $('.btn-submit').removeAttr('disabled');
    $('.btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN KUESIONER');
}

</script>
@endsection