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
                <button type="button" class="btn btn-primary btn-sm pull-right" style="margin: 3px 3px" data-toggle="modal" data-target="#addNilai">
                    <i class="fa fa-plus"></i>
                    Nilai
                </button>
                <a href="{{ route('konversi_mbkm') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i>Daftar</a>              
            </div>
            
            <div class="col-md">

                <div class="row">

                    @foreach ($data['aktivitas'] as $a)
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th width="130px">Judul</th><td>: {{ $a->judul_aktivitas }}</td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Semester</th><td>: {{ $a->nm_smt }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Aktivitas</th><td>: {{ $a->nm_aktivitas }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach

                    @foreach ($data['mhs'] as $m)
                    <div class="col-md-6">
                       <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="120px">Nama Mahasiswa</th><td>: {{ $m->nm_mhs }}</td>
                                    </tr>
                                    <tr>
                                        <th>NIM</th><td>: {{ $m->nim }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                    
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
                            <th rowspan="2" width="75px">Aksi</th>
                        </tr>
                        <tr>
                            <th>Angka</th>
                            <th>Huruf</th>
                            <th>Index</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        <?php $i = 1; ?>
                        @foreach ($data['nilai'] as $n)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $n->nm_mk }}</td>
                            <td>{{ $n->sks_mk }}</td>
                            <td>{{ number_format($n->nil_angka, 2) }}</td>
                            <td>{{ $n->nil_huruf }}</td>
                            <td>{{ number_format($n->nil_indeks, 2) }}</td>
                            <td>
                                {{-- <a href="" class="btn btn-warning btn-xs" title="Lihat Nilai"><i class="fa fa-pencil"></i></a> --}}
                                <a href="{{ route('deleteNilMbkm', ['id' => $n->id]) }}" class="btn btn-danger btn-xs" title="Lihat Nilai"><i class="fa fa-times"></i></a>
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

{{-- {{ dd($data['aktivitas'][0]->id_smt) }} --}}

<div class="modal fade" id="addNilai" tabindex="-1" role="dialog" aria-labelledby="addNilaiLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addNilaiLabel">Add Nilai MBKM</h4>
            </div>
            <form action="{{ route('insertNilaiMBKM') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_aktivitas" value="{{ $data['aktivitas'][0]->id_aktivitas }}">
                <input type="hidden" name="id_smt" value="{{ $data['aktivitas'][0]->id_smt }}">
                <input type="hidden" name="id_mhs_reg" value="{{ $data['mhs'][0]->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nmMk">Mata Kuliah</label>
                        <select name="nm_mk" id="nmMk" class="form-control">
                            <option value="-">Matakuliah</option>
                            @foreach ($data['mk'] as $mk)
                                {{-- {{ dd($mk->id) }} --}}
                                <option value="{{ $mk->id }}">{{ $mk->nm_mk }} | {{ $mk->sks_mk }} SKS</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nil_angka">Nilai</label>
                        <input type="number" name="nil_angka" class="form-control" id="nil_angka">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
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