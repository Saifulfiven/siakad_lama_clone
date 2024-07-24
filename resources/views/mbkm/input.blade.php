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
            
            <div class="col-md">

                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th width="130px">Judul</th><td>: </td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Semester</th><td>: </td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Aktivitas</th><td>: </td>
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
                                        <th width="120px">Nama Mahasiswa</th><td>: </td>
                                    </tr>
                                    <tr>
                                        <th>NIM</th><td>: </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>

            </div>

            <div class="table-responsive">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="table-data">
                    <thead class="custom">
                        <tr>
                            <th width="20px" rowspan="2">No.</th>
                            <th rowspan="2">Mata Kuliah</th>
                            <th rowspan="2">SKS</th>
                            <th colspan="3">Nilai</th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th>Angka</th>
                            <th>Huruf</th>
                            <th>Index</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <?php $i = 1; ?>
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="" class="btn btn-success btn-xs" title="Lihat Nilai"><i class="fa fa-search-plus"></i></a>
                            </td>
                        </tr>
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