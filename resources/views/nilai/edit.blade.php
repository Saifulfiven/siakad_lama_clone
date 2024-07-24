@extends('layouts.app')

@section('title','Input Nilai Perkuliahan')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>NILAI PERKULIAHAN</a></li>
    </ul>
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="javascript:;" id="submit-nilai" class="btn btn-primary btn-sm pull-right" style="margin: 3px 3px"><i class="fa fa-save"></i> SIMPAN</a>
                        <a href="{{ route('nil_cetak', ['id' => $r->id, 'dosen' => $r->dosen]) }}" class="btn btn-default btn-sm pull-right" style="margin: 3px 3px" target="_blank"><i class="fa fa-print"></i> CETAK</a>
                        <a href="javascript:;" onclick="window.history.back()" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> KEMBALI</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <div class="ajax-message"></div>
                        {{ Rmt::AlertSuccess() }}
                        {{ Rmt::AlertError() }}

                        <div class="table-responsive">
                            <table border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th>Matakuliah</th>
                                        <td>: {{ $r->kode_mk }} - {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
                                        <th width="160px">Kelas / Ruangan</th>
                                        <td>: {{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>
                                    </tr>
                                    <tr>
                                        <th width="160px">Semester</th>
                                        <td width="400px">: {{ $r->nm_smt }}</td>
                                        <th>Program Studi</th>
                                        <td>: {{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-12">
                        @if ( $r->id_prodi == '61101' )
                            @include('nilai.edit-s2')
                        @else
                            <form action="{{ route('nil_update') }}" method="post" id="form-nilai">

                                <input type="hidden" name="matakuliah" value="{{ $r->nm_mk }}">
                                <input type="hidden" name="id_smt" value="{{ $r->id_smt }}">
                                <input type="hidden" name="dosen" value="{{ $r->dosen }}">
                                <input type="hidden" name="id_prodi" value="{{ $r->id_prodi }}">

                                {{ csrf_field() }}
                                <div class="table-responsive">

                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                        <thead class="custom">
                                            <tr>
                                                <th width="20px">No.</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                @if ( !empty($r->nm_ruangan) )
                                                    <th width="110">Nil. Kehadiran</th>
                                                    <th width="110">Nil. Tugas</th>
                                                    <th width="110">Nil. MID</th>
                                                    <th width="110">Nil. Final</th>
                                                    <th>Preview</th>
                                                @endif
                                                <th width="100">Nilai Huruf</th>
                                            </tr>
                                        </thead>
                                        <tbody align="center">
                                            @foreach( $peserta as $ps )
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td align="left">{{ $ps->nim }}</td>
                                                <td align="left">{{ $ps->nm_mhs }}</td>
                                                @if ( !empty($r->nm_ruangan) )
                                                    <td><input type="text" value="{{ $ps->nil_kehadiran }}" onchange="hitung2('{{ $ps->nim }}','tugas-uts-uas', this.value)" id="hadir-{{ $ps->nim }}" maxlength="5" name="kehadiran[]" class="form-control number"></td>
                                                    <td><input type="text" value="{{ $ps->nil_tugas }}" onchange="hitung2('{{ $ps->nim }}','hadir-uts-uas', this.value)" id="tugas-{{ $ps->nim }}" maxlength="5" name="tugas[]" class="form-control number"></td>
                                                    <td><input type="text" value="{{ $ps->nil_mid }}" onchange="hitung2('{{ $ps->nim }}','hadir-tugas-uas', this.value)" id="uts-{{ $ps->nim }}" maxlength="5" name="uts[]" class="form-control number"></td>
                                                    <td><input type="text" value="{{ $ps->nil_final }}" onchange="hitung2('{{ $ps->nim }}','hadir-tugas-uts', this.value)" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number"></td>
                                                    <td id="pre-{{ $ps->nim }}" style="background: #eee">
                                                        {{ $ps->nilai_angka > 0 ? $ps->nilai_angka : '' }}
                                                        {{ !empty($ps->nilai_huruf) ? ' = '.$ps->nilai_huruf : '-' }}
                                                    </td>
                                                @else
                                                    <input type="hidden" name="kehadiran[]" value="{{ $ps->nil_kehadiran }}">
                                                    <input type="hidden" name="tugas[]" value="{{ $ps->nil_tugas }}">
                                                    <input type="hidden" name="uts[]" value="{{ $ps->nil_mid }}">
                                                    <input type="hidden" name="uas[]" value="{{ $ps->nil_final }}">
                                                @endif

                                                <td>
                                                    <select id="select-huruf-{{ $ps->nim }}" onchange="updatePriority(2, '{{ $ps->nim }}')" name="nilai[]" class="form-control" style="width: 70px">
                                                        <option value="-">--</option>
                                                        @foreach( Sia::skalaNilai($r->id_prodi) as $sn )
                                                            <option value="{{ $sn->nilai_indeks }}-{{ $sn->nilai_huruf }}" {{ $sn->nilai_indeks.'-'.$sn->nilai_huruf == $ps->nilai_indeks.'-'.$ps->nilai_huruf ? 'selected':'' }}>{{ $sn->nilai_huruf }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="id_nilai[]" value="{{ $ps->id_nilai }}">
                                                    <input type="hidden" name="nil_lama[]" value="{{ $ps->nilai_huruf }}">
                                                    <input type="hidden" name="huruf_baru[]" id="huruf-{{ $ps->nim }}" value="{{ $ps->nilai_huruf }}">
                                                    <input type="hidden" name="nil_angka[]" id="angka-{{ $ps->nim }}" value="{{ $ps->nilai_angka }}">
                                                    <input type="hidden" name="nil_huruf_priority[]" id="priority-{{ $ps->nim }}" value="2">
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>
    $(function () {
        'use strict';

        $('#submit-nilai').click(function(){
            $('#form-nilai').submit();
        });

        $('.number').keypress(function(event) {
          if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
          }
        });
            var options = {
                beforeSend: function() 
                {
                    $('#overlay').show();
                    $("#submit-nilai").attr('disabled','');
                    $("#submit-nilai").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
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

            $('#form-nilai').ajaxForm(options);

    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#submit-nilai').removeAttr('disabled');
        $('#submit-nilai').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function hitung(nim, jenis, value = null)
    {
        var uts,uas,jns;

        var jml = $('#'+jenis+'-'+nim).val();
        if ( jml === '' ) {
            return;
        }

        uts = $('#uts-'+nim).val();
        uas = $('#uas-'+nim).val();

        $('#pre-'+nim).html('<i class="fa fa-spinner fa-spin"></i>');

        $.get('{{ route('nil_hitung_s2') }}', {prodi: '{{ $r->id_prodi }}', uts: uts, uas: uas, nilai:value }, function(data){
            $('#pre-'+nim).html(data.html);
            $('#huruf-'+nim).val(data.grade);
            $('#angka-'+nim).val(data.nilai);
        });

    }

    function hitung2(nim, jenis, value)
    {
        var hadir,tugas,uts,uas,jns,i;

        jns = jenis.split('-');

        if ( value === '' ) {
            return;
        }

        for( i = 0; i < jns.length; i++){
            var jml = $('#'+jns[i]+'-'+nim).val();
            if ( jml == 0 || jml === '' ) {
                return;
            }
        }

        hadir = $('#hadir-'+nim).val();
        tugas = $('#tugas-'+nim).val();
        uts = $('#uts-'+nim).val();
        uas = $('#uas-'+nim).val();

        $('#pre-'+nim).html('<i class="fa fa-spinner fa-spin"></i>');

        $.get('{{ route('dsn_nilai_akhir') }}', {prodi: '{{ $r->id_prodi }}', hadir: hadir, tugas: tugas, uts: uts, uas: uas }, function(data){
            $('#pre-'+nim).html(data.html);
            $('#huruf-'+nim).val(data.grade);
            $('#angka-'+nim).val(data.nilai);
            updatePriority(1, nim);

            $('#select-huruf-'+nim+' option').filter(function() {
                return this.text == data.grade; 
            }).attr('selected', true);
        }).error(function(data){
            console.log(data);
        });
    }

    function updatePriority(value,nim){
        $('#priority-'+nim).val(value);
    }
</script>
@endsection