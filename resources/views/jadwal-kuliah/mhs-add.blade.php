@extends('layouts.app')

@section('title','Tambah kolektif peserta kelas')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah kolektif peserta kelas
            </header>
              
            <div class="panel-body" style="padding-top: 3px">
                <form action="{{ route('jdk_mhs_store_arr') }}" id="form-mahasiswa" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jdk" value="{{ $r->id }}">
                    <input type="hidden" name="sks" value="{{ $r->sks_mk }}">
                    <input type="hidden" name="semester_mk" value="{{ $r->smt }}">
                    <input type="hidden" name="hari" value="{{ $r->hari }}">
                    <input type="hidden" name="jam" value="{{ $r->id_jam }}">
                    <div class="ajax-message"></div>
                    {{ Rmt::AlertSuccess() }}
                    <div class="row" style="margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('jdk') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                            <a href="{{ route('jdk_detail', ['id'=> $r->id]) }}" style="margin: 3px 3px" class="btn btn-info btn-sm pull-right"><i class="fa fa-refresh"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="0" class="table table-striped">
                                    <tbody class="detail-mhs">

                                        <tr>
                                            <th width="160px">Semester</th>
                                            <td width="400px">: {{ $r->nm_smt }}</td>
                                            <th width="160px">Nama Kelas</th>
                                            <td>: {{ $r->kode_kls }}</td>
                                        </tr>
                                        <tr>
                                            <th>Hari/Jam</th>
                                            <td>: {{ Rmt::hari($r->hari) }} - {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}</td>
                                            <th>Ruangan</th>
                                            <td>: {{ $r->nm_ruangan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Program Studi</th>
                                            <td>: {{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                            <th>Kapasitas Kelas</th>
                                            <td>: {{ $r->kapasitas_kls }}</td>
                                        </tr>
                                        <tr>
                                            <th>Matakuliah</th>
                                            <td>: {{ $r->kode_mk }} - {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="0" width="100%" style="margin-bottom: 10px">
                                    <tr>
                                        <td width="70px">
                                            Angkatan
                                        </td>
                                        <td width="80px">
                                            <select class="form-control input-sm" id="angkatan">
                                                <option value="">Semua</option>
                                                @foreach( Sia::listAngkatan() as $a )
                                                    <option value="{{ $a }}" {{ Request::get('ang') == $a ? 'selected':'' }}>{{ $a }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                </table>

                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                        <tr>
                                            <th width="40px">Pilih</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Prodi</th>
                                            <th>Angkatan</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">
                                        @foreach( $mahasiswa as $mhs )
                                            <tr>
                                                <td>
                                                    @if ( $mhs->available > 0 )
                                                        <i class="fa fa-check" style="color: green"></i>
                                                    @else
                                                        <input type="checkbox" name="mahasiswa[]" value="{{ $mhs->id }}">
                                                        <input type="hidden" name="nama_mhs[]" value="{{ $mhs->nm_mhs }}">
                                                    @endif
                                                </td>
                                                <td align="left">{{ $mhs->nim }}</td>
                                                <td align="left">{{ $mhs->nm_mhs }}</td>
                                                <td>{{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                                <td>{{ substr($mhs->semester_mulai, 0, 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
        $('#angkatan').change(function(){
            var angkatan = $(this).val();
            window.location.href='{{ route('jdk_mhs_add') }}?jdk={{ Request::get('jdk') }}&pr={{ Request::get('pr') }}&ang='+angkatan;
        });
    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(data.msg);
                } else {
                    window.location.href='{{ route('jdk_detail',['id' => $r->id]) }}';
                }
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
                showMessage(pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }

    submit('mahasiswa');

</script>
@endsection