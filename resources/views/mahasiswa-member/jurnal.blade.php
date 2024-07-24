@extends('layouts.app')

@section('title','Jurnal Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Upload Jurnal
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-9">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="130px">NIM</th>
                                        <td>: {{ $mhs->nim }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th><td>: {{ $mhs->nm_mhs }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                       <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th>Angkatan</th><td>: {{ substr($mhs->nim,0,4) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class="table-responsive">

                            <br>
                            <form action="{{ route('mhs_jurnal_store') }}" id="form-jurnal" enctype="multipart/form-data" method="post">
                                {{ csrf_field() }}

                                <table border="0" style="min-width: 700px">
                                    <tr>
                                        <td width="130px" align="left"><b>{{ empty($mhs->jurnal_file) ? 'Masukkan File':'Ganti File' }} (pdf) : </b></td>
                                        <td width="300px">
                                            <div style="position: relative;">
                                                <div class="input-icon right"> 
                                                    <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                    <input type="file" class="form-control input-sm" name="file" required="">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            &nbsp; &nbsp; 
                                            <button id="btn-submit" class="btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Upload Jurnal </button>
                                            &nbsp; &nbsp;
                                            <a href="https://drive.google.com/file/d/1uta3ly0M6r-3mmc_3T_xZJdAtpWPeRgK/view?usp=sharing" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-book"> Donwload Template Jurnal</i></a>
                                        </td>
                                    </tr>
                                </table>
                            </form>

                            <hr>

                            {{ Rmt::AlertSuccess() }}

                            @if ( empty($mhs->jurnal_file) )
                                <div class="alert alert-danger">
                                    Anda belum setor jurnal
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <b>File Jurnal : </b> <a href="{{ route('mhs_jurnal_download') }}" target="_blank">
                                        <img width="24" src="{{ url('resources') }}/assets/img/icon/doc.png" />{{ $mhs->jurnal_file }}</a>

                                    <a href="{{ route('mhs_jurnal_file_delete', ['id_mhs_reg' => $mhs->id_reg_pd]) }}" class="btn btn-xs btn-danger pull-right" onclick="return confirm('Anda ingin menghapus file jurnal ini?')"><i class="fa fa-times"></i> Hapus</a>
                                </div>
                            @endif
                            
                            <p><b>Status Persetujuan Jurnal : </b>
                                @if ( $mhs->jurnal_approved != '1' )
                                 <i class="fa fa-ban" style="color: red"></i> Belum Disetujui
                                    @if ( !empty($mhs->pesan_revisi) )
                                        <div class="alert alert-danger">
                                            PESAN REVISI:
                                            <p><?= nl2br($mhs->pesan_revisi) ?></p>
                                        </div>
                                    @endif
                                @else
                                    <i class="fa fa-check-square" style="color: green"></i> Telah Disetujui
                                @endif
                            </p>

                            <p><b>Status Publikasi Jurnal : </b>
                                @if ( $mhs->jurnal_published != '1' )
                                 <i class="fa fa-ban" style="color: red"></i> Belum dipublikasikan
                                @else
                                    <i class="fa fa-check-square" style="color: green"></i> Telah dipublikasikan
                                @endif
                            </p>

                        </div>

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
    $(function () {

        var options = {
            beforeSend: function() 
            {
                $('#caplet-overlay').show();
                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i>");
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
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage(pesan);
            }
        }; 

        $('#form-jurnal').ajaxForm(options);
    });

    function showMessage(pesan)
    {
        $('#caplet-overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> Upload Jurnal');
    }

</script>
@endsection