@extends('layouts.app')

@section('title','Aktivitas Perkuliahan Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Aktivitas Perkuliahan Mahasiswa
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
                                            <select class="form-custom" id="ganti-nim">
                                                @foreach( $mhs_reg as $val )
                                                    <option value="{{ $val->id }}|{{ $val->nim }}" {{ Session::get('konfersi_data')[0].'|'.Session::get('konfersi_data')[1] == $val->id.'|'.$val->nim ? 'selected':'' }}>{{ $val->nim }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Nama</th><td>: {{ $mhs->nm_mhs }}</td>
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
                                        <th>Tempat, Tgl lahir</th><td>: {{ $mhs->tempat_lahir }}, {{ Rmt::formatTgl($mhs->tgl_lahir) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Agama</th><td>: {{ $mhs->nm_agama }}</td>
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
                                        <th>ID SMT</th>
                                        <th>Tahun ajaran</th>
                                        <th>SKS Smt</th>
                                        <th>IPS</th>
                                        <th>SKS Total</th>
                                        <th>IPK</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    @foreach( $aktivitas as $r )
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $r->id_smt }}</td>
                                            <td>{{ $r->nm_smt }}</td>
                                            <td>{{ $r->sks_smt }}</td>
                                            <td>{{ $r->ips }}</td>
                                            <td>{{ $r->sks_total }}</td>
                                            <td>{{ $r->ipk }}</td>
                                            <td>{{ $r->nm_stat_mhs }}</td>
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
    $(function(){

        $('#ganti-nim').change(function(){
            var data = $(this).val();
            var dataArr = data.split('|');
            window.location.href='?id_reg_pd='+dataArr[0]+'&nim='+dataArr[1];
        });

    });
</script>
@endsection