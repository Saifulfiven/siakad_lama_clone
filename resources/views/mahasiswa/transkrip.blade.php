@extends('layouts.app')

@section('title','Transkrip Nilai')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Transkrip Nilai
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">
            
            @include('mahasiswa.link-cepat')

            <div class="col-md-9">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                    </div>
                </div>
                
                {{ Rmt::AlertSuccess() }}

                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="130px">NIM</th>
                                        <td>:
                                            <select class="form-custom" onchange="ubahNim(this.value)">
                                                @foreach( $nim as $n )
                                                    <option value="{{ $n->id }}" {{ $mhs->id_reg_pd == $n->id ? 'selected':'' }}>{{ $n->nim }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                       <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th>Nama</th><td>: {{ $mhs->nm_mhs }}</td>
                                    </tr>
                                    <tr>
                                        <th>Angkatan</th><td>: {{ substr($mhs->nim,0,4) }}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <br>
                        <div class="table-responsive">
                            <table border="0" width="100%" style="margin-bottom: 10px">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td width="120"><a href="{{ route('mahasiswa_transkrip_sementara_cetak', ['id' => $id_mahasiswa]) }}" class="btn btn-primary btn-xs pull-right" target="_blank"><i class="fa fa-print"></i> CETAK TRANSKRIP SEMENTARA</a></td>
                                    <td width="140"><a href="{{ route('mahasiswa_transkrip_cetak', ['id' => $id_mahasiswa]) }}" class="btn btn-primary btn-xs pull-right" target="_blank"><i class="fa fa-print"></i> CETAK TRANSKRIP</a></td>
                                    <td width="120"><a href="javascript::void(0);" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#modal-print-ijazah"><i class="fa fa-print"></i> CETAK IJAZAH</a></td>
                                </tr>
                            </table>

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
                                    <?php $mk_terganti = [] ?>

                                    @if ( $count_krs > 0 )

                                        @foreach( $krs as $r )
                                            <?php $kumulatif = $r->sks_mk * $r->nilai_indeks ?>

                                            <!-- Skip Matakuliah terganti -->
                                            <?php if ( !empty($r->mk_terganti) ) {
                                                $mk_terganti[] = $r->mk_terganti;
                                            }

                                            if ( in_array($r->id_mk, $mk_terganti) ) {
                                                continue;
                                            }
                                            ?>
                                            
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $r->kode_mk }}</td>
                                                <td align="left">{{ strtoupper($r->nm_mk) }}</td>
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
                                            <th>{{ $total_sks_diprogram }}</th>
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
                                        <td align="center">
                                            @if ( empty($total_bobot) || empty($total_sks) )
                                                <b>0</b>
                                            @else
                                                <b>{{ number_format($total_bobot / $total_sks, 2) }}</b>
                                            @endif
                                        </td>
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


<div id="modal-print-ijazah" class="modal fade md-stickTop" tabindex="-1" data-width="350">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Masukkan tanggal keluar ijazah</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <form action="{{ route('mahasiswa_ijazah_cetak', ['id' => $mhs->id]) }}" target="_blank">
            <div class="form-group">
                <div>
                    <label class="control-label">Tanggal Ijazah<span>*</span></label>
                    <input type="date" class="form-control mw-2" name="tgl_ijazah" value="{{ !empty($mhs->tgl_ijazah) ? Carbon::parse($mhs->tgl_ijazah)->format('Y-m-d') : date('Y-m-d') }}" required="">
                </div>
            </div>
            
            <button type="submit" id="btn-submit-impor-pin" class="btn btn-primary btn-sm">CETAK</button>

        </form>
    </div>
    <!-- //modal-body-->
</div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>
    $(function(){
        $('#nav-mini').trigger('click');
    });
    
    function ubahNim(id)
    {
        window.location.href='{{ route('mahasiswa_transkrip',['id' => $mhs->id]) }}?ubah_nim='+id;
    }

</script>
@endsection