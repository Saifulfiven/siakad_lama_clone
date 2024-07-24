@extends('layouts.app')

@section('title','Matakuliah Kurikulum')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Matakuliah Kurikulum untuk {{ $kur->nm_kurikulum }}
            </header>
              
            <div class="panel-body" style="padding-top: 3px">
                <form action="{{ route('kurikulum_mk_store_arr') }}" id="form-matakuliah" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_kurikulum" value="{{ $kur->id }}">
                    <div class="ajax-message"></div>
                    {{ Rmt::AlertSuccess() }}
                    <div class="row" style="margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('kurikulum') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-info btn-sm pull-right"><i class="fa fa-refresh"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="0" class="table table-hover">
                                    <tbody class="detail-mhs">
                                        <tr>
                                            <th width="160px">Nama Kurikulum</th>
                                            <td colspan="5">: {{ $kur->nm_kurikulum }}</td>
                                        </tr>
                                        <tr>
                                            <th>Program Studi</th>
                                            <td>: {{ $kur->jenjang }} {{ $kur->nm_prodi }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>: <?= Sia::statusKurikulum($kur->aktif) ?></td>
                                            <th width="80px">SKS Wajib</th>
                                            <td>: {{ $kur->jml_sks_wajib }}</td>
                                            <th width="80px">SKS Pilihan</th>
                                            <td>: {{ $kur->jml_sks_pilihan }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="table-responsive">
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                            <thead class="custom">
                                <tr>
                                    <th width="40px">Pilih</th>
                                    <th width="40px">No</th>
                                    <th width="120px">Kode Matakuliah</th>
                                    <th>Nama Matakuliah</th>
                                    <th width="120px">Prodi</th>
                                    <th width="80px">SKS</th>
                                    <th width="80px">Semester</th>
                                    <th width="80px">Jenis</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                @foreach( $matakuliah as $r )
                                    <tr>
                                        <td><input type="checkbox" name="matakuliah[{{ $r->id }}]" value="{{ $r->id }}" {{ empty($r->mk_smt) ? '':'checked' }}></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td align="left">{{ $r->kode_mk }}</td>
                                        <td align="left">{{ $r->nm_mk }}</td>
                                        <td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                        <td>{{ $r->sks_mk }}</td>
                                        <td><select name="smt[{{ $r->id }}]" class="form-custom mw-1">
                                                @for( $i = 1; $i <= 8; $i++ )
                                                    <option value="{{ $i }}" {{ $r->mk_smt == $i ? 'selected':'' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                        <td>{{ Sia::jenisMatakuliah($r->jenis_mk) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <center>
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
                    window.location.href='{{ route('kurikulum_detail',['id' => $kur->id]) }}';
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

    submit('matakuliah');

</script>
@endsection