@extends('mobile.layouts.app')

@section('title','Input Nilai Perkuliahan')

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        {{ Rmt::AlertSuccess() }}
                        {{ Rmt::AlertError() }}

                        <?php 

                            $dosen_ajar = DB::table('dosen_mengajar')
                                    ->where('id_jdk', $r->id)
                                    ->where('id_dosen', Request::get('id_dosen'))
                                    ->first();

                            $jml_dosen = DB::table('dosen_mengajar')
                                        ->where('id_jdk', $r->id)
                                        ->count();

                            $dosen_ke = $dosen_ajar->dosen_ke;

                            if ( $jml_dosen == 1 ) {
                                $aksi = route('m_nilai_updates2_single');
                            } else {
                                $aksi = route('m_nilai_updates2');
                            }

                        ?>

                        <div class="table-responsive">
                            <table border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th>Matakuliah</th>
                                        <td>: {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
                                    </tr>
                                    <tr>
                                        <th width="160px">Kelas / Ruangan</th>
                                        <td>: {{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>
                                    </tr>
                                    <tr>
                                        <th width="160px">Semester</th>
                                        <td width="400px">: {{ $r->nm_smt }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>: {{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dosen Ke</th>
                                        <td>: {{ $dosen_ke }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-12" style="padding: 0">

                        <form action="{{ $aksi }}" method="post" id="form-nilai">
                            {{ csrf_field() }}
                            <input type="hidden" name="matakuliah" value="{{ $r->nm_mk }}">
                            <input type="hidden" name="id_smt" value="{{ $r->id_smt }}">
                            <input type="hidden" name="id_prodi" value="{{ $r->id_prodi }}">
                            <input type="hidden" name="jenis_jadwal" value="{{ $r->jenis }}">
                            <input type="hidden" name="dosen_ke" value="{{ $dosen_ke }}">

                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                        <tr>
                                            <th width="20px">No.</th>
                                            <th width="120">NIM</th>
                                            <th style="text-align: left">Nama</th>
                                            @if ( $jml_dosen == 2 )
                                                <th width="110">Nil. Dosen 1</th>
                                                <th width="110">Nil. Dosen 2</th>
                                                <th>Rata2</th>
                                            @else
                                                <th style="min-width: 110px;width: 110px">Nilai</th>
                                            @endif

                                            <th width="100">Nilai Huruf</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">

                                        @foreach( $peserta as $ps )
                                            <?php
                                                $cek = DB::table('nilai')
                                                        ->where('id_jdk', $r->id)
                                                        ->where('id_mhs_reg', $ps['id_mhs_reg'])
                                                        ->selectRaw('(a_1 + a_2 + a_3 + a_4 + a_5 + a_6 + a_7 + a_8 + a_9 + a_10 + a_11 + a_12 + a_13 + a_14) as hadir')
                                                        ->first();

                                                $rata = $ps['nil_mid'] + $ps['nil_final'];
                                                if ( $rata > 0 ) {
                                                    $rata = round($rata/2,2);
                                                }
                                            ?>

                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td align="left">{{ $ps['nim'] }}</td>
                                                <td align="left">{{ $ps['nm_mhs'] }}</td>

                                                <input type="hidden" name="nilai_mid_hide[]" value="{{ $ps['nil_mid'] }}">
                                                <input type="hidden" name="nilai_final_hide[]" value="{{ $ps['nil_final'] }}">

                                                @if ( $jml_dosen == 1 )
                                                    <td>
                                                        <input type="text" value="{{ $ps['nil_final'] }}" onkeyup="hitungJml(this.value, 'uts-{{ $ps['nim'] }}')" id="uas-{{ $ps['nim'] }}" maxlength="3" name="uas[]" class="form-control number">
                                                    </td>

                                                @else

                                                    <td>
                                                        @if ( $dosen_ke == '1' )
                                                            <input type="text" value="{{ $ps['nil_mid'] }}" onkeyup="hitungJml(this.value, 'uts-{{ $ps['nim'] }}')" id="uts-{{ $ps['nim'] }}" maxlength="3" name="uts[]" class="form-control number">
                                                        @else
                                                            {{ $ps['nil_mid'] }}
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if ( $dosen_ke == '2' )
                                                            <input type="text" value="{{ $ps['nil_final'] }}" onkeyup="hitungJml(this.value, 'uts-{{ $ps['nim'] }}')" id="uas-{{ $ps['nim'] }}" maxlength="3" name="uas[]" class="form-control number">
                                                        @else
                                                            {{ $ps['nil_final'] }}
                                                        @endif
                                                    </td>
                                                    <td id="rata-{{ $ps['nim'] }}">
                                                        {{ $rata }}
                                                    </td>
                                                @endif

                                                <td>
                                                    {{ empty($ps['nilai_huruf']) ? '-' : $ps['nilai_huruf'] }}
                                                </td>

                                                <input type="hidden" name="id_nilai[]" value="{{ $ps['id_nilai'] }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                            @if ( count($peserta) == 0 )
                                Belum ada peserta kelas
                            @else
                                <hr style="margin-bottom: 5px">
                                <a href="javascript:;" class="submit-nilai btn btn-primary btn-block"><i class="fa fa-save"></i> SIMPAN</a>
                            @endif
                        </form>
                    </div>
                </div>

            </div>

        </div>
      </div>
    </div>

    <div id="modal-info" class="modal fade" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Petunjuk Penginputan Nilai</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <p><b>- Aspek & Persentase Penilaian</b></p>
            <table>
                <!-- <tr>
                    <td width="100">1. Kehadiran</td>
                    <td>: 0%</td>
                </tr> -->
                <tr>
                    <td>- Tugas & Quis</td>
                    <td>: 40%</td>
                </tr>
                <tr>
                    <td>- UTS (MID)</td>
                    <td>: 30%</td>
                </tr>
                <tr>
                    <td>- UAS (FINAL)</td>
                    <td>: 30%</td>
                </tr>
            </table>
            <br>
            <p><b>- Ketentuan</b></p>
            <ol style="list-style-type: decimal; padding-left: 20px">
                <li style="padding-bottom: 10px">Apabila anda mengisi 4 aspek penilaian (Kehadiran, Tugas, UTS, dan UAS)
                     maka anda tidak perlu mengisi Nilai Huruf karena perhitungan nilai huruf akan dilakukan oleh sistem.</li>
                <li style="padding-bottom: 10px">Walaupun anda hanya mengisi Nilai Huruf saja (Mengabaikan 4 aspek penilaian) maka tetap dianggap Sah oleh Sistem</li> 
                <li>Gunakan tanda titik (.) untuk menggantikan tanda koma (,)</li>
            </ol>

            <p><b>- Setelah anda selesai mengisikan nilai, 
            klik tombol 'SIMPAN' (tombol berwarna biru) yang ada di atas ataupun yang ada di bawah halaman </b></p> 

            <br>

            <p><b>- Skala Nilai</b></p>
            <table class="table table-bordered" style="width: 200px">
                <tr>
                    <th>Nilai Huruf</th>
                    <th>Rentang</th>
                </tr>
                <?php
                    $skala = DB::table('skala_nilai')
                                ->where('id_prodi',$r->id_prodi)
                                ->where('nilai_huruf','<>', 'T')
                                ->orderBy('range_atas', 'desc')->get() ?>
                @foreach( $skala as $sk )
                    <tr>
                        <td style="text-align: center">{{ $sk->nilai_huruf }}</td>
                        <td style="text-align: center">{{ $sk->range_nilai }}</td>
                    </tr>
                @endforeach
            </table>

            <hr>
            <center>
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
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
        'use strict';

        $('.submit-nilai').click(function(){
            $('#form-nilai').submit();
        });

        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $(".submit-nilai").attr('disabled','');
                $(".submit-nilai").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
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

        $('.number').keypress(function(event) {
          if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
          }
        });

    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('.submit-nilai').removeAttr('disabled');
        $('.submit-nilai').html('<i class="fa fa-floppy-o"></i> SIMPAN');

        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    function hitung(nim, jenis, value)
    {
        var hadir,tugas,uts,uas,jns,i;

        jns = jenis.split('-');

        for( i = 0; i < jns.length; i++){
            var jml = $('#'+jns[i]+'-'+nim).val();
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
        });
    }

    function hitungJml(value, key)
    {
        var elem = document.getElementById(key);

        if ( parseInt(value) > 100 ) {
            alert('Jumlah nilai hanya boleh antara 0 - 100');
            elem.value = 0;
        }

    }

</script>
@endsection