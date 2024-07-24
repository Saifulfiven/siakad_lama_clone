@extends('mobile.layouts.app')

@section('title','Kuis : Jawaban Peserta')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">

            {{ Rmt::alertError() }}

          <section class="panel">
            <header class="panel-heading">
                {{ $kuis->judul }}
            </header>
            <div class="panel-body" style="padding-top: 13px">
                <a href="{{ route('m_kuis_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}" class="btn-loading btn btn-success btn-block btn-sm">Kembali</a>
                <hr>

                {!! $kuis->ket !!}
                <hr>

                <div class="row">
                    <div class="col-md-12">

                        <div class="alert alert-info">
                            Klik pada nama mahasiswa untuk melihat jawaban atau mengubah penilaian (apabila soal essay)
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Peserta Kelas</th>
                                        <th>Dikerjakan</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $peserta_kelas as $ps )
                                        <?php

                                            $peserta = $ps->nim.' - '. trim($ps->nm_mhs);

                                            $nilai = DB::table('lmsk_kuis_hasil as kh')
                                                    ->leftJoin('lmsk_kuis_soal as ks', 'kh.id_kuis_soal', 'ks.id')
                                                    ->leftJoin('lmsk_kuis as k', 'k.id', 'ks.id_kuis')
                                                    ->where('k.id', $kuis->id)
                                                    ->where('kh.id_peserta', $ps->id_mhs_reg)
                                                    ->sum('kh.penilaian');
                                            $dikerjakan = DB::table('lmsk_telah_kuis')
                                                            ->where('id_kuis', $kuis->id)
                                                            ->where('id_peserta', $ps->id_mhs_reg)
                                                            ->where('sisa_waktu',0)->count();

                                        ?>
                                        <tr>
                                            <td align="center">{{ $loop->iteration }}</td>
                                            <td><a href="{{ route('m_kuis_jawaban_detail', ['id_jadwal' => $r->id, 'id' => $kuis->id, 'id_peserta' => $ps->id_mhs_reg]) }}" class="btn-loading">{{ $peserta }}</a></td>
                                            <td align="center">
                                                <?= $dikerjakan > 0 ? '<i class="fa fa-check" style="color: green"' : '<i class="fa fa-ban" style="color: red"' ?>
                                            </td>
                                            <td align="center">
                                                {{ number_format($nilai,2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>

@endsection