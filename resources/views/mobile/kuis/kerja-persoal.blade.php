@extends('mobile.layouts.app')

@section('title','Kuis')

@section('heading')
<style>
    #content {
        font-size: 15px !important;
        padding: 0 30px 0;
    }
    .wizard-step .wizard-status {
       color: #444;
       font-size: 14px;  
    }
    .tab-content {
        padding: 0 15px 15px 15px;
    }
    .count-tryout {
        background: #3498db;
        margin: 0 auto;
        padding: 5px;
        color: #fff !important;
        font-size: 20px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>

<link type=text/css rel="stylesheet" href="{{ url('resources') }}/assets/css/radio.css">

@endsection


@section('content')

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
                <form action="{{ route('m_mhs_kuis_store') }}" id="rootwizard" method="post" class="wizard-step form-kuis">
                    
                    {{ csrf_field() }}
                    <input type="hidden" name="jumlah_soal" value="{{ $jumlah_soal }}">
                    <input type="hidden" name="id_kuis" value="{{ $kuis->id }}">
                    <input type="hidden" name="id_telah_kuis" value="{{ $telah_kuis->id }}">
                    <input type="hidden" name="id_mhs_reg" value="{{ $mhs->id }}">

                    {{ Rmt::alertError() }}

                    <ul style="display: none">
                        @foreach( $soal as $so )
                            <li><a href="#tab{{ $loop->iteration }}" data-toggle="tab">{{ $loop->iteration }}</a></li>
                        @endforeach
                    </ul>
                    <!-- <div class="progress progress-stripes progress-sm" style="margin:0">
                        <div class="progress-bar" data-color="primary"></div>
                    </div> -->

                    <div class="tab-content">

                        <?php $no = 0 ?>

                        @foreach( $soal as $so )

                            <?php

                                $no++;

                                $jawab = App\KuisHasil::where('id_peserta', $mhs->id)
                                        ->where('id_kuis_soal', $so->id_kuis_soal)
                                        ->first();

                                $jawaban = !empty($jawab) ? $jawab->jawaban : '';

                            ?>

                            <div class="tab-pane fade" id="tab{{ $loop->iteration }}">
                            
                                @if ( $so->jenis_soal == 'pg' )
                                    <input type="hidden" name="jenis[{{ $so->id_kuis_soal }}]" value="pg">

                                    <br>
                                    {!! $so->soal !!}
                                    <div class="jawaban" data-id_kuis_soal="{{ $so->id_kuis_soal }}" data-jenis="{{ $so->jenis_soal }}">
                                        <ul class="custom-radio">

                                            <li>
                                                <input type="radio" id="a-{{ $so->id_kuis_soal }}" value="a" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'a' ? 'checked':'' }}>
                                                <label for="a-{{ $so->id_kuis_soal }}">{{ $so->jawaban_a }}</label>
                                                <div class="check"></div>
                                            </li>

                                            <li>
                                                <input type="radio" id="b-{{ $so->id_kuis_soal }}" value="b" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'b' ? 'checked':'' }}>
                                                <label for="b-{{ $so->id_kuis_soal }}">{{ $so->jawaban_b }}</label>
                                                <div class="check"></div>
                                            </li>

                                            <li>
                                                <input type="radio" id="c-{{ $so->id_kuis_soal }}" value="c" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'c' ? 'checked':'' }}>
                                                <label for="c-{{ $so->id_kuis_soal }}">{{ $so->jawaban_c }}</label>
                                                <div class="check"></div>
                                            </li>
                                            @if ( !empty($so->jawaban_d) )
                                                <li>
                                                    <input type="radio" id="d-{{ $so->id_kuis_soal }}" value="d" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'd' ? 'checked':'' }}>
                                                    <label for="d-{{ $so->id_kuis_soal }}">{{ $so->jawaban_d }}</label>
                                                    <div class="check"></div>
                                                </li>
                                            @endif

                                            @if ( !empty($so->jawaban_e) )
                                                <li>
                                                    <input type="radio" id="e-{{ $so->id_kuis_soal }}" value="e" name="jawaban[{{ $so->id_kuis_soal }}]" {{ $jawaban == 'e' ? 'checked':'' }}>
                                                    <label for="e-{{ $so->id_kuis_soal }}">{{ $so->jawaban_e }}</label>
                                                    <div class="check"></div>
                                                </li>
                                            @endif
                                          
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>

                                @else

                                    <input type="hidden" name="jenis[{{ $so->id_kuis_soal }}]" value="es">
                                    <br>
                                    {!! $so->soal !!}
                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="jawaban" data-id_kuis_soal="{{ $so->id_kuis_soal }}" data-jenis="{{ $so->jenis_soal }}">
                                        <textarea name="jawaban[{{ $so->id_kuis_soal }}]" style="height: 100px" class="form-control">{{ $jawaban }}</textarea>
                                    </div>
                                @endif

                            </div>

                        @endforeach

                        <hr>
                        <footer class="row">
                            <div class="col-sm-12">
                                <section class="wizard">
                                    <div class="wizard-status pull-left">Soal : <span></span></div>
                                    <center>
                                        <button type="button" class="previous" style="visibility: hidden;"></button>
                                        <button type="button" class="btn btn-theme next">Selanjutnya <i class="fa fa-angle-right"></i></button>
                                        <button type="button" style="display: none" id="btn-submit-kuis" class="btn btn-theme"><i class="fa fa-save"></i> Simpan</button>
                                    </center>
                                </section>
                            </div>
                        </footer>
                    </div>
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

        $('#rootwizard').bootstrapWizard({
            tabClass:"nav-wizard",
            onNext: function(tab, navigation, index) {
                var err = false;

                var id_kuis_soal = $('#rootwizard').find('#tab'+index+' > div.jawaban').data('id_kuis_soal');
                var jenis = $('#rootwizard').find('#tab'+index+' > div.jawaban').data('jenis');
                if ( jenis === 'pg' ) {
                    var jawaban = $("input[name='jawaban["+id_kuis_soal+"]']:checked").val();
                } else {
                    var jawaban = $("textarea[name='jawaban["+id_kuis_soal+"]']").val();
                }

                $('.next').attr('disabled','');
                $('.next').html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');

                $.ajax({
                    url: '{{ route('m_kuis_store_single') }}',
                    type: 'POST',
                    data: { id_kuis_soal: id_kuis_soal, jenis: jenis, jawaban: jawaban, jml_soal: '{{ $jumlah_soal }}',id_mhs_reg : '{{ $mhs->id }}' },
                    success: function(result){
                        setTimeout(function(){
                            $('.next').removeAttr('disabled','');
                        }, 2000);

                        $('.next').html('Selanjutnya <i class="fa fa-angle-right"></i>');
                    },
                    error: function(data,status,msg){
                        alert(msg);
                        $('.next').removeAttr('disabled','');
                        $('.next').html('Selanjutnya <i class="fa fa-angle-right"></i>');
                        $('.previous').trigger('click');
                    }
                });

            },
            onTabShow: function(tab, navigation, index) {

                tab.prevAll().addClass('completed');
                tab.nextAll().removeClass('completed');
                if(tab.hasClass("active")){
                    tab.removeClass('completed');
                }
                var $total = navigation.find('li').length;
                var $current = index+1;

                $('#rootwizard').find('.wizard-status span').html($current+" / "+$total);
                
                var total = '{{ $no }}';
                if ( $current === parseInt(total) ) {
                    $('#btn-submit-kuis').show();
                    $('.next').hide();
                }
            }
        });

        $('#btn-submit-kuis').click(function(){
            if ( confirm('Anda yakin telah selesai mengerjakan kuis ini.?') ) {
                $('.form-kuis').submit();
            } else {
                return;
            }
        });

        var sisa_waktu = '{{ $telah_kuis->sisa_waktu }}';
    
        setInterval(function(){
        
            sisa_waktu = sisa_waktu - 1;
            $.get('{{ route('m_kuis_update_waktu') }}', {id:'{{ $telah_kuis->id }}', waktu: sisa_waktu});
        
        }, 60 * 1000 );

        $(".count-tryout").jCounter({
            twoDigits: 'on',
            customDuration: '{{ $telah_kuis->sisa_waktu * 60 }}',
            callback: function() { 
                $('.form-kuis').submit();
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
                    window.location.href="{{ route('kuis') }}?id={{ $kuis->id }}&id_jdk={{ $r->id }}&nim={{ $mhs->nim }}";
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

        $('.form-kuis').ajaxForm(options);
        
    });


</script>
@endsection