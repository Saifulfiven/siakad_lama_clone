@extends('layouts.app')

@section('title','Learning Management System')

@section('content')

<?php $id_jadwal = $r->id ?>
<?php $prodi = Sia::sessionMhs('prodi') ?>

<ol class="breadcrumb">
    <li><a href="{{ route('mhs_lms') }}">Matakuliah</a></li>
    <li class="active">{{ $r->kode_mk .' - '.$r->nm_mk }}</li>
</ol>

<div id="content" style="padding-top: 0">
    <div class="row" >

        <div class="col-md-12 learning">

            {{ Rmt::alertError() }}

            
            <button class="btn btn-default toggle-forum" style="height: 33px;margin-top: 1px"><i class="fa fa-comments"></i> Forum <i class="karet fa fa-caret-down"></i></button>
            <div class="forum" style="display: none">
            
                <div id="konten-forum"></div>

                <div class="forum-footer">
                    <button class="toggle-forum btn btn-theme btn-sm"><i class="fa fa-times"></i> Tutup</button>
                    <button class="btn btn-theme-inverse btn-sm" data-toggle="modal" data-target="#modal-add-topik"><i class="fa fa-comments-o"></i> Bertanya ke Forum / Dosen</button>
                </div>
            </div>

            @for( $i = 0; $i <= $jml_pertemuan; $i++ )

                @if ( $i == 0 )

                    @if ( !empty($id_dosen_arr) )
                        
                        @foreach( $id_dosen_arr as $key => $val )
                            <section class="panel">
                                <div class="panel-body">
                                    <?php Rmt::makeIntro($r->id, $val, $r->nm_mk) ?>
                                </div>
                            </section>
                        @endforeach

                    @else
                   
                        <section class="panel">
                            <div class="panel-body">
                                <?php Rmt::makeIntro($r->id, $id_dosen, $r->nm_mk) ?>
                            </div>
                        </section>

                    @endif

                @endif

                <div class="widget-chat">
                    <header>
                        <span class="chat-collapse pull-right" title="Collapse chat">
                            <i class="fa fa-minus"></i>
                        </span>
                        <h4 class="online">
                            @if ( $r->id_prodi == '61101' )

                                @if ( $i == 0 )
                                    Intro: <small style="color: #fff">{{ $r->nm_mk }} - {{ $r->kode_kls }}</small>
                                @else
                                    Pertemuan <strong>{{ $i }}</strong>
                                @endif

                            @else

                                @if ( $r->jenis == 1 )

                                    @if ( $i == 0 )
                                        Intro: <small style="color: #fff">{{ $r->nm_mk }} - {{ $r->kode_kls }}</small>
                                    @elseif ( $i == 8 )
                                        <strong>Ujian Tengah Semester</strong>
                                    @elseif ( $i == 16 )
                                        <strong>Ujian Akhir Semester</strong>
                                    @else
                                        @if ( $jml_pertemuan > 14 && $i > 7 )
                                            Pertemuan <strong>{{ $i - 1 }}</strong>
                                        @else
                                            Pertemuan <strong>{{ $i }}</strong>
                                        @endif
                                    @endif

                                @else

                                    @if ( $i == 0 )
                                        Intro: <small style="color: #fff">{{ $r->nm_mk }} - {{ $r->kode_kls }}</small>
                                    @else
                                        @if ( $jml_pertemuan > 14 && $i > 7 )
                                            Pertemuan <strong>{{ $i - 1 }}</strong>
                                        @else
                                            Pertemuan <strong>{{ $i }}</strong>
                                        @endif
                                    @endif

                                @endif

                            @endif
                        </h4>
                    </header>

                    <div class="chat-body">
                        

                        @if ( !empty($id_dosen_arr) )
                            <?php $data = Sia::getResources2($i, $r->id, $id_dosen_arr); ?>
                        @else
                            <?php $data = Sia::getResources($i, $r->id, $id_dosen); ?>
                        @endif

                        @if ( count($data) > 0 )

                            @foreach( $data as $res )

                                @if ( $res->jenis == 'materi' )
                                    <a href="{{ route('mhs_lms_materi_view', ['id_materi' => $res->id_resource, 'id_dosen' => $res->id_dosen, 'file' => $res->file]) }}" target="_blank">
                                @elseif( $res->jenis == 'tugas' )
                                    <?php
                                        $selesai = App\JawabanTugas::where('id_tugas', $res->id_resource)
                                            ->where('id_peserta', Sia::sessionMhs())
                                            ->count(); 
                                    ?>
                                    <a href="{{ route('mhs_lms_tugas_detail', ['id_jadwal' => $r->id, 'id_tugas' => $res->id_resource, 'id_dosen' => $res->id_dosen]) }}">
                                @elseif ( $res->jenis == 'kuis')
                                    
                                    <a href="{{ route('mhs_kuis', ['id_jdk' => $r->id, 'id_kuis' => $res->id_resource, 'id_dosen' => $res->id_dosen]) }}">
                                @elseif ( $res->jenis == 'video')
                                    
                                    <a href="{{ route('mhs_video', ['id_jdk' => $r->id, 'id_video' => $res->id_resource, 'id_dosen' => $res->id_dosen]) }}">

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

                                        @if ( $res->jenis2 == 'ujian' )
                                            &nbsp; <span class="label label-danger">Ujian</span>
                                        @endif 
                                            
                                        @if ( $res->jenis == 'tugas' )
                                            @if ( $selesai > 0 )
                                                &nbsp; &nbsp; <span class="label label-success">Telah dikerjakan</span>
                                            @endif
                                            
                                            <span class="label" style="color: red" id="time-remaining-<?= $res->id ?>">
                                                <i class="fa fa-spinner fa-spin"></i>
                                                </span>
                                        @endif

                                        @if ( $res->jenis == 'kuis' )

                                            <?php $telah_kuis = Sia::telahMengerjakanKuis(Sia::sessionMhs(), $res->id_resource); ?>

                                            @if ( $telah_kuis > 0 )
                                                &nbsp; &nbsp; <span class="label label-success">Telah dikerjakan</span>
                                            @else
                                                <span class="label" style="color: red" id="time-remaining-<?= $res->id ?>">
                                                    <i class="fa fa-spinner fa-spin"></i>
                                                </span>
                                            @endif
                                        @endif

                                    @endif

                                    @if ( !empty($res->deskripsi) && $res->jenis != 'tugas' )
                                        @if ( $res->jenis != 'kuis' && $res->jenis != 'video' )
                                            <?= $res->jenis != 'catatan' ? '<br>':'' ?>
                                            {!! $res->deskripsi !!}
                                        @endif
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

                                @if ( $res->jenis == 'kuis' && $telah_kuis == 0 )
                                    <?php $kuis = App\Kuis::find($res->id_resource); ?>

                                    @if ( !empty($kuis->tgl_tutup) )

                                        <script>
                                            
                                            // Set the date we're counting down to
                                            var countDownDate<?= $res->id ?> = new Date("{{ $kuis->tgl_tutup }}").getTime();


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

<div id="modal-add-topik" class="modal fade" data-width="700" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="myModalLabel">Buat Topik</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body" style="padding-top: 0">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('mhs_lms_topik_store') }}" method="post" id="form-add-topik">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                    <input type="hidden" name="id_dosen" value="{{ $id_dosen }}">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" class="form-control" name="judul">
                    </div>
                    <div class="form-group">
                        <label>Konten</label>
                        <div>
                        <textarea name="konten" class="form-control" rows="10" cols="80"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm" id="btn-submit-add-topik"><i class="fa fa-save"></i> Simpan</button>
                        <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                    </div>
                </form>                                    
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-topik" class="modal fade" data-width="700" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Ubah Topik</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body" style="padding-top: 0;height: 340px">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('mhs_lms_topik_update') }}" method="post" id="form-edit-topik">
                    <div id="edit-konten"></div>
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
        $('.toggle-forum').click(function(){
            $('.toggle-forum').find('i.karet').toggleClass("fa-caret-down");
            $('.toggle-forum').find('i.karet').toggleClass("fa-caret-up");
            $('.forum').slideToggle();
        });

        @if ( Session::has('success') )
            setTimeout(() => {
                showSuccess('<?= Session::get('success') ?>');
            }, 100);
        @endif
        
    });

    function submit(modul)
    {
        
        var options = {
            beforeSend: function() 
            {

                $('#caplet-overlay').show();
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2(modul, data.msg);
                } else {
                    window.location.reload();
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
                showMessage2(modul, pesan);
            }
        };

        $('#form-'+modul).ajaxForm(options);
    }
    submit('add-topik');
    submit('edit-topik');

    function ubahJenis(id)
    {
        window.location.href='{{ route('mhs_jdk') }}?ubah_jenis='+id;
    }

    function ubahTopik(id, judul, konten){
        $('#modal-edit-topik').modal('show');
        $('#edit-konten').html('<center><br><i class="fa fa-spinner fa-spin"></i></center>');

        $.get('{{ route('mhs_lms_topik_edit') }}/'+id, function(data){
            $('#edit-konten').html(data);
        }).fail(function(){
            showMessage2('', 'Terjadi kesalahan, mohon muat ulang halaman ini');
        });
    }

    function getTopik(id_jadwal, id_dosen)
    {

        $('#konten-forum').html('<i class="fa fa-spinner fa-spin"></i>')
        $.ajax({
            url: '{{ route('mhs_lms_topik') }}',
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

    getTopik('<?= $id_jadwal ?>', '<?= $id_dosen ?>');

</script>
@endsection