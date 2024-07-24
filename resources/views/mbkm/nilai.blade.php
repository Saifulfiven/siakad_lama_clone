@extends('layouts.app')

@section('title','Detal Konversi MBKM')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel">
        <header class="panel-heading col-md-12">
            Detail Konversi Nilai MBKM
        </header>

        <div class="panel-body" style="padding: 3px 3px">

            <div class="col-md-12">
                <a href="{{ route('konversi_mbkm') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i>Daftar</a>        
            </div>
            
            @foreach ($data as $d)
            <div class="col-md">

                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th width="130px">Program Studi</th><td>: {{ $d->jenjang }} {{ $d->nm_prodi }}</td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Semester</th><td>: {{ $d->nm_smt }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Anggota</th>
                                        <td>: 
                                            @if($d->jenis_anggota == 0)
                                                -
                                            @elseif($d->jenis_anggota == 1)
                                                Personal
                                            @elseif($d->jenis_anggota == 2)
                                                Kelompok
                                            @endif
                                        </td>
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
                                        <th width="120px">Jenis Aktivitas</th><td>: {{ $d->nm_aktivitas }}</td>
                                    </tr>
                                    <tr>
                                        <th>Judul</th><td>: {{ $d->judul_aktivitas }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th><td>: {{ $d->keterangan }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>

            </div>
            @endforeach

            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
                    <thead class="custom">
                        <tr>
                            <th width="20px">No.</th>
                            <th>NIM</th>
                            <th>Nama Peserta</th>
                            <th>Jenis</th>
                            <th colspan="2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <?php $i = 1; ?>
                        @foreach($mhs as $m)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $m->nim }}</td>
                            <td>{{ $m->nm_mhs }}</td>
                            <td>
                                @if ($m->jenis_peran == 1)
                                    Ketua
                                @elseif ($m->jenis_peran == 2)
                                    Anggota
                                @elseif ($m->jenis_peran == 3)
                                    Personal
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('show_nilai_mbkm', ['id' => $m->id_mhs_reg]) }}" class="btn btn-success btn-xs" title="Lihat Nilai"><i class="fa fa-search-plus"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pull-right"> 
                    
                </div>

            </div>

        </div>

    </section>

    </div>
  </div>
</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>

<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>
@endsection