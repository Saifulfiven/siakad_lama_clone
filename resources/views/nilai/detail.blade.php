@extends('layouts.app')

@section('title','')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>Nilai Detail</a></li>
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
                       <!--  <a href="javascript:;" id="submit-nilai" class="btn btn-primary btn-sm pull-right" style="margin: 3px 3px"><i class="fa fa-save"></i> SIMPAN</a>
                        <a href="{{ route('nil_cetak', ['id' => $r->id, 'dosen' => $r->dosen]) }}" class="btn btn-default btn-sm pull-right" style="margin: 3px 3px" target="_blank"><i class="fa fa-print"></i> CETAK</a> -->
                        <a href="javascript:;" onclick="window.history.back()" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> KEMBALI</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <div class="ajax-message"></div>

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

                        <div class="table-responsive">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        @if ( $r->id_prodi == 61101 )
                                            <th width="110">Nil. Dosen 1</th>
                                            <th width="110">Nil. Dosen 2</th>
                                        @else
                                            <th width="110">Nil. Kehadiran</th>
                                            <th width="110">Nil. Tugas</th>
                                            <th width="110">Nil. MID</th>
                                            <th width="110">Nil. Final</th>
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
                                         @if ( $r->id_prodi == 61101 )
                                            <td>{{ $ps->nil_mid }}</td>
                                            <td>{{ $ps->nil_final }}</td>
                                        @else
                                            <td>{{ $ps->nil_kehadiran }}</td>
                                            <td>{{ $ps->nil_tugas }}</td>
                                            <td>{{ $ps->nil_mid }}</td>
                                            <td>{{ $ps->nil_final }}</td>
                                        @endif
                                        <td>{{ $ps->nilai_huruf }}</td>
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