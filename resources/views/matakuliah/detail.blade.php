@extends('layouts.app')

@section('title','Detail Matakuliah')

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Detail Matakuliah
                <div class="pull-right">
                    {{ Rmt::AlertSuccess() }}
                    <a href="{{ route('matakuliah') }}" style="margin: 3px 3px" class="btn btn-success btn-xs pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                    @if ( $mk->terpakai == 0 )
                    <a href="{{ route('matakuliah_edit', ['id' => $mk->id]) }}" class="btn btn-warning btn-xs pull-right"    style="margin: 3px 3px" ><i class="fa fa-pencil"></i> UBAH</a>
                    @endif
                </div>
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="200px">Kode Matakuliah</th>
                                        <td> : {{ $mk->kode_mk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Matakuliah</th>
                                        <td> : {{ $mk->nm_mk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td> : {{ $mk->nm_prodi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Konsentrasi</th>
                                        <td> : {{ $mk->nm_konsentrasi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Matakuliah yang digantikan</th>
                                        <td> : {{ $mk_terganti }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Matakuliah</th>
                                        <td> : {{ Sia::jenisMatakuliah($mk->jenis_mk) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kelompok Matakuliah</th>
                                        <td> : {{ Sia::kelompokMatakuliah($mk->kelompok_mk) }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKS Tatap Muka</th>
                                        <td> : {{ $mk->sks_tm }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKS Praktikum</th>
                                        <td> : {{ $mk->sks_prak }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKS Praktek Lapangan</th>
                                        <td> : {{ $mk->sks_prak_lap }}</td>
                                    </tr>
                                    <tr>
                                        <th>SKS Simulasi</th>
                                        <td> : {{ $mk->sks_sim }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total SKS</th>
                                        <td> : {{ $mk->sks_mk }} <small>(SKS Tatap Muka + SKS Praktikum + SKS Praktek Lapangan + SKS Simulasi)</small></td>
                                    </tr>
                                    <tr>
                                        <th>Ada SAP</th>
                                        <td> : {{ Rmt::yesNo($mk->a_sap) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ada Silabus</th>
                                        <td> : {{ Rmt::yesNo($mk->a_silabus) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ada Bahan Ajar</th>
                                        <td> : {{ Rmt::yesNo($mk->a_bahan_ajar) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ada Acara Praktek</th>
                                        <td> : {{ Rmt::yesNo($mk->acara_praktek) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ada Diktat</th>
                                        <td> : {{ Rmt::yesNo($mk->a_diktat) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Mulai Efektif</th>
                                        <td> : {{ empty($mk->tgl_mulai_efektif) ? '' : Rmt::formatTgl($mk->tgl_mulai_efektif) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Akhir Efektif</th>
                                        <td> : {{ empty($mk->tgl_akhir_efektif) ? '' : Rmt::formatTgl($mk->tgl_akhir_efektif) }}</td>
                                    </tr>
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