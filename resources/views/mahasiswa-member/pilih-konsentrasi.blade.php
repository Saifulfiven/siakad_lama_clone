@extends('layouts.app')

@section('title','Pemilihan konsentrasi')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Pemilihan konsentrasi
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-9">

                <div class="row" style="margin-bottom: 13px">
                </div>
                
                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-">
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
                       <div class="table-">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="130px">Angkatan</th><td>: {{ substr($mhs->nim,0,4) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">

                        @if ( !empty($konsentrasi) )
                            <hr>

                            <div class="alert alert-info">
                                <p><b>Anda telah selesai memilih konsentrasi</b></p>
                            </div>
                            <p><b>Berikut ini data yang kami terima berdasarkan input dari akun anda: </b></p>
                            <div class="table-responsive">
                                <table border="0" class="table" style="min-width: 500px">
                                    <tr>
                                        <td width="50"><b>Kelas</b></td>
                                        <td>: <b>{{ $konsentrasi->kelas }}</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Konsentrasi</b></td>
                                        <td>: <b>{{ $konsentrasi->konsen->nm_konsentrasi }}</b></td>
                                    </tr>
                                </table>
                            </div>


                        @else

                            <br>
                            <form action="{{ route('mhs_konsentrasi_store') }}" id="form-konsentrasi" enctype="multipart/form-data" method="post">
                                {{ csrf_field() }}

                                <p><b>PILIH KONSENTRASI</b></p>
                                <div class="form-group">
                                    <label class="control-label">Kelas anda sekarang</label>
                                    <div>
                                        <?php
                                            $kelas = Sia::listKelasKonsentrasi();
                                        ?>
                                        <select name="kelas" class="form-control" style="max-width: 200px">
                                            <option value="">Pilih kelas</option>
                                            @foreach( $kelas as $key => $val )
                                                @foreach( range('A', $val) as $bag )
                                                    <option value="{{ $key }}-{{ $bag }}">{{ $key }}-{{ $bag }}</option>
                                                @endforeach

                                            @endforeach
                                            <option value="XII-G1">XII-G1</option>
                                            <option value="XII-H1">XII-H1</option>
                                            <option value="XII-H2">XII-H2</option>
                                            <option value="XIII-A">XIII-A</option>
                                            <option value="XIII-B">XIII-B</option>
                                            <option value="XIII-C">XIII-C</option>
                                            <option value="XIII-D">XIII-D</option>
                                            <option value="XIII-E">XIII-E</option>
                                            @foreach( Sia::listKelasKonsentrasi2() as $key => $val )
                                                @foreach( range('A', $val) as $bag )
                                                    <option value="{{ $key }}-{{ $bag }}">{{ $key }}-{{ $bag }}</option>
                                                @endforeach

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Pilih Konsentrasi</label>
                                    <div>
                                        <select name="konsentrasi" class="form-control" style="max-width: 350px">
                                            <option value="">Pilih konsentrasi</option>
                                            @foreach( Sia::listKonsentrasi($mhs->id_prodi) as $kon )
                                                <option value="{{ $kon->id_konsentrasi }}">{{ $kon->nm_konsentrasi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="button" onclick="konfirmasi()" class="btn btn-primary" id="btn-submit"><i class="fa fa-save"></i> SIMPAN</button>
                                </div>

                            </form>
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

        $('#form-konsentrasi').ajaxForm(options);
    });

    function showMessage(pesan)
    {
        $('#caplet-overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-save"></i> SIMPAN');
    }

    function konfirmasi()
    {
        if ( confirm('Anda tidak bisa lagi mengubah konsentrasi setelah ini. Lanjutkan Simpan?') ) {
            $('#form-konsentrasi').submit();
        }
    }
</script>
@endsection