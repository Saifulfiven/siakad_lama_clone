@extends('layouts.app')

@section('title','Learning Management System')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>{{ $r->nm_mk }} - {{ Sia::namaDosen($dosen->gelar_depan, $dosen->nm_dosen, $dosen->gelar_belakang) }}</a></li>
    </ul>
@endsection

@section('content')

<?php $id_jadwal = $r->id ?>

<ol class="breadcrumb">
    <li><a href="{{ route('lms') }}">LMS</a></li>
    <li class="active">{{ $r->kode_mk .' - '.$r->nm_mk }} - {{ Sia::namaDosen($dosen->gelar_depan, $dosen->nm_dosen, $dosen->gelar_belakang) }}</li>
</ol>

<div id="content" style="padding-top: 0">
    <div class="row" >
        <div class="col-md-12">
            <div class="col-lg-8 col-md-6 col-sm-6" style="padding-left: 0">
                <button class="btn btn-default toggle-forum" style="height: 33px;margin-top: 1px"><i class="fa fa-comments"></i> Forum <i class="karet fa fa-caret-down"></i></button>
            </div>
        </div>

        <br>
        <br>

        <div class="col-md-12 learning">

            {{ Rmt::alertError() }}

            <div class="forum" style="display: none">
                
                <div id="konten-forum"></div>

                <div class="forum-footer">
                    <button class="toggle-forum btn btn-theme btn-sm"><i class="fa fa-times"></i> Tutup</button>
                </div>
            </div>

            @for( $i = 0; $i <= $jml_pertemuan; $i++ )

                <div class="widget-chat">
                    <header>
                        <span class="chat-collapse pull-right" title="Collapse chat">
                                <i class="fa fa-minus"></i>
                        </span>
                        <h4 class="online">
                            @if ( $i == 0 )
                                <strong>INTRO</strong> {{ $r->nm_mk }}
                            @else
                                Pertemuan <strong>{{ $i }}</strong>
                            @endif
                        </h4>
                    </header>

                    <div class="chat-body">
                        <?php $data = Sia::getResources($i, $r->id, $dosen->id); ?>

                        @if ( count($data) > 0 )

                            @foreach( $data as $res )

                                @if ( $res->jenis == 'materi' )
                                    <a href="{{ route('lms_materi_view', ['id_materi' => $res->id_resource, 'id_dosen' => $dosen->id, 'file' => $res->file]) }}" target="_blank">
                                @elseif( $res->jenis == 'tugas' )
                                    <?php
                                        $selesai = App\JawabanTugas::where('id_tugas', $res->id_resource)
                                            ->where('id_peserta', Sia::sessionMhs())
                                            ->count(); 
                                    ?>
                                    <a href="{{ route('mhs_lms_tugas_detail', ['id_jadwal' => $r->id, 'id_tugas' => $res->id_resource, 'id_dosen' => $dosen->id]) }}">
                                @else
                                    <a class="catatan">
                                @endif

                                <div class="col-md-12 container-resources">
                                    @if ( $res->jenis != 'catatan' )
                                        <div class="icon-resources">
                                            <?php $icon = $res->jenis == 'materi' ? Rmt::icon($res->file) : $res->file; ?>
                                            <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                        </div>
                                        {{ $res->judul }}
                                        @if ( $res->jenis == 'tugas' )
                                            @if ( $selesai > 0 )
                                                &nbsp; &nbsp; <span class="label label-success">Telah dikerjakan</span>
                                            @endif
                                            
                                            <span class="label" style="color: red" id="time-remaining-<?= $res->id ?>">
                                                <i class="fa fa-spinner fa-spin"></i>
                                                </span>
                                        @endif
                                    @endif

                                    @if ( !empty($res->deskripsi) && $res->jenis != 'tugas' )
                                        <?= $res->jenis != 'catatan' ? '<br>':'' ?>
                                        {!! $res->deskripsi !!}
                                    @endif
                                </div>

                                </a>

                                @if ( $res->jenis == 'tugas' )
                                    <?php $tugas = App\Tugas::find($res->id_resource); ?>

                                    @if ( !empty($tugas->tgl_berakhir) )

                                        <script>
                                            
                                            // Set the date we're counting down to
                                            var countDownDate<?= $res->id ?> = new Date("{{ $tugas->tgl_berakhir }}").getTime();


                                            // Update the count down every 1 second
                                            var x<?= $res->id ?> = setInterval(function() {

                                              // Get today's date and time
                                              var now = new Date().getTime();
                                                
                                              // Find the distance between now and the count down date
                                              var distance = countDownDate<?= $res->id ?> - now;
                                                
                                              // Time calculations for days, hours, minutes and seconds
                                              var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                              var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                              var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                              var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                
                                              document.getElementById("time-remaining-<?= $res->id ?>").innerHTML = days + " hari " + hours + " jam "
                                              + minutes + " menit " + seconds + " detik ";
                                                
                                              // If the count down is over, write some text 
                                              if (distance < 0) {
                                                clearInterval(x<?= $res->id ?>);
                                                document.getElementById('time-remaining-<?= $res->id ?>').innerHTML = '';
                                              }
                                            }, 1000);
                                        </script>

                                    @else
                                        <script>
                                            document.getElementById('time-remaining-<?= $res->id ?>').innerHTML = '';
                                        </script>
                                    @endif
                                @endif

                            @endforeach
                        
                        @endif
                            
                    </div>
                </div>

            @endfor

        </div>
    </div>
</div>

@endsection

@section('registerscript')

<script>

    $(function () {
        $('.toggle-forum').click(function(){
            $('.toggle-forum').find('i.karet').toggleClass("fa-caret-down");
            $('.toggle-forum').find('i.karet').toggleClass("fa-caret-up");
            $('.forum').slideToggle();
        });
        
    });

    function getTopik(id_jadwal, id_dosen)
    {

        $('#konten-forum').html('<i class="fa fa-spinner fa-spin"></i>')
        $.ajax({
            url: '{{ route('lms_topik') }}',
            method: 'get',
            data : { 
                preventCache : new Date(), 
                id_jadwal:id_jadwal,
                id_dosen:id_dosen,
            },
            success: function(data){
                $('#konten-forum').html(data);
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

                var error = '<div style="color: red">'+pesan+'. <a href="javascript:;" onclick="getTopik(\''+id_jadwal+'\')"><i class="fa fa-refresh"></i> Refresh</a></div>';
                $('#konten-forum').html(error);
            }
        });
    }

    getTopik('<?= $id_jadwal ?>', '<?= $dosen->id ?>');
</script>
@endsection