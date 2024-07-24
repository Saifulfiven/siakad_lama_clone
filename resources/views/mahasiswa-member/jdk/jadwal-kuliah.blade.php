@extends('layouts.app')

@section('title','Jadwal Perkuliahan')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Jadwal Perkuliahan
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                
                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}

                <div class="row">

                    <div class="col-md-12">

                        <div class="table-responsive">
                            Jenis Jadwal :

                            <select class="form-custom" onchange="ubahJenis(this.value)">
                                <option value="1" {{ Session::get('jeniskrs_in_jdk') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                                <option value="2" {{ Session::get('jeniskrs_in_jdk') == 2 ? 'selected':'' }}>SP</option>
                            </select>

                            <a href="{{ route('mhs_jdk_cetak') }}" target="_blank" class="btn btn-primary btn-sm pull-right"><i class="fa fa-print"> CETAK</i></a>

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Nama matakuliah</th>
                                        <th>SKS</th>
                                        <th>Kelas</th>
                                        <th>Ruang</th>
                                        <th>Dosen</th>
                                    </tr>
                                </thead>
                                <tbody align="center">

                                    @foreach( $jadwal as $r )
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td align="left">{{ Rmt::hari($r->hari) }}</td>
                                            <td>
                                                {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
                                            </td>
                                            <td align="left">
                                                {{ $r->nm_mk }}
                                            </td>
                                            <td>{{ $r->sks_mk }}</td>
                                            <td>{{ $r->kode_kls }}</td>
                                            <td>{{ $r->nm_ruangan }}</td>
                                            <td align="left"><?= $r->dosen ?></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (count($jadwal) == 0)
                                Belum ada jadwal anda pada periode {{ Sia::sessionPeriode('nama') }}
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
    $(function () {

        
    });

    function ubahJenis(id)
    {
        window.location.href='{{ route('mhs_jdk') }}?ubah_jenis='+id;
    }

</script>
@endsection