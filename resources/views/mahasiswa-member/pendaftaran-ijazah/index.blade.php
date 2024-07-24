@extends('layouts.app')

@section('title','Pendaftaran Pengambilan Ijazah')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Pendaftaran Pengambilan Ijazah
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">

                @if ( Sia::sessionMhs('jenis_keluar') == 1 )

                    <div class="row">
                        <br>
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <p><strong>Silahkan lengkapi dokumen yang diperlukan berikut</strong></p>
                            </div>
                        </div>

                        <!-- Skripsi/Tesis -->
                        <div class="col-md-6" style="margin-bottom: 20px">
                            <div class="well {{ !empty($mahasiswa) && !empty($mahasiswa->skripsi) ? 'bg-theme-inverse' :'' }}">
                                <h3><strong>File</strong> Skripsi/Tesis </h3>

                                @if ( empty($mahasiswa) )
                                    <p>Belum ada file diupload</p>
                                @else
                                    @if ( empty($mahasiswa->skripsi) )
                                        <p>Belum ada file diupload</p>
                                    @else
                                        <p>
                                            <div class="icon-resources">
                                                <?php $icon = Rmt::icon($mahasiswa->skripsi); ?>
                                                <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                            </div>

                                            <?php $param = ['file' => $mahasiswa->skripsi] ?>

                                            <a href="{{ route('daftar_ijazah_download', $param) }}" title="Download File" style="color: #fff">
                                                {{ $mahasiswa->skripsi }}
                                            </a>
                                            <p><i class="fa fa-clock-o"></i><i> {{ Rmt::Waktulalu($mahasiswa->updated_at) }}</i></p>
                                        </p>
                                    @endif
                                    
                                @endif

                                <div class="flip">

                                    @if ( !empty($mahasiswa) )
                                        <hr>
                                    @endif
                                    <button class="btn {{ !empty($mahasiswa) && !empty($mahasiswa->skripsi) ? 'btn-theme' : 'btn-theme-inverse' }}" onclick="uploadFile('File Skripsi/Tesis', 'S')">
                                        <i class="fa fa-plus"></i> 

                                        @if ( !empty($mahasiswa) )
                                            @if ( !empty($mahasiswa->skripsi) )
                                                UPLOAD PERUBAHAN
                                            @else
                                                UPLOAD FILE
                                            @endif
                                        @else
                                            UPLOAD FILE
                                        @endif
                                    </button>

                                </div>
                            </div>
                        </div>
                    

                        <!-- Turnitin -->
                        <div class="col-md-6" style="margin-bottom: 20px">
                            <div class="well {{ !empty($mahasiswa) && !empty($mahasiswa->turnitin) ? 'bg-theme-inverse':'' }}">
                                <h3><strong>File</strong> Turnitin </h3>

                                @if ( empty($mahasiswa) )
                                    <p>Belum ada file diupload</p>
                                @else
                                    @if ( empty($mahasiswa->turnitin) )
                                        <p>Belum ada file diupload</p>
                                    @else
                                        <p>
                                            <div class="icon-resources">
                                                <?php $icon = Rmt::icon($mahasiswa->turnitin); ?>
                                                <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                            </div>

                                            <?php $param = ['file' => $mahasiswa->turnitin] ?>

                                            <a href="{{ route('daftar_ijazah_download', $param) }}" title="Download File" style="color: #fff">
                                                {{ $mahasiswa->turnitin }}
                                            </a>
                                            <p><i class="fa fa-clock-o"></i><i> {{ Rmt::Waktulalu($mahasiswa->updated_at) }}</i></p>
                                        </p>
                                    @endif
                                    
                                @endif

                                <div class="flip">

                                    @if ( !empty($mahasiswa) )
                                        <hr>
                                    @endif
                                    <button class="btn {{ !empty($mahasiswa) && !empty($mahasiswa->turnitin) ? 'btn-theme' : 'btn-theme-inverse' }}" onclick="uploadFile('File Turnitin', 'T')">
                                        <i class="fa fa-plus"></i> 

                                        @if ( !empty($mahasiswa) )
                                            @if ( !empty($mahasiswa->turnitin) )
                                                UPLOAD PERUBAHAN
                                            @else
                                                UPLOAD FILE
                                            @endif
                                        @else
                                            UPLOAD FILE
                                        @endif
                                    </button>

                                </div>
                            </div>
                        </div>

                        @if ( !empty($mahasiswa) )
                            <div class="col-md-12">
                                <p><strong>Status Persetujuan Pengambilan Ijazah</strong></p>
                                <table border="0">
                                    <tr>
                                        <td width="150">Persetujuan Prodi</td>
                                        <td>: {{ Rmt::status3($mahasiswa->bebas_skripsi) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Bebas Pustaka</td>
                                        <td>: {{ Rmt::status3($mahasiswa->bebas_skripsi) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Bebas Pembayaran</td>
                                        <td>: {{ Rmt::status3($mahasiswa->bebas_skripsi) }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                    </div>

                @else

                    <br>
                    <div class="alert alert-info">
                        <h4>Anda belum lulus, silahkan ajukan pengambilan ijazah setelah anda dinyatakan lulus</h4>
                    </div>

                @endif

            </div>

        </div>

    </div>
  </div>
</div>


<div id="modal-daftar" class="modal fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Upload <span id="jenis"></span></h4>
    </div>
    <div class="modal-body">

        <div class="col-md-12">

            <form action="{{ route('daftar_ijazah_store') }}" id="form-daftar" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}

                <input type="hidden" name="jenis" id="jenis-file">

                <div class="form-group">
                    <input type="file" name="file" class="form-control">
                </div>

                <hr>
                <button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> BATALKAN</button>
                <button type="submit" id="btn-submit-daftar" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
                <br>
                <br>
            </form>

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
            showSuccess('{{ Session::get('success') }}', 3000);
        @endif
    })

    function filterJenis(value)
    {
        window.location.href='?jenis='+value;
    }

    function showMessage(modul,pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit-'+modul).removeAttr('disabled');
        $('#btn-submit-'+modul).html('<i class="fa fa-floppy-o"></i> SIMPAN');
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
                showMessage(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('daftar');


    function uploadFile(jenis, jns_code)
    {
        $('#jenis').html(jenis);
        $('#jenis-file').val(jns_code);
        $('#modal-daftar').modal('show');
    }

</script>
@endsection