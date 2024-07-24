@extends('layouts.app')

@section('title','Transkrip Nilai')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Transkrip Nilai
            <div class="pull-right">
                <a href="{{ route('mhs_transkrip_cetak') }}" target="_blank" class="btn btn-primary btn-xs">
                    <i class="fa fa-print"></i> Cetak Transkrip Sementara
                </a>
            </div>

        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                
                {{ Rmt::AlertSuccess() }}

                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <div class="table-responsive">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                <tr>
                                    <th rowspan="2">No.</th>
                                    <th rowspan="2">Kode MK</th>
                                    <th rowspan="2">Nama MK</th>
                                    <th rowspan="2">SKS</th>
                                    <th rowspan="2">SMT</th>
                                    <th colspan="2">Nilai</th>
                                    <th rowspan="2">SKS * N.Indeks</th>
                                </tr>
                                <tr>
                                    <th>Huruf</th>
                                    <th>Indeks</th>
                                </tr>
                                </thead>
                                <tbody align="center">

                                    <?php $total_sks = 0 ?>
                                    <?php $total_sks_diprogram = 0 ?>
                                    <?php $total_nilai = 0 ?>
                                    <?php $total_bobot = 0 ?>
                                    <?php $count_krs = count($krs) ?>
                                    @if ( $count_krs > 0 )

                                        @foreach( $krs as $r )
                                            <?php $kumulatif = $r->sks_mk * $r->nilai_indeks ?>
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $r->kode_mk }}</td>
                                                <td align="left">{{ $r->nm_mk }}</td>
                                                <td>{{ $r->sks_mk }}</td>
                                                <td>{{ $r->smt }}</td>
                                                <td>{{ $r->nilai_huruf }}</td>
                                                <td>{{ number_format($r->nilai_indeks,2) }}</td>
                                                <td>{{ number_format($kumulatif,2) }}</td>
                                            </tr>
                                            <?php
                                                if ( !empty($r->nilai_indeks) ) {
                                                    $total_sks += $r->sks_mk;
                                                }
                                                $total_sks_diprogram += $r->sks_mk;
                                                
                                                $total_nilai += $r->nilai_indeks;
                                                $total_bobot += $kumulatif;
                                            ?>
                                        @endforeach
                                        <tr>
                                            <th colspan="3">Total</th>
                                            <th>{{ $total_sks }}</th>
                                            <th></th>
                                            <th></th>
                                            <th>{{ number_format($total_nilai, 2) }}</th>
                                            <th>{{ number_format($total_bobot, 2) }}</th>
                                        </tr>

                                    @else
                                        <tr><td colspan="8">Tidak ada data</td></tr>
                                    @endif
                                </tbody>
                            </table>

                            @if ( $count_krs > 0 )

                                <table class="table table-bordered" style="width:350px">
                                    <tr>
                                        <td><b>Total SKS Diprogram</b></td>
                                        <td align="center"><b>{{ $total_sks_diprogram }}</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total SKS Lulus</b></td>
                                        <td align="center"><b>{{ $total_sks }}</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Indeks Prestasi Kumulatif (IPK)</b></td>
                                        <td align="center"><b>{{ number_format($total_bobot / $total_sks, 2) }}</b></td>
                                    </tr>
                                </table>
                            @endif

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>

@endsection

@section('registerscript')
<script>
    $(function(){
        if (window.matchMedia("(min-width: 990px)").matches) {
            $('#nav-mini').trigger('click');
        }
    });

</script>
@endsection