@extends('layouts.app')

@section('title','Nilai')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>NILAI</a></li>
    </ul>
@endsection

@section('content')
    <div id="overlay"></div>

    <div id="content">

        <div class="row">
                
            <div class="col-md-12">
                <section class="panel">

                    <div class="panel-body">

                        {{ Rmt::AlertError() }}
                        {{ Rmt::AlertSuccess() }}
                        
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Program Studi</th>
                                        <th>Semester</th>
                                        <th>Jenis Aktivitas</th>
                                        <th>Judul</th>
                                        <th colspan="2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    <?php $i = 1; ?>
                                    @foreach($data as $mbkm)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $mbkm->nm_prodi }}</td>
                                        <td>{{ $mbkm->nm_smt }}</td>
                                        <td>{{ $mbkm->nm_aktivitas }}</td>
                                        <td>{{ $mbkm->judul_aktivitas }}</td>
                                        <td>
                                            <a href="{{ route('detail_konversi', ['id' => $mbkm->ka_id]) }}" class="btn btn-success btn-xs" title="Lihat Nilai"><i class="fa fa-search-plus"></i></a>
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
        <!-- //content > row-->
        
    </div>
    <!-- //content-->

@endsection

@section('registerscript')
            
<script>

    $(document).ready(function(){

        $(".collapse.in").each(function(){
            $(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
        });
        
        $(".collapse").on('show.bs.collapse', function(){
            $(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
        }).on('hide.bs.collapse', function(){
            $(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
        });

        $('#nav-mini').trigger('click');

        $('#reset-cari').click(function(){
            var q = $('input[name="q"]').val();
            $('input[name="q"]').val('');
            if ( q.length > 0 ) {
                $('#form-cari').submit();
            }
            
        });

    });

    function filter(modul, value)
    {
        window.location.href='?'+modul+'='+value;
    }

</script>
@endsection