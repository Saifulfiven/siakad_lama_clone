@extends('layouts.app')

@section('title','Kuesioner')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Pengisian Kuesioner
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                
                <div class="row">

                    <div class="col-md-12">

                        <div class="table-responsive">

                            @if ( empty($kues_aktif->id) )
                                <br>
                                <br>
                                <div class="alert alert-info">
                                    <b>INFORMASI : </b> Saat ini belum ada kuesioner yang tersedia atau 
                                    masa pengisian kuesioner pada semester ini telah berakhir
                                </div>
                            @else

                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                        <tr>
                                            <th width="20px">No.</th>
                                            <th>Nama matakuliah</th>
                                            <th>SKS</th>
                                            <th>Kelas</th>
                                            <th>Dosen</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">

                                        @foreach( $jadwal as $r )
                                            <?php
                                                $cek = 0;
                                                if ( !empty($kues_aktif->id) ) {
                                                    $cek = DB::table('kues')
                                                            ->where('id_kues_jadwal', $kues_aktif->id)
                                                            ->where('id_mhs_reg', Sia::sessionMhs())
                                                            ->where('id_mk', $r->id_mk)
                                                            ->where('id_dosen', $r->id_dosen)
                                                            ->count();
                                                }
                                            ?>
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td align="left">{{ $r->nm_mk }}</td>
                                                <td>{{ $r->sks_mk }}</td>
                                                <td>{{ $r->kode_kls }}</td>
                                                <td align="left"><?= Sia::namaDosen($r->gelar_depan, $r->nm_dosen, $r->gelar_belakang) ?></td>
                                                <td>
                                                    @if ( empty($cek) && !empty($kues_aktif->id) )
                                                    <a href="{{ route('mhs_kues_add', [
                                                                    'dos' => $r->id_dosen, 
                                                                    'mk' => $r->id_mk, 
                                                                    'kls' => $r->kode_kls,
                                                                    'rgn' => $r->ruangan,
                                                                    'id_jdk' => $r->id,
                                                                    'kues_jadwal' => $kues_aktif->id
                                                            ]) }}"
                                                        class="btn btn-xs btn-primary">Isi Kuesioner</a>
                                                    @else
                                                        <i class="fa fa-check"></i>
                                                    @endif
                                                </td> 
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @if (count($jadwal) == 0)
                                    Belum ada data
                                @endif

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
        @if ( Session::has('success') )
            $.notific8('Berhasil menyimpan kuesioner', {
                life:5000,
                horizontalEdge:"bottom",
                theme:"success",
                heading:" Pesan "
            });
        @endif
    });
</script>
@endsection