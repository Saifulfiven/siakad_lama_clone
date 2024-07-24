@extends('layouts.app')

@section('title','Kuis')

@section('heading')
<style>
    #content {
        font-size: 15px !important;
    }
    .count-tryout {
        background: #3498db;
        margin: 0 auto;
        padding: 10px;
        color: #fff !important;
        font-size: 24px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>

<link type=text/css rel="stylesheet" href="{{ url('resources') }}/assets/css/radio.css">

@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>{{ $r->kode_kls .' - '.$r->nm_mk }}</a></li>
    </ul>
@endsection

@section('content')
    <ol class="breadcrumb">
        <li><a href="{{ route('mhs_lms') }}">Matakuliah</a></li>
        <li><a href="{{ route('mhs_lms_detail', ['id_jdk' => $r->id, 'id_dosen' => $id_dosen]) }}">{{ $r->kode_mk .' - '.$r->nm_mk }}</a></li>
        <li><a href="{{ route('mhs_kuis', ['id_jdk' => $r->id, 'id_kuis' => $kuis->id, 'id_dosen' => $id_dosen]) }}">{{ $kuis->judul }}</a></li>
    </ol>

    <div id="content">
      <div class="row">

        <div class="col-md-12">
            <div id="tryout" class="count-tryout">
                <center>
                    <span class="hours">00</span> :
                    <span class="minutes">00</span> :
                    <span class="seconds">00</span>
                </center>
            </div>

            <form action="{{ route('mhs_kuis_store') }}" id="form-kuis" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="jumlah_soal" value="{{ $jumlah_soal }}">
                <input type="hidden" name="id_kuis" value="{{ $kuis->id }}">
                <input type="hidden" name="id_telah_kuis" value="{{ $telah_kuis->id }}">
                <input type="hidden" name="id_peserta" value="{{ Sia::sessionMhs() }}">

                {{ Rmt::alertError() }}

                @foreach( $soal as $so )

                    <?php

                        $jawab = App\KuisHasil::where('id_peserta', Sia::sessionMhs())
                                ->where('id_kuis_soal', $so->id_kuis_soal)
                                ->first();

                        $jawaban = !empty($jawab) ? $jawab->jawaban : '';
                    ?>

                    @if ( $so->jenis_soal == 'pg' )

                        <input type="hidden" name="jenis[{{ $so->id_kuis_soal }}]" value="pg">

                        <section class="panel">
                            <header class="panel-heading">
                                <b>Soal {{ $loop->iteration }}</b>
                            </header>
                            <div class="panel-body" style="padding-top: 13px">
                                {!! $so->soal !!}
                                <div class="clearfix"></div>
                                
                                <div class="jawaban">
                                    <ul class="custom-radio">

                                        <li>
                                            <input type="radio" id="a-{{ $so->id_kuis_soal }}" value="a" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'a' ? 'checked':'' }} onclick="store('{{ $so->id_kuis_soal }}','pg',this.value)">
                                            <label for="a-{{ $so->id_kuis_soal }}">{{ $so->jawaban_a }}</label>
                                            <div class="check"></div>
                                        </li>

                                        <li>
                                            <input type="radio" id="b-{{ $so->id_kuis_soal }}" value="b" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'b' ? 'checked':'' }} onclick="store('{{ $so->id_kuis_soal }}','pg',this.value)">
                                            <label for="b-{{ $so->id_kuis_soal }}">{{ $so->jawaban_b }}</label>
                                            <div class="check"></div>
                                        </li>

                                        <li>
                                            <input type="radio" id="c-{{ $so->id_kuis_soal }}" value="c" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'c' ? 'checked':'' }} onclick="store('{{ $so->id_kuis_soal }}','pg',this.value)">
                                            <label for="c-{{ $so->id_kuis_soal }}">{{ $so->jawaban_c }}</label>
                                            <div class="check"></div>
                                        </li>

                                        @if ( !empty($so->jawaban_d) )
                                            <li>
                                                <input type="radio" id="d-{{ $so->id_kuis_soal }}" value="d" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'd' ? 'checked':'' }} onclick="store('{{ $so->id_kuis_soal }}','pg',this.value)">
                                                <label for="d-{{ $so->id_kuis_soal }}">{{ $so->jawaban_d }}</label>
                                                <div class="check"></div>
                                            </li>
                                        @endif

                                        @if ( !empty($so->jawaban_e) )
                                            <li>
                                                <input type="radio" id="e-{{ $so->id_kuis_soal }}" value="e" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'e' ? 'checked':'' }} onclick="store('{{ $so->id_kuis_soal }}','pg',this.value)">
                                                <label for="e-{{ $so->id_kuis_soal }}">{{ $so->jawaban_e }}</label>
                                                <div class="check"></div>
                                            </li>
                                        @endif
                                      
                                    </ul>
                                </div>
                            </div>
                        </section>

                    @else

                        <input type="hidden" name="jenis[{{ $so->id_kuis_soal }}]" value="es">
                        
                        <section class="panel">
                            <header class="panel-heading">
                                <b>Soal {{ $loop->iteration }}</b>
                            </header>
                            <div class="panel-body" style="padding-top: 13px">
                                {!! $so->soal !!}
                                <div class="clearfix"></div>
                                <br>
                                <textarea onblur="store('{{ $so->id_kuis_soal }}','es',this.value)" name="jawaban[{{ $so->id_kuis_soal }}]" style="height: 100px" class="form-control">{{ $jawaban }}</textarea>
                            </div>
                        </section>

                    @endif
                @endforeach

                <section class="panel">
                    <div class="panel-body" style="padding-top: 13px;">
                        <center>
                            <button type="button" id="btn-submit-kuis" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                        </center>
                    </div>
                </section>
            </form>

        </div>

      </div>
    </div>

@endsection

@section('registerscript')
<script src="{{ url('resources/assets/js/jquery.jCounter-0.1.4.js') }}"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    $(function () {
        'use strict';
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#btn-submit-kuis').click(function(){
            if ( confirm('Anda yakin telah selesai mengerjakan kuis ini.?') ) {
                $('#form-kuis').submit();
            } else {
                return;
            }
        });

        var sisa_waktu = '{{ $telah_kuis->sisa_waktu }}';
    
        setInterval(function(){
        
            sisa_waktu = sisa_waktu - 1;
            $.get('{{ route('mhs_kuis_update_waktu') }}', {id:'{{ $telah_kuis->id }}', waktu: sisa_waktu});
        
        }, 60 * 1000 );

        $(".count-tryout").jCounter({
            twoDigits: 'on',
            customDuration: '{{ $telah_kuis->sisa_waktu * 60 }}',
            callback: function() { 
                $('#form-kuis').submit();
            }
        });

        var options = {
            beforeSend: function() 
            {
                $('#caplet-overlay').show();
                $("#btn-submit-kuis").attr('disabled','');
                $("#btn-submit-kuis").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2('kuis', data.msg);
                } else {
                    window.location.href="{{ route('mhs_kuis', ['id_jdk' => $r->id, 'id_kuis' => $kuis->id, 'id_dosen' => $id_dosen]) }}";
                }
            },
            error: function(data, status, message)
            {
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                $("#close-error").show();
                showMessage2('kuis', pesan);
            }
        }; 

        $('#form-kuis').ajaxForm(options);
        
    });

    function store(id_kuis_soal,jenis,jawaban)
    {
        $.ajax({
            url: '{{ route('mhs_kuis_store_single') }}',
            type: 'POST',
            data: { id_kuis_soal: id_kuis_soal, jenis: jenis, jawaban: jawaban, jml_soal: '{{ $jumlah_soal }}' },
            success: function(result){

            },
            error: function(data,status,msg){
                console.log(msg);
            }
        });
    }

</script>
@endsection