@extends('layouts.app')

@section('title','Persetujuan Seminar')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Persetujuan Seminar
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-lg-2 col-md-4" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('jenis', this.value)">
                            <option value="all">Semua Jenis</option>
                            @foreach( Sia::jenisSeminar() as $key => $val )
                                <option value="{{ $key }}" {{ Session::get('sem.jenis') == $key ? 'selected':'' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('smt', this.value)">
                            <option value="all">Semester</option>
                            @foreach( Sia::listSemester() as $smt )
                                <option value="{{ $smt->id_smt }}" {{ Session::get('sem.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('status', this.value)">
                            <option value="all" {{ Session::get('sem.status') == 'all' ? 'selected':'' }}>Semua Status</option>
                            <option value="0" {{ Session::get('sem.status') == '0' ? 'selected':'' }}>Belum Diproses</option>
                            <option value="1" {{ Session::get('sem.status') == '1' ? 'selected':'' }}>Disetujui</option>
                            <option value="2" {{ Session::get('sem.status') == '2' ? 'selected':'' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                        <form action="" method="get" id="form-cari">
                            <div class="input-group pull-right">
                                <input type="hidden" name="pencarian" value="1">
                                <input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('sem.cari') }}">
                                <div class="input-group-btn">
                                    @if ( Session::has('sem.cari') )
                                        <button class="btn btn-danger btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
                                    @endif
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                            <thead class="custom">
                                <tr>
                                    <th width="20" class="text-center">No</th>
                                    <th>Mahasiswa</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $seminar as $sem )
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration - 1 + $seminar->firstItem()}}</td>
                                        <td>{{ $sem->nm_mhs }} - {{ $sem->nim }}</td>
                                        <td class="text-center">{{ Sia::jenisSeminar($sem->jenis) }}</td>
                                        <td class="text-center">
                                            {{ Rmt::status2($sem->setuju) }}
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-primary" href="{{ route('dsn_approv_seminar_detail',['id' => $sem->id]) }}">Lihat Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ( $seminar->total() == 0 )
                            &nbsp; Tidak ada data
                        @endif

                        @if ( $seminar->total() > 0 )
                            <div class="pull-left">
                                Jumlah data : {{ $seminar->total() }}
                            </div>
                        @endif

                        <div class="pull-right"> 
                            {{ $seminar->render() }}
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
        
        $('#reset-cari').click(function(){
            var q = $('input[name="cari"]').val();
            $('input[name="cari"]').val('');
            if ( q.length > 0 ) {
                $('#form-cari').submit();
            }
            
        });

    })

    function filter(modul, value)
    {
        window.location.href = '{{ route('dsn_approv_seminar_filter') }}?modul='+modul+'&val='+value;
    }

</script>
@endsection