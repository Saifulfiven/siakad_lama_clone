@extends('layouts.app')

@section('title','Detail Persetujuan Seminar')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          <a href="{{ route('dsn_approv_seminar') }}" class="btn btn-circle btn-default"><i class="fa fa-arrow-left"></i> </a> &nbsp;  Detail Persetujuan Seminar
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                <div class="row">

                    <div class="col-md-6">

                        <table class="table" border="0">
                            <tr>
                                <td width="150">Nama Mahasiswa</td>
                                <td>: {{ $sem->nm_mhs }}</td>
                            </tr>
                            <tr>
                                <td>NIM</td>
                                <td>: {{ $sem->nim }}</td>
                            </tr>
                            <tr>
                                <td>Jenis Seminar</td>
                                <td>: {{ Sia::jenisSeminar($sem->jenis) }}</td>
                            </tr>
                        </table>
                        <br>

                        <?php $persetujuan_saya = 0 ?>
                        <?php $id_penguji = '' ?>

                        <p><b>Persetujuan Dosen Pembimbing & Penguji</b></p>
                        <table class="table table-striped table-hover">
                            @foreach( $penguji as $pg )

                                @if ( $pg->id_dosen == Sia::sessionDsn() )
                                    <?php $persetujuan_saya = $pg->setuju ?>
                                    <?php $id_penguji = $pg->id ?>
                                @endif

                                <tr>
                                    <td width="">{{ $pg->nm_dosen }}</td>
                                    <td>: {{ Rmt::status2($pg->setuju) }}</td>
                                </tr>
                            @endforeach
                        </table>
                        
                    </div>

                    <div class="col-md-6">
                        <p><b>Jadwal Seminar yang diusulkan Mahasiswa</b></p>

                        <table class="table">
                            <tr>
                                <td width="150">Tanggal</td>
                                <td>: {{ empty($ujian->tgl_ujian) ? 'Belum ada' : Rmt::tgl_indo($ujian->tgl_ujian) }}</td>
                            </tr>
                            <tr>
                                <td>Jam/Pukul</td>
                                <td>: {{ empty($ujian->pukul) ? 'Belum ada' : $ujian->pukul.' WITA' }} </td>
                            </tr>
                        </table>

                        <hr>

                        @if ( empty($ujian->pukul) )
                            <div class="alert alert-info">
                                <p><i class="fa fa-info-circle"></i> Silahkan tunggu hingga mahasiswa mengajukan jadwal</p>
                            </div>
                        @else

                            @if ( $persetujuan_saya == 0 )

                                <button onclick="updateStatus('1', '{{ $sem->nm_mhs }} - {{ $sem->nim }}', '{{ Sia::jenisSeminar($sem->jenis) }}')"
                                    class="btn btn-success pull-left" style="margin-right: 20px"><i class="fa fa-check-square"></i> Setujui</button>

                                <button onclick="updateStatus('2', '{{ $sem->nm_mhs }} - {{ $sem->nim }}', '{{ Sia::jenisSeminar($sem->jenis) }}')"
                                    class="btn btn-danger pull-left"><i class="fa fa-ban"></i> Tolak</button>

                            @elseif ( $persetujuan_saya == 1 )

                                <button onclick="updateStatus('2', '{{ $sem->nm_mhs }} - {{ $sem->nim }}', '{{ Sia::jenisSeminar($sem->jenis) }}')"
                                    class="btn btn-danger pull-left"><i class="fa fa-ban"></i> Tolak</button>

                            @else

                                <button onclick="updateStatus('1', '{{ $sem->nm_mhs }} - {{ $sem->nim }}', '{{ Sia::jenisSeminar($sem->jenis) }}')"
                                    class="btn btn-success pull-left" style="margin-right: 20px"><i class="fa fa-check-square"></i> Setujui</button>

                            @endif

                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
  </div>
</div>

<form action="{{ route('dsn_approv_seminar_update') }}" method="post" id="form-update">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $id_penguji }}">
    <input type="hidden" name="disetujui" id="disetujui">
</form>

<div id="modal-update" class="modal fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Persetujuan Seminar</h4>
    </div>
    <div class="modal-body">
        <center>
            <strong><span id="mhs"></span></strong><br><br>
            Anda yakin akan <span id="setuju"></span> <strong><span id="nm-seminar"></span></strong> 
            <br>
            dari mahasiswa tersebut
        </center>

    </div>

    <div class="modal-footer">
        
        <button type="button" data-dismiss="modal" class="btn btn-default pull-left"><i class="fa fa-times"></i> Tutup</button>

        <button type="submit" class="btn pull-right" id="btn-submit-update"></button>

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

        $('#btn-submit-update').click(function(){
            $('#form-update').submit();
        })
    })

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
    submit('update');

    function updateStatus(value, mhs, nm_seminar)
    {
        $('#mhs').html(mhs);
        $('#nm-seminar').html(nm_seminar);
        $('#disetujui').val(value);

        if ( value === '1' ) {
            $('#setuju').html('menyetujui');
            $('#btn-submit-update').removeClass('btn-danger');
            $('#btn-submit-update').addClass('btn-success');
            $('#btn-submit-update').html('<i class="fa fa-check-square"></i> Setujui');
        } else {
            $('#setuju').html('membatalkan persetujuan');
            $('#btn-submit-update').addClass('btn-danger');
            $('#btn-submit-update').removeClass('btn-success');
            $('#btn-submit-update').html('<i class="fa fa-ban"></i> Tolak');
        }
        $('#modal-update').modal('show');
    }

</script>
@endsection