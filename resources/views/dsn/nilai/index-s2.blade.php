<div class="row">
    <div class="col-md-12">

        <div class="pull-right">
            <a href="{{ route('dsn_nilai_ekspor', ['id' => $r->id]) }}" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i> EXCEL</a>
                &nbsp; 
            <a href="{{ route('dsn_nilai_cetak_s2', ['id' => $r->id]) }}"
                class="btn btn-default btn-sm" target="_blank"><i class="fa fa-print"></i> CETAK</a>
                &nbsp; 
            <a href="javascript:;" data-toggle="modal" data-target="#modal-info"
                class="btn btn-danger btn-sm"><i class="fa fa-question-circle"></i> Petunjuk</a>

            @if ( Sia::canAction(Session::get('jdm.ta'), false, 2) && $jenis_jadwal == 1 )
                &nbsp;  
                <a href="javascript:;" class="submit-nilai btn btn-primary btn-sm" style="margin: 3px 3px" ><i class="fa fa-save"></i> SIMPAN</a>
            @endif
        </div>

        <?php 

            $dosen_ajar = DB::table('dosen_mengajar')
                    ->where('id_jdk', $r->id)
                    ->where('id_dosen', Sia::sessionDsn())
                    ->first();

            $jml_dosen = DB::table('dosen_mengajar')
                        ->where('id_jdk', $r->id)
                        ->count();

            $dosen_ke = $dosen_ajar->dosen_ke;

            if ( $jml_dosen == 1 ) {
                $aksi = route('dsn_nilai_update_s2_single');
            } else {
                $aksi = route('dsn_nilai_update_s2');
            }

        ?>
        <!-- <center>Mohon maaf, untuk sementara penginputan nilai di nonaktifkan</center> -->
        
        <form action="{{ $aksi }}" method="post" id="form-nilai">
            {{ csrf_field() }}
            <input type="hidden" name="matakuliah" value="{{ $r->nm_mk }}">
            <input type="hidden" name="id_smt" value="{{ $r->id_smt }}">
            <input type="hidden" name="id_prodi" value="{{ $r->id_prodi }}">
            <input type="hidden" name="jenis_jadwal" value="{{ $jenis_jadwal }}">
            <input type="hidden" name="dosen_ke" value="{{ $dosen_ke }}">

            <div class="table-responsive">
                @if ( !Sia::canAction(Session::get('jdm.ta'), false, 2) && $jenis_jadwal == 1 )
                    <div class="clearfix"></div>
                    <div class="alert alert-danger">
                        Penginputan nilai untuk matakuliah ini telah berakhir
                    </div>
                @endif
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                    <thead class="custom">
                        <tr>
                            <th width="20px">No.</th>
                            <th width="120">NIM</th>
                            <th style="text-align: left">Nama</th>
                            @if ( $jml_dosen == 2 )
                                <th width="110">Nil. Dosen 1</th>
                                <th width="110">Nil. Dosen 2</th>
                            @else
                                <th width="110">Nilai</th>
                            @endif
                            @if ( $jml_dosen == 2 )
                                <th>Rata2</th>
                            @endif
                            <th width="100">Nilai Huruf</th>
                        </tr>
                    </thead>
                    <tbody align="center">

                        @foreach( $peserta as $ps )
                            <?php
                                $cek = DB::table('nilai')
                                        ->where('id_jdk', $r->id)
                                        ->where('id_mhs_reg', $ps->id_mhs_reg)
                                        ->selectRaw('(a_1 + a_2 + a_3 + a_4 + a_5 + a_6 + a_7 + a_8 + a_9 + a_10 + a_11 + a_12 + a_13 + a_14) as hadir')
                                        ->first();

                                $rata = $ps->nil_mid + $ps->nil_final;
                                if ( $rata > 0 ) {
                                    $rata = round($rata/2,2);
                                }
                            ?>

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td align="left">{{ $ps->nim }}</td>
                                <td align="left">{{ $ps->nm_mhs }}</td>

                                <input type="hidden" name="nilai_mid_hide[]" value="{{ $ps->nil_mid }}">
                                <input type="hidden" name="nilai_final_hide[]" value="{{ $ps->nil_final }}">

                                @if ( $jml_dosen == 1 )

                                    <td>
                                        <input type="text" <?= !Sia::canAction(Session::get('jdm.ta'), false, 2) && $jenis_jadwal == 1 ? 'disabled=""':'' ?> value="{{ $ps->nil_final }}" onkeyup="hitungJml(this.value, 'uas-{{ $ps->nim }}')" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number">
                                    </td>
                                
                                @else

                                    <td>
                                        @if ( $dosen_ke == '1' )
                                            <input type="text" <?= !Sia::canAction(Session::get('jdm.ta'), false, 2) && $jenis_jadwal == 1 ? 'disabled=""':'' ?> value="{{ $ps->nil_mid }}" onkeyup="hitungJml(this.value, 'uts-{{ $ps->nim }}')" id="uts-{{ $ps->nim }}" maxlength="5" name="uts[]" class="form-control number">
                                        @else
                                            {{ $ps->nil_mid }}
                                        @endif
                                    </td>

                                    <td>
                                        @if ( $dosen_ke == '2' )
                                            <input type="text" <?= !Sia::canAction(Session::get('jdm.ta'), false, 2) && $jenis_jadwal == 1 ? 'disabled=""':'' ?> value="{{ $ps->nil_final }}" onkeyup="hitungJml(this.value, 'uas-{{ $ps->nim }}')" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number">
                                        @else
                                            {{ $ps->nil_final }}
                                        @endif
                                    </td>
                                @endif

                                @if ( $jml_dosen == 2 )
                                    <td id="rata-{{ $ps->nim }}">
                                        {{ $rata }}
                                    </td>
                                @endif

                                <td>
                                    {{ empty($ps->nilai_huruf) ? '-' : $ps->nilai_huruf }}
                                </td>

                                <input type="hidden" name="id_nilai[]" value="{{ $ps->id_nilai }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            @if ( count($peserta) == 0 )
                Belum ada peserta kelas
            @else

                @if ( Sia::canAction(Session::get('jdm.ta'), false, 2) && $jenis_jadwal == 1)
                    <hr style="margin-bottom: 5px">
                    <a href="javascript:;" class="submit-nilai btn btn-primary btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-save"></i> SIMPAN</a>
                @elseif ( $jenis_jadwal == 2 )
                    <hr style="margin-bottom: 5px">
                    <a href="javascript:;" class="submit-nilai btn btn-primary btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-save"></i> SIMPAN</a>
                @endif
            @endif
        </form>
    </div>
</div>

<script>
    function hitungJml(value, key)
    {
        var elem = document.getElementById(key);

        if ( parseInt(value) > 100 ) {
            alert('Jumlah nilai hanya boleh antara 0 - 100');
            elem.value = 0;
        }

    }
</script>