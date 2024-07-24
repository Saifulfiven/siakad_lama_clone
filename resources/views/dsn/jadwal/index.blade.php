@extends('layouts.app')

@section('title','Jadwal Mengajar')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Jadwal Mengajar
            <select  onchange="filter('ta',this.value)">
                @foreach( $semester as $sm )
                    <option value="{{ $sm->id_smt }}" {{ Session::get('jdm.ta') == $sm->id_smt ? 'selected':'' }}>{{ $sm->nm_smt }}</option>
                @endforeach
            </select>
        </header>
        
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                
                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}

                <div class="row">

                    <div class="col-md-12">

                        <div class="table-responsive">
                            <span class="hidden-xs">FILTER : </span>
                            <select class="form-custom" onchange="filter('jenis',this.value)">
                                <option value="all">All Jenis</option>
                                <option value="1" {{ Session::get('jdm.jenis') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                                <option value="2" {{ Session::get('jdm.jenis') == 2 ? 'selected':'' }}>SP</option>
                            </select>

                            <select class="form-custom" onchange="filter('prodi',this.value)">
                                <option value="all">Semua Prodi</option>
                                @foreach( Sia::listProdiAll() as $pr )
                                    <option value="{{ $pr->id_prodi }}" {{ Session::get('jdm.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
                                @endforeach
                            </select>

                            <div class="pull-right">
                                <a href="{{ route('dsn_jadwal_cetak') }}" target="_blank" class="btn btn-primary btn-sm hidden-xs"><i class="fa fa-print"> CETAK</i></a>
                                &nbsp; &nbsp; 
                                <button data-toggle="modal" data-target="#modal-cetak" class="btn btn-primary btn-sm hidden-xs"><i class="fa fa-print"> SK MENGAJAR</i></button>
                            </div>

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Nama matakuliah</th>
                                        <th>Dosen ke</th>
                                        <th>Program Studi</th>
                                        <th>Kelas</th>
                                        <th>Ruang</th>
                                        <th>Tools</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    <?php $no = 1 ?>

                                    @foreach( $jadwal as $r )
                                        
                                        <?php
                                            // dd($jadwal);
                                            $dsn = DB::table('dosen')
                                                    ->where('id', $r->id_dosen)
                                                    ->first();

                                            $dosen = Sia::namaDosen($dsn->gelar_depan, $dsn->nm_dosen, $dsn->gelar_belakang); ?>
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td align="left">{{ Rmt::hari($r->hari) }}</td>
                                            <td>
                                                {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
                                            </td>
                                            <td align="left">
                                                {{ $r->nm_mk }} ({{ $r->sks_mk }})
                                            </td>
                                            <td>{{ $r->dosen_ke }}</td>
                                            <td>{{ $r->jenjang.' '.$r->nm_prodi }}</td>
                                            <td>{{ $r->kode_kls }}</td>
                                            <td>{{ $r->nm_ruangan }}</td>
                                            <td>
                                                <?= Rmt::link(route('dsn_lms', ['id' => $r->id, 'jenis' => $r->jenis]), 'E-Learning') ?>&nbsp;

                                                <?= Rmt::link(route('dsn_absen', ['id' => $r->id, 'jenis' => $r->jenis]), 'Absen') ?>&nbsp; 
                                                <?= Rmt::link(route('dsn_nilai', ['id' => $r->id, 'jenis' => $r->jenis]), 'Nilai') ?>&nbsp; 
                                                <!-- <a href="javascript:;" 
                                                        onclick="detail(
                                                            '{{ $r->id }}',
                                                            '{{ $r->nm_mk }}',
                                                            '{{ $r->id_dosen }}',
                                                            '{{ $dosen }}',
                                                            '{{ $r->kode_kls }}',
                                                            '{{ $r->ruangan }}',
                                                            '{{ $r->jenjang }} {{ $r->nm_prodi }}'
                                                        )" class="btn btn-primary btn-xs" title="Kuesioner">
                                                    Hasil Kuesioner
                                                </a> -->
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            @if ( count($jadwal) == 0 )
                                Tidak ada jadwal mengajar
                            @endif

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>

<div id="modal-kuesioner" class="modal fade container" data-width="800" style="top: 20% !important" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 id="matakuliah-detail">Hasil Penilaian Mahasiswa Terhadap Dosen</h4>
    </div>
    <div class="modal-body">
        <div class="ajax-message"></div>

        <div class="col-md-12" style="padding-bottom: 40px">
            <table>
                <tr>
                    <td width="150">Nama Matakuliah</td>
                    <td width="250">: <span id="matakuliah"></span></td>
                </tr>
                <tr>
                    <td>Nama Dosen</td>
                    <td>: <span id="dosen"></span></td>
                </tr>
                <tr>
                    <td width="100">Kelas</td>
                    <td>: <span id="kelas"></span></td>
                </tr>
                <tr>
                    <td width="100">Ruangan</td>
                    <td>: <span id="ruangan"></span></td>
                </tr>

            </table>

            <div id="content-detail"></div>

        </div>

    </div>
</div>

<div id="modal-cetak" class="modal fade" data-width="400" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Cetak SK Mengajar</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <form action="{{ route('dsn_sk_mengajar') }}" target="_blank" method="get">
            <div class="form-group">
                <label class="control-label">Masukkan Tanggal Cetak</label>
                <input type="date" name="tgl" class="form-control" required="">
            </div>
            
            <button class="btn btn-primary btn-block">Cetak</button>
        </form>
    </div>
    <!-- //modal-body-->
</div>

@endsection

@section('registerscript')
<script>
    function filter(modul, value)
    {
        window.location.href = '{{ route('dsn_jadwal_filter') }}?modul='+modul+'&val='+value;
    }

    function filterTa(value){
        window.location.href='?ta='+value;
    }

    function detail(id_jdk, matakuliah, id_dosen, dosen, kelas, ruangan, prodi)
    {
        $('#modal-kuesioner').modal('show');
        $('#matakuliah').html(matakuliah);
        $('#dosen').html(dosen);
        $('#kelas').html(kelas);
        $('#ruangan').html(ruangan);

        $.ajax({
            url: '{{ route('dsn_kuesioner_detail') }}',
            data : { 
                preventCache : new Date(), 
                id_jdk:id_jdk, 
                id_dosen: id_dosen,
                dosen: dosen,
                matakuliah: matakuliah,
                prodi: prodi
            },
            beforeSend: function( xhr ) {
                $('#content-detail').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            },
            success: function(data){
                $('#content-detail').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
            }
        });
    }
</script>
@endsection