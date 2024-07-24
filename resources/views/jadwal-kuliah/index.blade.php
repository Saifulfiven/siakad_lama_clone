@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')

@section('heading')
    <style type="text/css">
        .panel-title {
            font-size: 12px;
        }

        .panel-group {
            margin-bottom: 2px;
        }

        .panel-group .panel-heading {
            padding: 0 5px;
        }

        .panel-group .panel-body {
            padding: 3px 3px 3px 15px !important;
            max-height: 200px !important;
            overflow-y: scroll !important;
        }

        a[data-toggle="collapse"] {
            color: #222;
        }
    </style>
@endsection

@section('topMenu')
    @include('jadwal-kuliah.top-menu')
@endsection

@section('content')
    <div id="overlay"></div>
    @php
        // dd($jadwal);
    @endphp
    <div id="content">

        <div class="row">

            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        Jadwal Perkuliahan
                        <div class="pull-right">
                            <a href="{{ route('dosen_mengajar') }}?ta={{ Sia::sessionPeriode() }}&jenis=1"
                                class="btn btn-theme btn-xs">Daftar Dosen Mengajar</a>
                        </div>
                    </header>

                    <div class="panel-body">

                        <div class="col-md-2 col-sm-2" style="padding-left: 0">
                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0"
                                    class="table table-bordered table-striped">
                                    <thead class="custom">
                                        <tr>
                                            <th>
                                                FILTER
                                                <span class="tooltip-area pull-right">
                                                    <?php if ( Session::has('jdk_prodi') || Session::has('jdk_search') || Session::has('jdk_smt') || Session::has('jdk_ket')) { ?>
                                                    <a href="{{ route('jdk_filter') }}" class="btn btn-warning btn-xs"
                                                        title="Hapus&nbsp;Filter"><i class="fa fa-filter"></i></a>
                                                    <?php } ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>

                                                <!-- Filer -->

                                                <div class="panel-group" id="accordion">
                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion"
                                                                    href="#programStudi"><span
                                                                        class="glyphicon glyphicon-plus"></span> Program
                                                                    Studi</a>
                                                            </h4>
                                                        </div>
                                                        <div id="programStudi"
                                                            class="panel-collapse collapse {{ Session::has('jdk_prodi') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::listProdi() as $r)
                                                                    <a href="javascript:void(0)"
                                                                        id="prodi-{{ $r->id_prodi }}"
                                                                        onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }}
                                                                        {{ $r->nm_prodi }}</a>
                                                                    <?php
									                    	if ( Session::has('jdk_prodi') && in_array($r->id_prodi,Session::get('jdk_prodi')) ) { ?>
                                                                    <i class="filter fa fa-filter"></i>
                                                                    <?php } ?>
                                                                    <br>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion"
                                                                    href="#smt"><span
                                                                        class="glyphicon glyphicon-plus"></span>
                                                                    Semester</a>
                                                            </h4>
                                                        </div>
                                                        <div id="smt"
                                                            class="panel-collapse collapse {{ Session::has('jdk_smt') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::listSemester() as $smt)
                                                                    <a href="javascript:void(0)"
                                                                        id="smt-{{ $smt->id_smt }}"
                                                                        onclick="filter({{ $smt->id_smt }},'smt')">{{ $smt->nm_smt }}</a>
                                                                    <?php
									                    	if ( Session::has('jdk_smt') && in_array($smt->id_smt,Session::get('jdk_smt')) ) { ?>
                                                                    <i class="filter fa fa-filter"></i>
                                                                    <?php } ?>
                                                                    <br>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion"
                                                                    href="#ketJam"><span
                                                                        class="glyphicon glyphicon-plus"></span> Kelompok
                                                                    Kelas</a>
                                                            </h4>
                                                        </div>
                                                        <div id="ketJam"
                                                            class="panel-collapse collapse {{ Session::has('jdk_ket') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                <a href="javascript:void(0)" id="ket-PAGI"
                                                                    onclick="filter('PAGI','ket')">PAGI</a>
                                                                <?php
								                    	if ( Session::has('jdk_ket') && in_array('PAGI',Session::get('jdk_ket')) ) { ?>
                                                                <i class="filter fa fa-filter"></i>
                                                                <?php } ?>
                                                                <br>
                                                                <a href="javascript:void(0)" id="ket-SIANG"
                                                                    onclick="filter('SIANG','ket')">SIANG</a>
                                                                <?php
								                    	if ( Session::has('jdk_ket') && in_array('SIANG',Session::get('jdk_ket')) ) { ?>
                                                                <i class="filter fa fa-filter"></i>
                                                                <?php } ?>
                                                                <br>
                                                                <a href="javascript:void(0)" id="ket-MALAM"
                                                                    onclick="filter('MALAM','ket')">MALAM</a>
                                                                <?php
								                    	if ( Session::has('jdk_ket') && in_array('MALAM',Session::get('jdk_ket')) ) { ?>
                                                                <i class="filter fa fa-filter"></i>
                                                                <?php } ?>
                                                                <br>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-10 col-sm-10">

                            {{ Rmt::AlertError() }}
                            {{ Rmt::AlertSuccess() }}

                            <table border="0" width="100%" style="margin-bottom: 10px">
                                <tr>
                                    <td width="90">
                                        @if (in_array(61101, Sia::getProdiUser()))
                                            <button class="btn btn-primary btn-sm"data-toggle="modal"
                                                data-target="#modal-cetak-jdk-s2">
                                                <i class="fa fa-print"></i> CETAK
                                            </button>
                                        @else
                                            <a href="{{ route('jdk_print') }}" target="_blank"
                                                class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK</a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('jdk_cetak_label_absen') }}" target="_blank"
                                            class="btn btn-sm btn-primary"><i class="fa fa-print"></i> CETAK LABEL ABSEN</a>
                                    </td>

                                    <td width="300px">
                                        <form action="{{ route('jdk_cari') }}" method="post" id="form-cari">
                                            <div class="input-group pull-right">
                                                {{ csrf_field() }}
                                                <input type="text" class="form-control input-sm" name="q"
                                                    value="{{ Session::get('jdk_search') }}">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-default btn-sm" id="reset-cari"
                                                        type="button"><i class="fa fa-times"></i></button>
                                                    <button class="btn btn-sm btn-primary"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                    <td width="110px">
                                        <a href="{{ route('jdk_add') }}" class="btn btn-sm btn-primary pull-right"><i
                                                class="fa fa-plus"></i> TAMBAH</a>
                                    </td>

                                </tr>
                            </table>

                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0"
                                    class="table table-bordered table-striped table-hover" id="table-data">
                                    <?php $fakultas = Sia::getFakultasUser(); ?>
                                    <thead class="custom">
                                        <tr>
                                            <th width="20px">No.</th>
                                            <th>Waktu</th>
                                            <th>Matakuliah</th>
                                            <th>Smt</th>
                                            <th>Kelas /<br>Ruang</th>
                                            <th>Program Studi</th>
                                            <th>TA</th>
                                            <th>Dosen Mengajar</th>
                                            <th>Peserta</th>
                                            @if (Sia::admin())
                                                <!-- <th>PDDIKTI</th> -->
                                            @endif
                                            <th width="75">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">

                                        @foreach ($jadwal as $r)
                                            <?php
                                            // Nama dosen tidak muncul apabila dari Query yang di Sia::jadwalKuliah
                                            // Karena isi field dosen_ke tak bernilai/null
                                            $dosen_mengajar = DB::table('dosen_mengajar as dm')
                                                ->leftJoin('dosen as d', 'dm.id_dosen', '=', 'd.id')
                                                ->select('dm.*', 'd.nidn', 'd.gelar_depan', 'd.gelar_belakang', 'd.nm_dosen')
                                                ->where('dm.id_jdk', $r->id)
                                                ->get();
                                            ?>

                                            <tr>
                                                <td>{{ $loop->iteration - 1 + $jadwal->firstItem() }}</td>
                                                <td>
                                                    {{ empty($r->hari) ? '-' : Rmt::hari($r->hari) }}<br>
                                                    {{ substr($r->jam_masuk, 0, 5) }} - {{ substr($r->jam_keluar, 0, 5) }}
                                                </td>
                                                <td align="left"><a href="{{ route('jdk_detail', ['id' => $r->id]) }}">
                                                        {{ $r->kode_mk }} <br>
                                                        {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</a>
                                                </td>
                                                <td>{{ $r->smt }}</td>
                                                <td>{{ $r->kode_kls }}<br>{{ $r->nm_ruangan }}</td>
                                                <td>{{ $r->jenjang }}<br>{{ $r->nm_prodi }}</td>
                                                <td>{{ $r->id_smt }}</td>
                                                <td align="left">
                                                    <?php $urut = 1; ?>
                                                    @foreach ($dosen_mengajar as $dm)
                                                        @if ($dosen_mengajar->count() > 1)
                                                            {{ $urut++ }}.
                                                        @endif
                                                        {{ Sia::namaDosen($dm->gelar_depan, $dm->nm_dosen, $dm->gelar_belakang) }}<br>
                                                    @endforeach
                                                </td>
                                                <td>{{ empty($r->terisi) ? '' : $r->terisi }}</td>
                                                @if (Sia::admin())
                                                    <!-- <td>
                  @if (empty($r->feeder_status))
    <a class="btn btn-warning btn-xs"><i class="fa fa-info-circle"></i></a>
@elseif ($r->feeder_status == '1')
    <a class="btn btn-primary btn-xs"><i class="fa fa-check"></i></a>
@else
    <a class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
    @endif
                 </td> -->
                                                @endif
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-primary btn-xs dropdown-toggle"
                                                            data-toggle="dropdown">Aksi</button>
                                                        <button type="button"
                                                            class="btn btn-primary btn-xs dropdown-toggle"
                                                            data-toggle="dropdown"> <span class="caret"></span> <span
                                                                class="sr-only">Toggle Dropdown</span> </button>
                                                        <ul class="dropdown-menu pull-right align-xs-right"
                                                            role="menu">

                                                            @if ($r->id_prodi == 61101)
                                                                <li><a href="{{ route('jdk_print') }}?s2=true&id={{ $r->id }}"
                                                                        target="_blank">Cetak Jadwal</a>
                                                            @endif
                                                            <li><a href="{{ route('jdk_cetak_absen_mhs') }}?id={{ $r->id }}"
                                                                    target="_blank">Cetak Absen Mahasiswa</a>
                                                            <li><a href="{{ route('jdk_cetak_absen_dosen') }}?id={{ $r->id }}&pst={{ $r->terisi }}"
                                                                    target="_blank">Cetak Absen Dosen</a>
                                                            <li><a href="{{ route('jdk_cetak_label_absen') }}?id_jdk={{ $r->id }}"
                                                                    target="_blank">Cetak Label Absen</a>
                                                                @if (Sia::canAction($r->id_smt) && Sia::akademik())
                                                            <li class="divider"></li>
                                                            <li><a
                                                                    href="{{ route('jdk_edit', ['id' => $r->id]) }}">Ubah</a>
                                                            <li><a href="{{ route('jdk_delete', ['id' => $r->id]) }}"
                                                                    onclick="return confirm('Anda ingin menghapus data ini?')">Hapus</a>
                                        @endif
                                        </ul>
                            </div>
                            </td>
                            </tr>
                            @endforeach
                            </tbody>
                            </table>
                            @if ($jadwal->total() == 0)
                                &nbsp; Tidak ada data
                            @endif

                            @if ($jadwal->total() > 0)
                                <div class="pull-left">
                                    Jumlah data : {{ $jadwal->total() }}
                                </div>
                            @endif

                            <div class="pull-right">
                                {{ $jadwal->render() }}
                            </div>

                        </div>
                    </div>
            </div>
            </section>
        </div>

    </div>
    <!-- //content > row-->

    </div>
    <!-- //content-->

    <div id="modal-cetak-jdk-s2" class="modal fade" data-width="400" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Cetak Jadwal</h4>
        </div>
        <div class="modal-body">
            Waktu Kuliah :
            <select onchange="getKelasJdk(this.value)" class="form-custom">
                <option value="PAGI">PAGI</option>
                <option value="SIANG">SIANG</option>
                <option value="MALAM">MALAM</option>
            </select>
            <br>
            <br>
            <div id="konten-jdk"></div>
        </div>
    </div>
@endsection

@section('registerscript')
    <script>
        $(document).ready(function() {
            /* Add minus icon for collapse element which is open by default */
            $(".collapse.in").each(function() {
                $(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus")
                    .removeClass("glyphicon-plus");
            });

            /* Toggle plus minus icon on show hide of collapse element */
            $(".collapse").on('show.bs.collapse', function() {
                $(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass(
                    "glyphicon-minus");
            }).on('hide.bs.collapse', function() {
                $(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass(
                    "glyphicon-plus");
            });

            $('#nav-mini').trigger('click');

            $('#reset-cari').click(function() {
                var q = $('input[name="q"]').val();
                $('input[name="q"]').val('');
                if (q.length > 0) {
                    $('#form-cari').submit();
                }

            });
        });

        function filter(value, modul) {
            $('#' + modul + '-' + value).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route('jdk_filter') }}',
                data: {
                    value: value,
                    modul: modul
                },
                success: function(result) {
                    var param = '{{ Request::has('page') }}';
                    if (param === '') {
                        window.location.reload();
                    } else {
                        window.location.href = '{{ route('jdk') }}';
                    }
                },
                error: function(data, status, msg) {
                    alert(msg);
                }
            });
        }

        function getKelasJdk(waktu_kuliah) {
            $('#konten-jdk').html('<center><i class="fa fa-spinner fa-spin fa-2x"></i></center>');

            $.ajax({
                url: '{{ route('jdk_ajax') }}',
                data: {
                    tipe: 'jdk-cetak-s2',
                    waktu_kuliah: waktu_kuliah
                },
                success: function(result) {
                    $('#konten-jdk').html(result);
                },
                error: function(data, status, msg) {
                    alert('Gagal mengambil data jadwal, silahkan muat ulang halaman. Message: ' + msg);
                }
            });
        }

        @if (in_array(61101, Sia::getProdiUser()))
            getKelasJdk('PAGI');
        @endif
    </script>
@endsection
