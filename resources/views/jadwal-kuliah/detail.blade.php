@extends('layouts.app')

@section('title','Detail Jadwal Perkuliahan')

@section('topMenu')
    @include('jadwal-kuliah.top-menu')
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Detail Jadwal Perkuliahan
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}
                {{ Rmt::AlertError() }}
                {{ Rmt::AlertErrors($errors) }}

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="{{ route('jdk') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                        @if ( Sia::akademik() )
                        <a href="{{ route('jdk_edit', ['id' => $r->id])}}" class="btn btn-warning btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-pencil"></i> UBAH</a>
                        <a href="{{ route('jdk_add') }}" class="btn btn-primary btn-sm pull-right" style="margin: 3px 3px" ><i class="fa fa-plus"></i> TAMBAH</a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th width="160px">Semester</th>
                                        <td width="400px">: {{ $r->nm_smt }}</td>
                                        <th width="160px">Nama Kelas</th>
                                        <td>: {{ $r->kode_kls }}</td>
                                    </tr>
                                    <tr>
                                        <th>Hari/Jam</th>
                                        <td>: {{ Rmt::hari($r->hari) }} - {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}</td>
                                        <th>Ruangan</th>
                                        <td>: {{ $r->nm_ruangan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>: {{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                        <th>Kapasitas Kelas</th>
                                        <td>: {{ $r->kapasitas_kls }}</td>
                                    </tr>
                                    <tr>
                                        <th>Matakuliah</th>
                                        <td>: {{ $r->kode_mk }} - {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" data-provide="tabdrop">
                                <li{{ Session::has('tab_dosen') || (!Session::has('tab_peserta') && !Session::has('tab_pertemuan')) ? " class=active" : '' }}><a href="#dosen" data-toggle="tab">Dosen Mengajar</a></li>
                                <li{{ Session::has('tab_peserta') ? " class=active" : '' }}><a href="#peserta" data-toggle="tab">Peserta</a></li>
                                @if ( $r->id_prodi == 61101 )
                                    <li{{ Session::has('tab_pertemuan') ? " class=active" : '' }}><a href="#pertemuan" data-toggle="tab">Jadwal Pertemuan</a></li>
                                @endif
                            </ul>
                            <div class="tab-content">
                            
                                <div class="tab-pane fade {{ Session::has('tab_dosen') || (!Session::has('tab_peserta') && !Session::has('tab_pertemuan')) ? 'in active' : '' }}" id="dosen">
                                    <div class="table-responsive">

                                        @if ( Sia::adminOrAkademik() && Sia::canAction($r->id_smt) )
                                            <a href="javascript::void(0)" data-toggle="modal" data-target="#modal-tambah" data-backdrop="static" data-keyboard="false"  class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH DOSEN MENGAJAR</a>
                                        @endif

                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                            <thead class="custom">
                                                <tr>
                                                    <th width="20px" rowspan="2">No.</th>
                                                    <th rowspan="2">NIDN</th>
                                                    <th rowspan="2">Nama Dosen</th>
                                                    <th rowspan="2">SKS</th>
                                                    <th rowspan="2">Dosen Ke</th>
                                                    <th colspan="2">Pertemuan</th>
                                                    <th rowspan="2">Aksi</th>
                                                </tr>
                                                <tr>
                                                    <th width="80px">Rencana</th>
                                                    <th width="80px">Terealisasi</th>
                                                </tr>
                                            </thead>
                                            <tbody align="center">
                                                @foreach( $dosen as $d )
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td align="left">{{ $d->nidn }}</td>
                                                        <td align="left">{{ $d->gelar_depan.' '.$d->nm_dosen.', '.$d->gelar_belakang }}</td>
                                                        <td>{{ $r->sks_mk }}</td>
                                                        <td>{{ $d->dosen_ke }}</td>
                                                        <td>{{ $d->jml_tm }}</td>
                                                        <td>{{ $d->jml_real }}</td>
                                                        <td>
                                                            @if ( Sia::adminOrAkademik() )
                                                                <span class="tooltip-area">
                                                                    <a href="javascript::void(0)" class="btn btn-warning btn-xs btn-edit" title="Ubah"
                                                                        data-id="{{ $d->id }}"
                                                                        data-iddosen="{{ $d->id_dosen }}"
                                                                        data-nama="{{ $d->nm_dosen }}"
                                                                        data-tatapmuka="{{ $d->jml_tm }}"
                                                                        data-dosenke="{{ $d->dosen_ke }}"
                                                                        data-realtm="{{ $d->jml_real }}"><i class="fa fa-pencil"></i></a>
                                                                    <a href="{{ route('jdk_dosen_delete',['id_jdk' => $d->id_jdk, 'id_dosen' => $d->id_dosen]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @if ( count($dosen) == 0 )
                                            <strong>Dosen belum diisi pada kelas ini</strong>
                                        @endif
                                    </div>

                                </div>
                                <!-- //dosen -->

                                <!-- //peserta -->
                                <div class="tab-pane fade {{ Session::has('tab_peserta') ? 'in active' : '' }}" id="peserta">
                                    <div class="table-responsive">

                                        <!-- Jika masih masa krsan dan mk != praktek -->
                                        @if ( Rmt::dateBetween($jdw_akademik->awal_krs, $jdw_akademik->akhir_krs) || $r->sks_tm == 0  )

                                            @if ( Sia::canAction($r->id_smt) )
                                                <div class="alert alert-info">
                                                    <form action="{{ route('jdk_mhs_store') }}" id="form-peserta" method="post">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="id_jdk" value="{{ $r->id }}">
                                                        <input type="hidden" name="id_mk" value="{{ $r->id_mk }}">
                                                        <input type="hidden" name="sks" value="{{ $r->sks_mk }}">
                                                        <input type="hidden" name="semester_mk" value="{{ $r->smt }}">
                                                        <input type="hidden" name="hari" value="{{ $r->hari }}">
                                                        <input type="hidden" name="jam" value="{{ $r->id_jam }}">
                                                        <div class="table-responsive">
                                                            <table border="0" width="100%" style="min-width: 750px">
                                                                <tr>
                                                                    <td width="100px" align="left"><b>NIM/NAMA : </b></td>
                                                                    <td width="300px">
                                                                        <div style="position: relative;">
                                                                            <div class="input-icon right"> 
                                                                                <span id="spinner-autocomplete-mhs" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                                <input type="text" class="form-control" required="" id="autocomplete-mhs" name="nama_mhs" style="max-width: 290px">
                                                                                <input type="hidden" id="id-mhs" name="mahasiswa">
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td width="120">
                                                                        <button id="btn-add-peserta" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> TAMBAHKAN </button>
                                                                    </td>
                                                                    <td></td>
                                                                    <td width="100">
                                                                        <a href="javascript:void(0)" 
                                                                            onclick="addFromKrs(
                                                                                '{{ $r->id }}',
                                                                                '{{ $r->kode_kls }}',
                                                                                '{{ $r->id_mkur }}',
                                                                            )" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Dari KRS</a>
                                                                    </td>
                                                                    <td width="150">
                                                                        <a href="{{ route('jdk_mhs_add', ['jdk' => $r->id, 'pr' => $r->id_prodi,'ang' => date('Y')-1]) }}" class="btn btn-primary btn-sm">TAMBAH KOLEKTIF PESERTA</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                                    </form>
                                                </div>
                                            @endif

                                        @else
                                            <div class="alert bg-darkorange">
                                                Masa KRS telah berakhir/belum terbuka. Hanya bisa menambah SKRIPSI/TESIS atau magang
                                            </div>
                                        @endif

                                        <a href="{{ route('jdk_cetak_peserta', ['id' => $r->id]) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Cetak Peserta</a>
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                            <thead class="custom">
                                                <tr>
                                                    <th width="20px">No.</th>
                                                    <th>NIM</th>
                                                    <th>Nama</th>
                                                    <th>L/P</th>
                                                    <th>Prodi</th>
                                                    <th>Angkatan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody align="center">
                                                @foreach( $peserta as $ps )
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td align="left">{{ $ps->nim }}</td>
                                                        <td align="left">{{ $ps->nm_mhs }}</td>
                                                        <td>{{ $ps->jenkel }}</td>
                                                        <td>{{ $ps->jenjang }} - {{ $ps->nm_prodi }}</td>
                                                        <td>{{ substr($ps->semester_mulai, 0, 4) }}</td>
                                                        <td>
                                                            @if ( Sia::adminOrAkademik() )
                                                                <a href="{{ route('jdk_mhs_delete',['id' => $ps->id_nilai]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @if ( count($peserta) == 0 )
                                            <strong>Belum ada peserta kelas</strong>
                                        @endif
                                    </div>
                                </div>
                                <!-- //peserta -->

                                @if ( $r->id_prodi == 61101 )
                                    <!-- //peserta -->
                                    <div class="tab-pane fade {{ Session::has('tab_pertemuan') ? 'in active' : '' }}" id="pertemuan">
                                        <div class="table-responsive">
                                        <?php
                                            $pertemuan = DB::table('jadwal_pertemuan_s2')->where('id_jdk', $r->id)->orderBy('pertemuan_ke')->get();
                                        ?>
                                            <button type="button" data-target="#modal-add-pertemuan" data-toggle="modal" class="btn btn-primary btn-sm">+ Tambah Pertemuan</button>
                                            
                                            <table cellpadding="0" width="420px" cellspacing="0" border="0" class="table-bordered table-striped table-hover">
                                                <thead class="custom">
                                                    <tr>
                                                        <th width="110">Pertemuan Ke</th>
                                                        <th>Tanggal</th>
                                                        <th>Jam</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody align="center">
                                                    @foreach( $pertemuan as $per )
                                                        <tr>
                                                            <td>{{ $per->pertemuan_ke }}</td>
                                                            <td>{{ Rmt::tgl_indo($per->tgl) }}</td>
                                                            <td>{{ $per->jam }}</td>
                                                            <td>
                                                                @if ( Sia::adminOrAkademik() )
                                                                    <a href="javascript:;" onclick="editPertemuan('{{ $per->id }}', '{{ $per->pertemuan_ke }}', '{{ $per->tgl }}', '{{ $per->jam }}')" class="btn btn-warning btn-xs" title="Ubah"><i class="fa fa-pencil"></i></a>
                                                                    <a href="{{ route('jdk_delete_pertemuan',['id' => $per->id]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                            </div>
                            <!-- //tab-content -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>

    <div id="modal-tambah" class="modal fade" style="top:30%" data-width="600" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Dosen Mengajar</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('jdk_dosen_store') }}" id="form-dosen" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_jdk" value="{{ $r->id }}">
                <input type="hidden" name="hari" value="{{ $r->hari }}">
                <input type="hidden" name="id_jam" value="{{ $r->id_jam }}">

                <div class="table-responsive">

                    <div class="ajax-message"></div>

                    <table border="0" class="table-hover table-form" width="100%">
                        <tr>
                            <td width="150px">Nama Dosen</td>
                            <td>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-dosen" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" id="autocomplete-dosen" required="">
                                        <input type="hidden" id="id-dosen" name="dosen">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Jumlah rencana tatap muka</td>
                            <td>
                                <input type="number" maxlength="2" size="2" class="form-control mw-1" name="tatap_muka" value="14">
                            </td>
                        </tr>
                        <tr>
                            <td>Jumlah realisasi tatap muka</td>
                            <td>
                                <input type="number" maxlength="2" size="2" class="form-control mw-1" name="real_tm" value="14">
                            </td>
                        </tr>
                        <tr>
                            <td>Dosen Ke</td>
                            <td>
                                <input type="number" maxlength="2" size="2" class="form-control mw-1" name="dosen_ke" required="">
                            </td>
                        </tr>
                    </table>
                </div>
                <button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
            </form>
        </div>
    </div>

    <div id="modal-edit" class="modal fade" style="top:30%" data-width="600" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Dosen Mengajar</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('jdk_dosen_update') }}" id="form-dosen-update" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_jdk" value="{{ $r->id }}">
                <input type="hidden" name="hari" value="{{ $r->hari }}">
                <input type="hidden" name="id_jam" value="{{ $r->id_jam }}">
                <input type="hidden" name="id" id="id-dm">

                <div class="table-responsive">

                    <div class="ajax-message"></div>

                    <table border="0" class="table-hover table-form" width="100%">
                        <tr>
                            <td width="150px">Nama Dosen</td>
                            <td>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-dosen-update" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" id="autocomplete-dosen-update" required="">
                                        <input type="hidden" id="id-dosen-update" name="dosen">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Jumlah rencana tatap muka</td>
                            <td>
                                <input type="number" maxlength="2" size="2" id="tatap-muka" class="form-control mw-1" name="tatap_muka" value="14">
                            </td>
                        </tr>
                        <tr>
                            <td>Jumlah realisasi tatap muka</td>
                            <td>
                                <input type="number" maxlength="2" size="2" id="real-tm" class="form-control mw-1" name="real_tm" value="14">
                            </td>
                        </tr>
                        <tr>
                            <td>Dosen Ke</td>
                            <td>
                                <input type="number" maxlength="2" size="2" class="form-control mw-1" name="dosen_ke" id="dosen-ke">
                            </td>
                        </tr>
                    </table>
                </div>
                <button type="submit" id="btn-update" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
            </form>
        </div>
    </div>


    <div id="modal-add-pertemuan" class="modal fade" style="top:30%" data-width="400" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Tambah Pertemuan</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('jdk_store_pertemuan') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_jdk" value="{{ $r->id }}">

                <div class="table-responsive">

                    <div class="ajax-message"></div>

                    <table border="0" class="table-hover table-form" width="100%">
                        <tr>
                            <td width="150px">Pertemuan Ke</td>
                            <td>
                                <select class="form-control mw-1" name="pertemuan">
                                    @for( $i = 1; $i <= 16; $i++ )
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Tangal Pertemuan</td>
                            <td>
                                <input type="date" name="tanggal" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <td>Jam : </td>
                            <td>
                                <select class="form-control mw-3" name="jam">
                                    <?php $jamkul = Sia::jamKuliah(61101); ?>
                                    <?php foreach( $jamkul as $j ) { ?>
                                        <option value="<?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?>"><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <br>
                <button type="submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
            </form>
        </div>
    </div>

    <div id="modal-edit-pertemuan" class="modal fade" style="top:30%" data-width="400" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Ubah Pertemuan</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('jdk_update_pertemuan') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" id="id-pertemuan">

                <div class="table-responsive">

                    <div class="ajax-message"></div>

                    <table border="0" class="table-hover table-form" width="100%">
                        <tr>
                            <td width="150px">Pertemuan Ke</td>
                            <td> : <span style="color: #000" class="pertemuan-ke"></span></td>
                        </tr>
                        <tr>
                            <td>Tangal Pertemuan</td>
                            <td>
                                <input type="date" name="tanggal" class="form-control tgl-pertemuan">
                            </td>
                        </tr>
                        <tr>
                            <td>Jam : </td>
                            <td>
                                <select class="form-control mw-3" name="jam" id="jam-pertemuan">
                                    <?php $jamkul = Sia::jamKuliah(61101); ?>
                                    <?php foreach( $jamkul as $j ) { 
                                        $jam = substr($j->jam_masuk,0,5).' - '.substr($j->jam_keluar,0,5) ?>
                                        <option value="<?= $jam ?>"><?= $jam ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <br>
                <button type="submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
            </form>
        </div>
    </div>


    <div id="modal-from-krs" class="modal fade md-stickTop" tabindex="-1" data-width="500">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Tambahkan dari KRS</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <div class="ajax-message"></div>
            <form action="{{ route('jdk_mhs_store_krs') }}" id="form-add-mahasiswa" method="post">
                <div id="add-peserta-form">

                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> </i> TUTUP</button>
        </div>
        <!-- //modal-body-->
    </div>


@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>
    $(function () {
        'use strict';

        $('#nav-mini').trigger('click');

        /* Jadwal and dosen mengajar */
            $(document).on('click', '.btn-edit', function(){
                var el = $(this);
                $('#modal-edit').modal({backdrop: 'static', keyboard: false});
                $('#id-dosen-update').val(el.data('iddosen'));
                $('#autocomplete-dosen-update').val(el.data('nama'));
                $('#tatap-muka').val(el.data('tatapmuka'));
                $('#real-tm').val(el.data('realtm'));
                $('#id-dm').val(el.data('id'));
                $('#dosen-ke').val(el.data('dosenke'));
            });

                $('#autocomplete-dosen').autocomplete({
                    serviceUrl: '{{ route('jdk_dosen') }}',
                    lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                        var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                        return re.test(suggestion.value);
                    },
                    onSearchStart: function(data) {
                        $('#spinner-autocomplete-dosen').show();
                    },
                    onSearchComplete: function(data) {
                        $('#spinner-autocomplete-dosen').hide();
                    },
                    onSelect: function(suggestion) {
                        $('#id-dosen').val(suggestion.data);
                    },
                    onInvalidateSelection: function() {
                    }
                });

                $('#autocomplete-dosen-update').autocomplete({
                    serviceUrl: '{{ route('jdk_dosen') }}',
                    lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                        var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                        return re.test(suggestion.value);
                    },
                    onSearchStart: function(data) {
                        $('#spinner-autocomplete-dosen-update').show();
                    },
                    onSearchComplete: function(data) {
                        $('#spinner-autocomplete-dosen-update').hide();
                    },
                    onSelect: function(suggestion) {
                        $('#id-dosen-update').val(suggestion.data);
                    },
                    onInvalidateSelection: function() {
                    }
                });

                var options = {
                    beforeSend: function() 
                    {
                        $('#overlay').show();
                        $("#btn-submit").attr('disabled','');
                        $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                    },
                    success:function(data, status, message) {
                        if ( data.error == 1 ) {
                            showMessage(data.msg);
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function(data, status, message)
                    {
                        var respon = parseObj(data.responseJSON);
                        var pesan = '';
                        for ( var i = 0; i < respon.length; i++ ){
                            pesan += "- "+respon[i]+"<br>";
                        }
                        if ( pesan == '' ) {
                            pesan = message;
                        }
                        showMessage(pesan);
                    }
                }; 

                $('#form-dosen').ajaxForm(options);

                var options = {
                    beforeSend: function() 
                    {
                        $('#overlay').show();
                        $("#btn-update").attr('disabled','');
                        $("#btn-update").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                    },
                    success:function(data, status, message) {
                        if ( data.error == 1 ) {
                            showMessage(data.msg);
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function(data, status, message)
                    {
                        var respon = parseObj(data.responseJSON);
                        var pesan = '';
                        for ( var i = 0; i < respon.length; i++ ){
                            pesan += "- "+respon[i]+"<br>";
                        }
                        if ( pesan == '' ) {
                            pesan = message;
                        }
                        showMessage(pesan);
                    }
                }; 

                $('#form-dosen-update').ajaxForm(options);


                /* Form add peserta from krs */
                var options2 = {
                    beforeSend: function() 
                    {
                        $("#btn-submit-peserta").attr('disabled','');
                        $("#btn-submit-peserta").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                    },
                    success:function(data, status, message) {
                        if ( data.error == 1 ) {
                            showMessage(data.msg);
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function(data, status, message)
                    {
                        var respon = parseObj(data.responseJSON);
                        var pesan = '';
                        for ( var i = 0; i < respon.length; i++ ){
                            pesan += "- "+respon[i]+"<br>";
                        }
                        if ( pesan == '' ) {
                            pesan = message;
                        }
                        showMessage(pesan);
                    }
                }; 

                $('#form-add-mahasiswa').ajaxForm(options2);

        /* Peserta kelas */
            $('#autocomplete-mhs').autocomplete({
                serviceUrl: '{{ route('jdk_mhs') }}',
                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                },
                onSearchStart: function(data) {
                    $('#spinner-autocomplete-mhs').show();
                },
                onSearchComplete: function(data) {
                    $('#spinner-autocomplete-mhs').hide();
                },
                onSelect: function(suggestion) {
                    $('#id-mhs').val(suggestion.data);
                },
                onInvalidateSelection: function() {
                }
            });

                var options = {
                    beforeSend: function() 
                    {
                        $('#overlay').show();
                        $("#btn-add-peserta").attr('disabled','');
                        $("#btn-add-peserta").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                    },
                    success:function(data, status, message) {
                        if ( data.error == 1 ) {
                            showMessage(data.msg);
                        } else {
                            window.location.reload();
                        }
                    },
                    error: function(data, status, message)
                    {
                        var respon = parseObj(data.responseJSON);
                        var pesan = '';
                        for ( var i = 0; i < respon.length; i++ ){
                            pesan += "- "+respon[i]+"<br>";
                        }
                        if ( pesan == '' ) {
                            pesan = message;
                        }
                        showMessage(pesan);
                    }
                }; 

                $('#form-peserta').ajaxForm(options);

    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-submit-peserta').removeAttr('disabled');
        $('#btn-submit-peserta').html('<i class="fa fa-floppy-o"></i> SIMPAN');

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
        $('#btn-update').removeAttr('disabled');
        $('#btn-update').html('<i class="fa fa-floppy-o"></i> SIMPAN');
        $('#btn-add-peserta').removeAttr('disabled');
        $('#btn-add-peserta').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function editPertemuan(id,pertemuan, tgl, jam)
    {
        $('#id-pertemuan').val(id);
        $('.pertemuan-ke').html(pertemuan);
        $('.tgl-pertemuan').val(tgl);
        $('#jam-pertemuan option[value="'+jam+'"]').attr("selected",true);
        
        $('#modal-edit-pertemuan').modal('show');
    }

    function addFromKrs(id_jdk, kelas, id_mkur)
    {
        $('#ajax-message').html('');
        $('#add-peserta-form').html('<center><i class="fa fa-spinner fa-spin"></i><br>Sedang mengambil data</center>');
        $('#modal-from-krs').modal({backdrop: 'static', keyboard: false});
        $('#modal-from-krs').modal('show');

        $.ajax({
            url: '{{ route('jdk_mhs_add_krs') }}',
            data: { id_jdk: id_jdk, kelas: kelas, id_mkur: id_mkur },
            success: function(result){
                $('#add-peserta-form').html(result);
            },
            error: function(data,status,msg){
                alert(msg);
            }
        });
    }

</script>
@endsection