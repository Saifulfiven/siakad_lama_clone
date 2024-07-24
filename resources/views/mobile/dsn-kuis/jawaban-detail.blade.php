@extends('mobile.layouts.app')

@section('title','Kuis : Jawaban Peserta')

@section('heading')
<style>
    #content {
        font-size: 15px !important;
    }
    .jawaban {
        position: relative;
        padding: 5px 5px 10px 20px;
    }
    .jawaban .simbol {
        position: absolute;
        top: 5px;
        left: 2px;
        font-weight: bold;
    }
    .benar {
        color: #0f31ea;
    }
    .dijawab {
        position: absolute;
        top: -1px;
        left: -2px;
        font-size: 22px;
        color: red;
    }
</style>

@endsection


@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">

            {{ Rmt::alertError() }}

            <section class="panel">
                <header class="panel-heading">
                    {{ $peserta->nm_mhs }} - {{ $peserta->nim }}
                </header>
                <div class="panel-body" style="padding-top: 13px">
                    <a href="{{ route('m_kuis_jawaban', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" class="btn btn-success btn-sm btn-block btn-loading">Kembali</a>
                    <hr>

                    <b>{{ $kuis->judul }}</b><br>
                    {!! $kuis->ket !!}
                </div>
            </section>

            @foreach( $soal as $so )

                <?php

                    $jawab = App\KuisHasil::where('id_peserta', $peserta->id_mhs_reg)
                            ->where('id_kuis_soal', $so->id_kuis_soal)
                            ->first();

                    $jawaban = !empty($jawab) ? $jawab->jawaban : '';
                    $nilai = !empty($jawab) ? $jawab->penilaian : '';
                    $id = !empty($jawab) ? $jawab->id : '';

                ?>

                @if ( $so->jenis_soal == 'pg' )
                    <section class="panel">
                        <header class="panel-heading">
                            <b>Soal {{ $loop->iteration }}</b>
                            @if ( $jawaban == $so->jawaban_benar )
                                <i class="fa fa-check" style="color: green; font-size: 20px"></i>
                            @else
                                <i class="fa fa-times" style="color: red; font-size: 20px"></i>
                            @endif
                        </header>
                        <div class="panel-body" style="padding-top: 13px">
                            {!! $so->soal !!}
                            <div class="clearfix"></div>
                            <br>

                            <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'a' ? 'benar':'' }}">
                                @if ( $jawaban == 'a' )
                                    <div class="dijawab">
                                        <i class="fa fa-times"></i>
                                    </div>
                                @endif
                                <div class="simbol">A.</div> {{ $so->jawaban_a }}
                            </div>
                            <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'b' ? 'benar':'' }}">
                                @if ( $jawaban == 'b' )
                                    <div class="dijawab">
                                        <i class="fa fa-times"></i>
                                    </div>
                                @endif
                                <div class="simbol">B.</div> {{ $so->jawaban_b }}
                            </div>


                            <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'c' ? 'benar':'' }}">
                                @if ( $jawaban == 'c' )
                                    <div class="dijawab">
                                        <i class="fa fa-times"></i>
                                    </div>
                                @endif
                                <div class="simbol">C.</div> {{ $so->jawaban_c }}
                            </div>
                            <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'd' ? 'benar':'' }}">
                                @if ( $jawaban == 'd' )
                                    <div class="dijawab">
                                        <i class="fa fa-times"></i>
                                    </div>
                                @endif
                                <div class="simbol">D.</div> {{ $so->jawaban_d }}
                            </div>

                            <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'e' ? 'benar':'' }}">
                                @if ( $jawaban == 'e' )
                                    <div class="dijawab">
                                        <i class="fa fa-times"></i>
                                    </div>
                                @endif
                                <div class="simbol">E.</div> {{ $so->jawaban_e }}
                            </div>
                        </div>
                    </section>
                @else
                    <section class="panel">
                        <header class="panel-heading">
                            <b>Soal {{ $loop->iteration }}</b> &nbsp;
                            NILAI : &nbsp; <a href="#" class="nilai" data-type="text" data-pk="{{ $so->id_kuis_soal }}" data-title="Masukkan Nilai (Rentang 1 - 100)">{{ empty($nilai) ? '0':$nilai }}</a> <small style="font-size: 12px">&nbsp; &nbsp; <i class="fa fa-arrow-left"></i> klik untuk mengubah nilai</small>
                        </header>
                        <div class="panel-body" style="padding-top: 13px">
                            {!! $so->soal !!}
                            <div class="clearfix"></div>
                            <b>Keyword:</b> <i>{{ $so->keyword }}</i>
                            <br>
                            <br>
                            <b>Jawaban:</b><br>
                            {{ $jawaban }}
                            
                        </div>
                    </section>
                @endif
            @endforeach

            <a href="{{ route('m_kuis_jawaban', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" class="btn btn-success btn-sm btn-block btn-loading">Kembali</a>
            
        </div>
      </div>
    </div>

@endsection

@section('registerscript')

<link href="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.css" rel="stylesheet"/>
<script src="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.min.js"></script>
<script>

    $(function () {
        'use strict';

        $('.nilai').editable({
            url: '{{ route('m_kuis_grade', ['id_jadwal' => $r->id, 'id_peserta' => $peserta->id_mhs_reg]) }}',
            name: 'nilai',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                return params;
            },
            success: function(response, newValue) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(response,value)
            {
                console.log(JSON.stringify(response));
                var respon = parseObj(response.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = response.statusText;
                }
                showMessage2('', pesan);
            }
        });

        
    });

    function jawaban(id_jawaban, peserta)
    {
        $('#modal-jawaban').modal('show');
        $('#modal-jawaban .modal-body').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
        $('#modal-jawaban .modal-title').html('Detail Jawaban <b>'+peserta+'</b>');

        $.ajax({
            url: '{{ route('dsn_lms_tugas_jawaban') }}',
            data : { 
                preventCache : new Date(), 
                id_jawaban: id_jawaban
            },
            success: function(data){
                $('#modal-jawaban .modal-body').html(data);
                $('#modal-jawaban').modal('show');
            },
            error: function(data,status,msg){
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = msg;
                }
                showMessage2('',pesan);
            }
        });
        
    }
</script>
@endsection