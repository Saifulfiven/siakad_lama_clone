@extends('mobile.layouts.app')

@section('title','Video Detail')

@section('heading')
<style>
[style*="--aspect-ratio"] > :first-child {
  width: 100%;
}
[style*="--aspect-ratio"] > img {  
  height: auto;
}
@supports (--custom:property) {
  [style*="--aspect-ratio"] {
    position: relative;
  }
  [style*="--aspect-ratio"]::before {
    content: "";
    display: block;
    padding-bottom: calc(100% / (var(--aspect-ratio)));
  }  
  [style*="--aspect-ratio"] > :first-child {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
  }  
}
</style>
@endsection


@section('content')
    <div id="content">
      <div class="row">
        <div class="col-md-12">
            <section class="panel">
                <header class="panel-heading">
                    {{ $video->judul }} - ID Video : {{ $video->video_id }}
                    <div class="pull-right">
                        <a href="{{ route('dsnm_lms', ['id_jdk' => $r->id, 'id_dosen' => Session::get('m_id_dosen')]) }}" class="btn-loading btn btn-success btn-xs">Kembali</a>
                    </div>
                </header>
                <div class="panel-body" style="padding-top: 13px">
                    @if ( !empty($video->ket) )
                        {!! $video->ket !!}
                        <hr>
                    @endif
                    <div class="row">
                        <div class="col-md-12">

                                @if ( $video->siap == 'n' )
                                    <div id="loading">
                                        <center>
                                            <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                                            Memeriksa ketersediaan video
                                        </center>
                                    </div>
                                    <div style="--aspect-ratio: 16/9;" id="player" style="display: none">
                                @else
                                    <div style="--aspect-ratio: 16/9;">
                                @endif

                                    <iframe frameborder="0" style="position: absolute; top:0;left:0;width:100%;height: 100%; border: none" width="1600" height="900" src="https://www.youtube.com/embed/{{ $video->video_id }}" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>

                                <div class="alert alert-info" style="display: none">
                                    Saat ini video masih diproses oleh youtube, video anda akan segera ditampilkan setelah diproses oleh youtube.
                                </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
      </div>
    </div>

@endsection

@section('registerscript')

<script>

    $(function () {
        'use strict';

    });

    @if ( $video->siap == 'n' )
        validVideoId('{{ $video->video_id }}');
    @endif

    function validVideoId(id) {
        var img = new Image();
        img.src = "http://img.youtube.com/vi/" + id + "/mqdefault.jpg";
        img.onload = function () {
            checkThumbnail(this.width, id);
        }
    }

    function checkThumbnail(width, id_video) {
        if (width === 120) {
            
            // Video belum siap
            $('.alert-info').show();
            $('#loading').hide();
            $('#player').hide();
            console.log('belum siap');

        } else {

            $.ajax({
                url: '{{ route('m_video_update_ketersediaan') }}',
                data: { id_video: '{{ $video->id }}' },
                success: function(result){
                    $('#loading').hide();
                    $('#player').show();
                },
                error: function(data,status,msg){
                    alert('Gagal memeriksa ketersediaan video. Coba muat ulang halaman');
                    $('.alert-info').show();
                    $('#loading').hide();
                }
            });
        }
    }

</script>
@endsection