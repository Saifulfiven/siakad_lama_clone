@extends('layouts.app')

@section('title','KHS / Nilai Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
        <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
              KHS / Nilai
            </header>
              
            <div class="panel-body" style="padding: 3px 3px;">

                <div class="col-md-12">
                    
                    {{ Rmt::AlertSuccess() }}

                    <div class="row">

                        <div class="col-md-12">
                            <table class="table">
                                <tr>
                                    <td width="120">Tahun Akademik</td>
                                    <td colspan="2">: 
                                        <select class="form-custom" onchange="ubahSmt(this.value)">
                                            @foreach( $semester as $s )
                                                <option value="{{ $s->id_smt }}" {{ Session::get('smt_in_nilai') == $s->id_smt ? 'selected':'' }}>{{ $s->nm_smt }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Jenis</td>
                                    <td>: 
                                        <select class="form-custom" onchange="ubahJenis(this.value)">
                                            <option value="1" {{ Session::get('jeniskrs_in_nilai') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                                            <option value="2" {{ Session::get('jeniskrs_in_nilai') == 2 ? 'selected':'' }}>SP</option>
                                        </select>
                                    </td>
                                    <td class="hidden-xs">
                                        <a href="{{ route('mhs_khs_cetak') }}" class="btn btn-primary btn-sm pull-right" target="blank"><i class="fa fa-print"></i> CETAK KHS</a>
                                    </td>
                                </tr>
                            </table>

                            <div class="table-responsive">

                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">Kode MK</th>
                                        <th rowspan="2">Nama MK</th>
                                        <th rowspan="2">SKS</th>
                                        <th colspan="5">Nilai</th>
                                        <th rowspan="2">SKS * N.Indeks</th>
                                    </tr>
                                    <tr>
                                        <th>Tugas</th>
                                        <th>UTS</th>
                                        <th>UAS</th>
                                        <th>Huruf</th>
                                        <th>Indeks</th>
                                    </tr>
                                    </thead>
                                    <tbody align="center">

                                        <?php $total_sks = 0 ?>
                                        <?php $total_nilai = 0 ?>
                                        <?php $total_bobot = 0 ?>
                                        <?php $count_krs = $krs->count() ?>
                                        
                                        @if ( $count_krs > 0 )

                                            @foreach( $krs as $r )
                                                <?php $cek_kuesioner = 0 ?>
                                                
                                                @if ( !empty($kues_aktif->id) )
                                                
                                                <?php

                                                    $cek_kuesioner = DB::table('kues as k')
                                                            ->leftJoin('kues_jadwal as kj', 'k.id_kues_jadwal', 'kj.id')
                                                            ->where('k.id_mhs_reg', Sia::sessionMhs())
                                                            ->where('k.id_mk', $r->id_mk)
                                                            ->where('k.id_kues_jadwal', $kues_aktif->id)
                                                            ->count();
                                                    ?>

                                                @endif

                                                <?php $kumulatif = $r->sks_mk * $r->nilai_indeks ?>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $r->kode_mk }}</td>
                                                    <td align="left">{{ $r->nm_mk }}</td>
                                                    <td>{{ $r->sks_mk }}</td>
                                                    
                                                    @if (   !empty($kues_aktif) 
                                                            && Session::get('smt_in_nilai') == $kues_aktif->id_smt
                                                            && $cek_kuesioner <= 0
                                                            && !empty($r->id_jam)
                                                        )
                                                        <td colspan="6">
                                                            <a href="{{ route('mhs_kues') }}">
                                                            Isi kuesioner untuk melihat nilai.!</a>
                                                        </td>
                                                    @else
                                                        <td>{{ $r->nil_tugas == null ? '' : number_format($r->nil_tugas,2) }}</td>
                                                        <td>{{ $r->nil_mid == null ? '' : number_format($r->nil_mid,2) }}</td>
                                                        <td>{{ $r->nil_final == null ? '' : number_format($r->nil_final,2) }}</td>
                                                        <td>{{ $r->nilai_huruf }}</td>
                                                        <td>{{ empty( $r->nilai_huruf ) ? '' : number_format($r->nilai_indeks,2) }}</td>
                                                        <td>{{ empty( $r->nilai_huruf ) ? '' : number_format($kumulatif,2) }}</td>
                                                        <?php
                                                            $total_nilai += $r->nilai_indeks;
                                                            $total_bobot += $kumulatif;
                                                        ?>
                                                    @endif
                                                </tr>
                                                <?php $total_sks += $r->sks_mk; ?>

                                            @endforeach
                                            <tr>
                                                <th colspan="3">Total</th>
                                                <th>{{ $total_sks }}</th>
                                                <th colspan="4"></th>
                                                
                                                <th>{{ number_format($total_nilai, 2) }}</th>
                                                <th>{{ number_format($total_bobot, 2) }}</th>
                                            </tr>

                                        @else
                                            <tr><td colspan="10">Tidak ada data</td></tr>
                                        @endif
                                    </tbody>
                                </table>

                                @if ( $count_krs > 0 && Session::get('jeniskrs_in_nilai') == 1 )

                                    <?php $ipks = Sia::ipk($ipk->tot_nilai, $ipk->tot_sks) ?>

                                    <table class="table table-bordered" style="width:450px">
                                        <tr>
                                            <th>Keterangan</th>
                                            <th>SKS Semester</th>
                                            <th>SKS Telah Dilulusi</th>
                                        </tr>
                                        <tr>
                                            <td>Kumulatif Semester</td>
                                            <td>{{ $total_sks }}</td>
                                            <td>{{ $ipk->tot_sks }}</td>
                                        </tr>
                                        <tr>
                                            <?php $ipknum = Sia::ipk($total_bobot, $total_sks) ?>
                                            <td>Indeks Prestasi Semester (IPS)</td>
                                            <td colspan="2">{{ $ipknum }}</td>
                                        </tr>
                                        <tr>
                                            <td>Indeks Prestasi Kumulatif (IPK)</td>
                                            <td colspan="2">{{ $ipks }}</td>
                                        </tr>
                                        <tr>
                                            <td>Max. Beban SKS Semester Depan</td>
                                            <td colspan="2">{{ Sia::maxSks($ipknum) }}</td>
                                        </tr>
                                    </table>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </section>
    </div>
  </div>
</div>

@endsection

@section('registerscript')
<script>

    function ubahSmt(id)
    {
        window.location.href='{{ route('mhs_khs') }}?smt='+id;
    }
    function ubahJenis(id)
    {
        window.location.href='{{ route('mhs_khs') }}?ubah_jenis='+id;
    }
</script>
@endsection