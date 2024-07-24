@extends('layouts.app')

@section('title','Jadwal Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Jadwal Perkuliahan
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">
            
            @include('mahasiswa.link-cepat')

            <div class="col-md-9">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                    </div>
                </div>
                
                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}

                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="130px">NIM</th>
                                        <td>: {{ $mhs->nim }} </td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Periode</th><td>: {{ Sia::sessionPeriode('nama') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Jadwal</th>
                                        <td>:
                                            <select class="form-custom" onchange="ubahJenis(this.value)">
                                                <option value="1" {{ Session::get('jeniskrs_in_jdk') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                                                <option value="2" {{ Session::get('jeniskrs_in_jdk') == 2 ? 'selected':'' }}>SP</option>
                                            </select>
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
                                        <th>Nama</th><td>: {{ $mhs->nm_mhs }}</td>
                                    </tr>
                                    <tr>
                                        <th>Angkatan</th><td>: {{ substr($mhs->nim,0,4) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class="table-responsive">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Waktu</th>
                                        <th>Nama matakuliah</th>
                                        <th>Kelas / Ruang</th>
                                        <th>Dosen</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    {{-- @php
                                        dd($jadwal);
                                    @endphp --}}
                                    @foreach( $jadwal as $r )
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td align="center">
                                                {{ empty($r->hari) ? '-': Rmt::hari($r->hari) }}<br>
                                                    {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
                                            </td>
                                            <td align="left">
                                                {{ $r->kode_mk }} <br>
                                                {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)
                                            </td>
                                            <td>{{ $r->kode_kls }}<br>{{ $r->nm_ruangan }}</td>
                                            <td align="left"><?= $r->dosen ?></td>
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
</div>

@endsection

@section('registerscript')
<script>
    $(function () {

        $('#nav-mini').trigger('click');
        
    });

    function ubahJenis(id)
    {
        window.location.href='{{ route('mahasiswa_jdk') }}/{{ $mhs->id }}?ubah_jenis='+id;
    }

</script>
@endsection