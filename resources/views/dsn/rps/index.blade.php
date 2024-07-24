@extends('layouts.app')

@section('title','RPS')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              RPS
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="tabbable">
                    <ul class="nav nav-tabs" data-provide="tabdrop">
                        <li class="{{ Request::get('prodi') == 61101 ? 'active':'' }}">
                        	<a href="?prodi=61101">Magister Manajemen (S2)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 61112 ? 'active':'' }}">
                        	<a href="?prodi=61112">Magister Keuangan Publik (S2)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 61113 ? 'active':'' }}">
                        	<a href="?prodi=61113">Magister Manajemen dan Kewirausahaan (S2)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 61201 ? 'active':'' }}">
                        	<a href="?prodi=61201">Manajemen (S1)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 62201 ? 'active':'' }}">
                        	<a href="?prodi=62201">Akuntansi (S1)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 59201 ? 'active':'' }}">
                        	<a href="?prodi=59201">STI (S1)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 54244 ? 'active':'' }}">
                        	<a href="?prodi=54244">Teknologi Hasil Perikanan (S1)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 31201 ? 'active':'' }}">
                        	<a href="?prodi=31201">Teknik Pertambangan (S1)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 26201 ? 'active':'' }}">
                        	<a href="?prodi=26201">Teknik Industri (S1)</a>
                        </li>
                        <li class="{{ Request::get('prodi') == 83207 ? 'active':'' }}">
                        	<a href="?prodi=83207">Teknik PTI (S1)</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                    
                        <div class="tab-pane fade in active">
                           
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="tabel-manajemen">
                                <thead class="custom">
                                    <tr>
                                        <th>No.</th>
                                        <th>Matakuliah</th>
                                        <th width="80">Tool</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $rps as $r )
                                        <?php $link = str_replace('preview','view', $r->link) ?>
                                        <tr>
                                            <td style="text-align:center">{{ $loop->iteration }}</td>
                                            <td>{{ $r->judul }}</td>
                                            <td>
                                                <a href="{{ $link }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-search-plus"></i> BUKA</a>
                                            </td>
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
@endsection

@section('registerscript')
<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>

<script>
    $(function () {
        'use strict';

        $('#tabel-manajemen').dataTable();
        $('#tabel-akuntansi').dataTable();
    });
</script>
@endsection