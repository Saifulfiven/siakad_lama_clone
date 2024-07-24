@extends('layouts.app')

@section('title','Persetujuan Olah data/Validasi')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Persetujuan Olah data/Validasi
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">

                {{ Rmt::AlertError() }}

                <div class="row">
                    <div class="col-lg-2" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('smt', this.value)">
                            <option value="all">Semester</option>
                            @foreach( Sia::listSemester() as $smt )
                                <option value="{{ $smt->id_smt }}" {{ Session::get('ndc.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control input-sm" onchange="filter('prodi', this.value)">
                            <option value="all">Semua Prodi</option>
                            @foreach( Sia::listProdi() as $pr )
                                <option value="{{ $pr->id_prodi }}" {{ Session::get('ndc.prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('status', this.value)">
                            <option value="all" {{ Session::get('ndc.status') == 'all' ? 'selected':'' }}>Semua Status</option>
                            <option value="0" {{ Session::get('ndc.status') == '0' ? 'selected':'' }}>Menunggu Persetujuan</option>
                            <option value="1" {{ Session::get('ndc.status') == '1' ? 'selected':'' }}>Disetujui</option>
                            <!-- <option value="2" {{ Session::get('ndc.status') == '2' ? 'selected':'' }}>Ditolak</option> -->
                        </select>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                        <form action="" method="get" id="form-cari">
                            <div class="input-group pull-right">
                                <input type="hidden" name="pencarian" value="1">
                                <input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('ndc.cari') }}">
                                <div class="input-group-btn">
                                    @if ( Session::has('ndc.cari') )
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
                                    <th>Prodi</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $seminar as $sem )
                                    <?php $nm_mhs = $sem->nm_mhs .' - '.$sem->nim; ?>
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration - 1 + $seminar->firstItem()}}</td>
                                        <td>{{ $nm_mhs }}</td>
                                        <td class="text-center">{{ $sem->jenjang }} {{ $sem->nm_prodi }}</td>
                                        <td class="text-center">{{ Sia::jenisSeminar($sem->jenis) }}</td>
                                        <td class="text-center">
                                            
                                            @if ( $sem->validasi_ndc == '0' )
                                                <i class="fa fa-refresh"></i> Menunggu Persetujuan
                                            @else
                                                <i class="fa fa-check-square" style="color: green"></i> Disetujui
                                            @endif

                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" onclick="detail('{{ $sem->id }}', '{{ $nm_mhs }}')">Lihat Detail</button>
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


<div id="modal-detail" class="modal fade md-stickTop" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 id="mhs"></h4>
    </div>

    <div id="modal-konten">
    
    </div>

</div>

<div id="modal-error" class="modal fade" tabindex="-1" style="top:30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Terjadi kesalahan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="ajax-message"></div>
        <hr>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
        </center>
    </div>
    <!-- //modal-body-->
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

        @if ( Session::has('success') )
            showSuccess('{{ Session::get('success') }}');
        @endif
    })

    function detail(id_seminar, mhs)
    {
        $('#mhs').html(mhs);
        $('#modal-detail').modal('show');
        $('#modal-konten').html('<br><br><h2><center><i class="fa fa-spinner fa-spin"></i></center></h2><br><br>');
        $.ajax({
            url: '{{ route('val_ndc_detail') }}',
            data: { id_seminar: id_seminar },
            success: function(data){
                $('#modal-konten').html(data);
            },
            error: function(err,data,msg)
            {
                $('#modal-konten').html('<br><br><center><p style="color: red">'+msg+'</p></center><br><br>');
            }
        });
    }

    function filter(modul, value)
    {
        window.location.href = '{{ route('val_ndc_filter') }}?modul='+modul+'&val='+value;
    }

</script>
@endsection