@extends('layouts.app')

@section('title','Jurnal Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Jurnal Mahasiswa
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">
            
            @include('mahasiswa.link-cepat')

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
                            <form action="{{ route('mahasiswa_jurnal_store') }}" id="form-jurnal" enctype="multipart/form-data" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id_mhs_reg" value="{{ $mhs->id_reg_pd }}">
                                <input type="hidden" name="nim" value="{{ $mhs->nim }}">
                                <input type="hidden" name="nm_mhs" value="{{ $mhs->nm_mhs }}">
                                <input type="hidden" name="id_mhs" value="{{ $mhs->id }}">

                                <table border="0" width="100%">
                                    <tr>
                                        <td width="190px" align="left"><b>Masukkan File Word (docx) : </b></td>
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
                                        </td>
                                    </tr>
                                </table>
                            </form>

                            <hr>

                            {{ Rmt::AlertSuccess() }}

                            @if ( empty($mhs->jurnal_file) )
                                <div class="alert alert-danger">
                                    Mahasiswa ini belum setor jurnal
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <b>File Jurnal :</b> <a href="{{ route('mahasiswa_jurnal_download', ['file' => 'jurnal-'.$mhs->nim]) }}?file={{ $mhs->jurnal_file }}" target="_blank">{{ $mhs->jurnal_file }} <?= !empty($mhs->updated_jurnal) ? '- Diupdate pada: <b>'. Carbon::parse($mhs->updated_jurnal)->format('d/m/Y H:i').'</b>' : '' ?></b></a>

                                    <div class="pull-right">
                                        <a href="javascript:;" data-target="#modal-revisi" data-toggle="modal" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Perlu direvisi</a>
                                        &nbsp; 
                                        <a href="{{ route('mahasiswa_jurnal_file_delete', ['id_mhs_reg' => $mhs->id_reg_pd]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Anda ingin menghapus file jurnal ini?')"><i class="fa fa-times"></i> Hapus</a> &nbsp
                                    </div>
                                </div>
                            @endif

                            @if ( Sia::role('jurnal') )

                                <p><b>Persetujuan Jurnal : </b>
                                    @if ( $mhs->jurnal_approved != '1' )
                                        <a href="javascript:;" id="approve" title="Tandai telah disetujui" onclick="approve('<?= $mhs->id_reg_pd ?>','1')"><i class="fa fa-ban" style="color: red"></i> Belum Disetujui</i></a>
                                    @else
                                        <a href="javascript:;" id="approve" title="Tandai belum distujui" onclick="approve('<?= $mhs->id_reg_pd ?>','0')"><i class="fa fa-check-square" style="color: green"></i> Telah Disetujui</a>
                                    @endif
                                </p>

                                <p><b>Status Penerbitan Jurnal : </b>
                                    @if ( $mhs->jurnal_published != '1' )
                                        <a href="javascript:;" id="publish" title="Tandai telah dipublikasikan" onclick="publish('<?= $mhs->id_reg_pd ?>','1')"><i class="fa fa-ban" style="color: red"></i> Belum dipublikasikan</i></a>
                                    @else
                                        <a href="javascript:;" id="publish" title="Tandai belum dipublikasikan" onclick="publish('<?= $mhs->id_reg_pd ?>','0')"><i class="fa fa-check-square" style="color: green"></i> Telah dipublikasikan</a>
                                    @endif
                                </p>

                            @elseif ( Sia::role('akademik|admin|cs') )
                                <p><b>Persetujuan Jurnal : </b>
                                    @if ( $mhs->jurnal_approved != '1' )
                                        <i class="fa fa-ban" style="color: red"></i> Belum Disetujui</i>
                                    @else
                                        <i class="fa fa-check-square" style="color: green"></i> Telah Disetujui
                                    @endif
                                </p>

                                <p><b>Status Penerbitan Jurnal : </b>
                                    @if ( $mhs->jurnal_published != '1' )
                                        <i class="fa fa-ban" style="color: red"></i> Belum dipublikasikan</i>
                                    @else
                                        <i class="fa fa-check-square" style="color: green"></i> Telah dipublikasikan
                                    @endif
                                </p>

                            @endif
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>

<div id="modal-revisi" class="modal fade" tabindex="-1" style="top:30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Pesan Revisi</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <form action="{{ route('mahasiswa_jurnal_revisi') }}" id="form-revisi">
            <input type="hidden" name="id_mhs_reg" value="{{ $mhs->id_reg_pd }}">
            <p>Pesan:</p>
            <textarea name="pesan" class="form-control" required="">{{ $mhs->pesan_revisi }}</textarea>
            <hr>
            <center>
                <button id="submit-revisi" class="btn btn-sm btn-primary">Kirim</button>
            </center>
        </form>
    </div>
    <!-- //modal-body-->
</div>

<div id="modal-loading" class="modal fade" tabindex="-1" style="top:30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Mengirim email</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <center>
            <h4>Sedang mengirim email ke mahasiswa</h4>
            <h1><i class="fa fa-spinner fa-spin"></i></h1>
        </center>
    </div>
    <!-- //modal-body-->
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

        $('#nav-mini').trigger('click');

        // Form jurnal
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


        // Form revisi
        var options = {
            beforeSend: function() 
            {
                $('#caplet-overlay').show();
                $("#submit-revisi").attr('disabled','');
                $("#submit-revisi").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengirim...");
            },
            success:function(data, status, message) {
                $('#modal-revisi').modal('hide');
                sendMailRevisi('{{ $mhs->id_reg_pd }}');
                $("#submit-revisi").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengirim...");
                $('#submit-revisi').removeAttr('disabled');
                $('#submit-revisi').html('Kirim');
                $('#caplet-overlay').hide();
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

        $('#form-revisi').ajaxForm(options);
    });

    function sendMailRevisi(id_mhs_reg)
    {
        $('#modal-loading').modal('show');

        $.ajax({
            url: '{{ route('mahasiswa_jurnal_mail_revisi') }}',
            data: { id_mhs_reg: id_mhs_reg, nm_mhs: '{{ $mhs->nm_mhs }}' },
            success: function(result){
                $('#modal-loading').modal('hide');
                showSuccess('Berhasil menyimpan & mengirim pemberitahuan revisi');
            },
            error: function(data,status,message){
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
        })
    }

    function showMessage(pesan)
    {
        $('#caplet-overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> Upload Jurnal');

        $('#submit-revisi').removeAttr('disabled');
        $('#submit-revisi').html('Kirim');
        $('#modal-loading').modal('hide');
    }


    function publish(id_mhs_reg, value)
    {
        var div = $('#publish');

        div.html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ route('mahasiswa_jurnal_publish') }}',
            data: { publish: value, id_mhs_reg: id_mhs_reg },
            success: function(result){
                alert('Aksi Berhasil');
                window.location.reload();

            },
            error: function(data,status,msg){
                alert(data.responseText);
            }
        })
    }

    function approve(id_mhs_reg, value)
    {
        var div = $('#approve');

        div.html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ route('mahasiswa_jurnal_publish') }}',
            data: { approve: value, id_mhs_reg: id_mhs_reg, jenis: 'approval' },
            success: function(result){
                window.location.reload();

            },
            error: function(data,status,msg){
                alert(data.responseText);
            }
        })
    }
</script>
@endsection