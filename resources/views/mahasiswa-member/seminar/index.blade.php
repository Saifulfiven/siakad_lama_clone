@extends('layouts.app')

@section('title','Pendaftaran Seminar')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Pendaftaran Seminar
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <select class="form-control" onchange="filterJenis(this.value)">
                            @foreach( Sia::jenisSeminar() as $key => $val )
                                <option value="{{ $key }}" {{ $jenis == $key ? 'selected':'' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-12">
                        <hr>
                        
                        @if ( $telah_seminar )

                            <div class="alert alert-success">
                                <br>
                                &nbsp;  <strong><i class="fa fa-check-circle"></i> Kamu telah menyelesaikan seminar/ujian ini</strong>
                                <br>
                                <br>
                            </div>

                        @else
                            {{-- {{ dd($has_skripsi) }} --}}
                            @if ( $has_skripsi == true )

                                <div class="alert alert-danger">
                                    Kamu belum/tidak bisa mendaftar seminar/ujian ini
                                </div>

                            @else

                                <!-- Jika memprogram skripsi/tesis semester ini -->

                                @if ( empty($seminar) )

                                    <div class="alert alert-danger">
                                        <h4>Kamu belum bisa mendaftar {{ Sia::jenisSeminar($jenis) }}</h4><br>
                                        Silahkan selesaikan bimbingan anda
                                    </div>

                                @else
                                    
                                    <!-- Jika telah mendaftar -->
                                    @include('mahasiswa-member.seminar.data-pendaftaran')

                                @endif

                            @endif

                        @endif

                    </div>
                </div>

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

    $(function(){
        @if ( Session::has('success') )
            showSuccess('{{ Session::get('success') }}', 5000);
        @endif
    });

    function jenisFile(jenis)
    {
        if ( jenis == 'pembayaran' ) {
            $('#keterangan').val('');
            $('#keterangan').hide();
            $('#jns-file').html(jenis);
        } else {
            $('#jns-file').html('Olah data atau Validasi');
            $('#keterangan').show();
        }

        $('#jenis-file').val(jenis);
        $('#modal-upload').modal('show');
    }

    function filterJenis(value)
    {
        window.location.href='?jenis='+value;
    }

    function showMessage(modul,pesan)
    {
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {

                window.location.reload();

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
                showMessage2(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('upload-file');
    submit('ajuan');

    function getEnd(value)
    {
        var next = 0;
        var next = parseInt(value) + 2;
        $('#pukul-2').val(next);

    }

</script>
@endsection