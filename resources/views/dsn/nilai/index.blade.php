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
                        <div class="pull-left" style="padding-top: 5px">
                            <b>INPUT NILAI</b>
                        </div>
                        <a href="javascript:;" onclick="window.history.back()" style="margin: 3px 3px" class="btn btn-success btn-xs pull-right"><i class="fa fa-list"></i> KEMBALI</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        {{ Rmt::AlertSuccess() }}
                        {{ Rmt::AlertError() }}

                        <div class="table-responsive">
                            <table border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th>Matakuliah</th>
                                        <td>: {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
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


                @if ( $r->jenjang == "S2" )

                    @include('dsn.nilai.index-s2')

                @else

                    <div class="row">
                        <div class="col-md-12">

                            <div class="pull-right">
                                <a href="{{ route('dsn_nilai_ekspor', ['id' => $r->id]) }}"
                                    class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i> EXCEL</a>
                                    &nbsp; 
                                <a href="{{ route('dsn_nilai_cetak', ['id' => $r->id, 'jenis' => Request::get('jenis')]) }}"
                                    class="btn btn-default btn-sm" target="_blank"><i class="fa fa-print"></i> CETAK</a>
                                    &nbsp; 
                                <a href="javascript:;" data-toggle="modal" data-target="#modal-info"
                                    class="btn btn-danger btn-sm"><i class="fa fa-question-circle"></i> Petunjuk</a>

                                @if ( Sia::canAction(Session::get('jdm.ta')) && $jenis_jadwal == 1 )
                                    &nbsp;  
                                    <a href="javascript:;" class="submit-nilai btn btn-primary btn-sm" style="margin: 3px 3px" ><i class="fa fa-save"></i> SIMPAN</a>
                                @endif
                            </div>

                            <form action="{{ route('dsn_nilai_update') }}" method="post" id="form-nilai">
                                {{ csrf_field() }}
                                <input type="hidden" name="matakuliah" value="{{ $r->nm_mk }}">
                                <input type="hidden" name="id_smt" value="{{ $r->id_smt }}">
                                <input type="hidden" name="id_prodi" value="{{ $r->id_prodi }}">
                                <input type="hidden" name="jenis_jadwal" value="{{ $jenis_jadwal }}">

                                <div class="table-responsive">
                                    @if ( !Sia::canAction(Session::get('jdm.ta')) && $jenis_jadwal == 1 )
                                        <div class="clearfix"></div>
                                        <div class="alert alert-danger">
                                            Penginputan nilai untuk matakuliah ini telah tertutup
                                        </div>
                                    @endif

                                    <!-- Jika  -->
                                    @if ( $jenis_jadwal == 2 )
                                        @if ( !Sia::inputNilaiSp($r->id_smt, $r->id_prodi) )
                                            <div class="clearfix"></div>
                                            <div class="alert alert-danger">
                                                Penginputan nilai untuk matakuliah ini telah tertutup
                                            </div>
                                        @endif
                                    @endif

                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                        <thead class="custom">
                                            <tr>
                                                <th width="20px">No.</th>
                                                <th width="120">NIM</th>
                                                <th style="text-align: left">Nama</th>
                                                <!-- <th width="110">Nil. Kehadiran</th> -->
                                                <th width="110">Nil. Tugas</th>
                                                <th width="110">Nil. MID</th>
                                                <th width="110">Nil. Final</th>
                                                <th>Preview</th>
                                                <th width="100">Nilai Huruf</th>
                                            </tr>
                                        </thead>
                                        <tbody align="center">

                                            <?php $jml_hadir_arr = [] ?>

                                            @foreach( $peserta as $ps )
                                                <?php
                                                    $cek = DB::table('nilai')
                                                            ->where('id_jdk', $r->id)
                                                            ->where('id_mhs_reg', $ps->id_mhs_reg)
                                                            ->selectRaw('(a_1 + a_2 + a_3 + a_4 + a_5 + a_6 + a_7 + a_8 + a_9 + a_10 + a_11 + a_12 + a_13 + a_14) as hadir')
                                                            ->first();

                                                    $jml_hadir_arr[$ps->id_nilai] = $cek->hadir;
                                                ?>

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td align="left">{{ $ps->nim }}</td>
                                                    <td align="left">{{ $ps->nm_mhs }}</td>

                                                    @if ( $cek->hadir < $min_kehadiran )

                                                        <td colspan="5"><i style="color: red" class="fa fa-ban"></i> Kehadiran belum mencapai 50% + 1 (dari {{ $jml_pertemuan }} pertemuan)</td>
                                                        <input type="hidden" name="nilai[]" value="non">
                                                        <input type="hidden" name="kehadiran[]" value="0">
                                                        <input type="hidden" name="uts[]" value="0">
                                                        <input type="hidden" name="uas[]" value="0">
                                                        <input type="hidden" name="tugas[]" value="0">

                                                    @else

                                                        @if ( $jenis_jadwal == 1 )

                                                            <input type="hidden" name="kehadiran[]" value="0">
                                                            <!-- <td><input type="text" <?= !Sia::canAction(Session::get('jdm.ta')) ? 'disabled=""':'' ?> value="{{ $ps->nil_kehadiran }}" onchange="hitung('{{ $ps->nim }}','tugas-uts-uas', this.value)" id="hadir-{{ $ps->nim }}" maxlength="5" name="kehadiran[]" class="form-control number"></td> -->
                                                            <td><input type="text" <?= !Sia::canAction(Session::get('jdm.ta')) ? 'disabled=""':'' ?> value="{{ $ps->nil_tugas }}" onchange="hitung('{{ $ps->nim }}','hadir-uts-uas', this.value)" id="tugas-{{ $ps->nim }}" maxlength="5" name="tugas[]" class="form-control number"></td>
                                                            <td><input type="text" <?= !Sia::canAction(Session::get('jdm.ta')) ? 'disabled=""':'' ?> value="{{ $ps->nil_mid }}" onchange="hitung('{{ $ps->nim }}','hadir-tugas-uas', this.value)" id="uts-{{ $ps->nim }}" maxlength="5" name="uts[]" class="form-control number"></td>
                                                            <td><input type="text" <?= !Sia::canAction(Session::get('jdm.ta')) ? 'disabled=""':'' ?> value="{{ $ps->nil_final }}" onchange="hitung('{{ $ps->nim }}','hadir-tugas-uts', this.value)" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number"></td>
                                                            <td id="pre-{{ $ps->nim }}" style="background: #eee">
                                                                {{ $ps->nilai_angka > 0 ? $ps->nilai_angka.' = ' : '' }}
                                                                {{ !empty($ps->nilai_huruf) ? $ps->nilai_huruf : '-' }}
                                                            </td>
                                                            <td>
                                                                <select id="select-huruf-{{ $ps->nim }}" onchange="updatePriority(2, '{{ $ps->nim }}')" name="nilai[]" <?= !Sia::canAction(Session::get('jdm.ta')) ? 'disabled=""':'' ?> class="form-control" style="width: 70px">
                                                                    <option value="-">--</option>
                                                                    @foreach( Sia::skalaNilai($r->id_prodi) as $sn )
                                                                        <option value="{{ $sn->nilai_indeks }}-{{ $sn->nilai_huruf }}" {{ $sn->nilai_indeks.'-'.$sn->nilai_huruf == $ps->nilai_indeks.'-'.$ps->nilai_huruf ? 'selected':'' }}>{{ $sn->nilai_huruf }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>

                                                        @else

                                                            <td><input type="text" <?= !Sia::inputNilaiSp($r->id_smt, $r->id_prodi) ? 'disabled=""':'' ?> value="{{ $ps->nil_tugas }}" onchange="hitung('{{ $ps->nim }}','hadir-uts-uas', this.value)" id="tugas-{{ $ps->nim }}" maxlength="5" name="tugas[]" class="form-control number"></td>
                                                            <td><input type="text" <?= !Sia::inputNilaiSp($r->id_smt, $r->id_prodi) ? 'disabled=""':'' ?> value="{{ $ps->nil_mid }}" onchange="hitung('{{ $ps->nim }}','hadir-tugas-uas', this.value)" id="uts-{{ $ps->nim }}" maxlength="5" name="uts[]" class="form-control number"></td>
                                                            <td><input type="text" <?= !Sia::inputNilaiSp($r->id_smt, $r->id_prodi) ? 'disabled=""':'' ?> value="{{ $ps->nil_final }}" onchange="hitung('{{ $ps->nim }}','hadir-tugas-uts', this.value)" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number"></td>
                                                            <td id="pre-{{ $ps->nim }}" style="background: #eee">
                                                                {{ $ps->nilai_angka > 0 ? $ps->nilai_angka.' = ' : '' }}
                                                                {{ !empty($ps->nilai_huruf) ? $ps->nilai_huruf : '-' }}
                                                            </td>
                                                            <td>
                                                                <select id="select-huruf-{{ $ps->nim }}" onchange="updatePriority(2, '{{ $ps->nim }}')" name="nilai[]" <?= !Sia::inputNilaiSp($r->id_smt, $r->id_prodi) ? 'disabled=""':'' ?> class="form-control" style="width: 70px">
                                                                    <option value="-">--</option>
                                                                    @foreach( Sia::skalaNilai($r->id_prodi) as $sn )
                                                                        <option value="{{ $sn->nilai_indeks }}-{{ $sn->nilai_huruf }}" {{ $sn->nilai_indeks.'-'.$sn->nilai_huruf == $ps->nilai_indeks.'-'.$ps->nilai_huruf ? 'selected':'' }}>{{ $sn->nilai_huruf }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>

                                                        @endif

                                                    @endif
                                                    <input type="hidden" name="id_nilai[]" value="{{ $ps->id_nilai }}">
                                                    <input type="hidden" name="nil_lama[]" value="{{ $ps->nilai_huruf }}">
                                                    <input type="hidden" name="huruf_baru[]" id="huruf-{{ $ps->nim }}" value="{{ $ps->nilai_huruf }}">
                                                    <input type="hidden" name="nil_angka[]" id="angka-{{ $ps->nim }}" value="{{ $ps->nilai_angka }}">
                                                    <input type="hidden" name="nil_huruf_priority[]" id="priority-{{ $ps->nim }}" value="2">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <?php Session::set('kehadiran', $jml_hadir_arr); ?>

                                </div>
                                @if ( count($peserta) == 0 )
                                    Belum ada peserta kelas
                                @else

                                    @if ( Sia::canAction(Session::get('jdm.ta')) && $jenis_jadwal == 1)
                                        <hr style="margin-bottom: 5px">
                                        <a href="javascript:;" class="submit-nilai btn btn-primary btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-save"></i> SIMPAN</a>
                                    @elseif ( $jenis_jadwal == 2 && Sia::inputNilaiSp($r->id_smt, $r->id_prodi) )
                                        <hr style="margin-bottom: 5px">
                                        <a href="javascript:;" class="submit-nilai btn btn-primary btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-save"></i> SIMPAN</a>
                                    @endif

                                @endif
                            </form>
                        </div>
                    </div>

                @endif
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

    function updatePriority(value,nim){
        $('#priority-'+nim).val(value);
    }

</script>
@endsection