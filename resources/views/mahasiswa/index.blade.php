@extends('layouts.app')

@section('title', 'Mahasiswa')

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
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>MAHASISWA</a></li>
    </ul>
@endsection

@section('content')

    <div id="overlay"></div>

    <div id="content">

        <div class="row">

            <div class="col-md-12">
                <section class="panel">

                    <div class="panel-body">

                        <div class="col-md-3" style="padding-left: 0">
                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0"
                                    class="table table-bordered table-striped">
                                    <thead class="custom">
                                        <tr>
                                            <th>
                                                FILTER
                                                <span class="tooltip-area pull-right">
                                                    <?php if ( Session::has('mhs_prodi') || Session::has('mhs_angkatan') || Session::has('mhs_status') || Session::has('mhs_jenkel') || Session::has('mhs_agama') || Session::has('mhs_ta') || Session::has('mhs_jns_daftar') || Session::has('mhs_search') || Session::has('mhs_waktu_kuliah') ) { ?>
                                                    <a href="{{ route('mahasiswa_filter') }}" class="btn btn-warning btn-xs"
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
                                                            class="panel-collapse collapse {{ Session::has('mhs_prodi') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                <?php $fakultas = Sia::getFakultasUser(); ?>


                                                                @if ($fakultas == 2)
                                                                    @foreach (Sia::listProdiAll() as $r)
                                                                        <a href="javascript:void(0)"
                                                                            id="prodi-{{ $r->id_prodi }}"
                                                                            onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }}
                                                                            {{ $r->nm_prodi }}</a>
                                                                        <?php
										                    	if ( Session::has('mhs_prodi') && in_array($r->id_prodi,Session::get('mhs_prodi')) ) { ?>
                                                                        <i class="filter fa fa-filter"></i>
                                                                        <?php } ?>
                                                                        <br>
                                                                    @endforeach
                                                                @else
                                                                    @foreach (Sia::listProdi() as $r)
                                                                        <a href="javascript:void(0)"
                                                                            id="prodi-{{ $r->id_prodi }}"
                                                                            onclick="filter({{ $r->id_prodi }},'prodi')">{{ $r->jenjang }}
                                                                            {{ $r->nm_prodi }}</a>
                                                                        <?php
										                    	if ( Session::has('mhs_prodi') && in_array($r->id_prodi,Session::get('mhs_prodi')) ) { ?>
                                                                        <i class="filter fa fa-filter"></i>
                                                                        <?php } ?>
                                                                        <br>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion"
                                                                    href="#collpaseTa"><span
                                                                        class="glyphicon glyphicon-plus"></span> Semester
                                                                    Masuk</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collpaseTa"
                                                            class="panel-collapse collapse {{ Session::has('mhs_ta') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::listSemester() as $ta)
                                                                    <a href="javascript:void(0);"
                                                                        id="ta-{{ $ta->id_smt }}"
                                                                        onclick="filter({{ $ta->id_smt }},'ta')">{{ $ta->nm_smt }}</a>
                                                                    <?php
								                    	if ( Session::has('mhs_ta') && in_array($ta->id_smt,Session::get('mhs_ta')) ) { ?>
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
                                                                    href="#collpaseAngkatan"><span
                                                                        class="glyphicon glyphicon-plus"></span>
                                                                    Angkatan</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collpaseAngkatan"
                                                            class="panel-collapse collapse {{ Session::has('mhs_angkatan') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::listAngkatan() as $a)
                                                                    <a href="javascript:void(0);"
                                                                        id="angkatan-{{ $a }}"
                                                                        onclick="filter({{ $a }},'angkatan')">{{ $a }}</a>
                                                                    <?php
								                    	if ( Session::has('mhs_angkatan') && in_array($a,Session::get('mhs_angkatan')) ) { ?>
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
                                                                    href="#collapseStatusMahasiswa"><span
                                                                        class="glyphicon glyphicon-plus"></span> Status
                                                                    Mahasiswa</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseStatusMahasiswa"
                                                            class="panel-collapse collapse {{ Session::has('mhs_status') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::statusMhs() as $r)
                                                                    <a href="javascript:void(0)"
                                                                        id="status-{{ $r->id_jns_keluar }}"
                                                                        onclick="filter({{ $r->id_jns_keluar }},'status')">{{ $r->ket_keluar }}</a>
                                                                    <?php
									                    	if ( Session::has('mhs_status') && in_array($r->id_jns_keluar,Session::get('mhs_status')) ) { ?>
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
                                                                    href="#collpaseJenisDaftar"><span
                                                                        class="glyphicon glyphicon-plus"></span> Jenis
                                                                    Pendaftaran</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collpaseJenisDaftar"
                                                            class="panel-collapse collapse {{ Session::has('mhs_jns_daftar') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::jenisDaftar() as $jd)
                                                                    <a href="javascript:void(0);"
                                                                        id="jns_daftar-{{ $jd->id_jns_pendaftaran }}"
                                                                        onclick="filter({{ $jd->id_jns_pendaftaran }},'jns_daftar')">{{ $jd->nm_jns_pendaftaran }}</a>
                                                                    <?php
								                    	if ( Session::has('mhs_jns_daftar') && in_array($jd->id_jns_pendaftaran,Session::get('mhs_jns_daftar')) ) { ?>
                                                                    <i class="filter fa fa-filter"></i>
                                                                    <?php } ?>
                                                                    <br>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion"
                                                                    href="#jenisMahasiswa"><span
                                                                        class="glyphicon glyphicon-plus"></span> Jenis
                                                                    Mahasiswa</a>
                                                            </h4>
                                                        </div>
                                                        <div id="jenisMahasiswa"
                                                            class="panel-collapse collapse {{ Session::has('mhs_jns_daftar') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::jenisDaftar() as $jd)
                                                                    <a href="javascript:void(0);"
                                                                        id="jns_daftar-{{ $jd->id_jns_pendaftaran }}"
                                                                        onclick="filter({{ $jd->id_jns_pendaftaran }},'jns_daftar')">{{ $jd->nm_jns_pendaftaran }}</a>
                                                                    <?php
								                    	// if ( Session::has('mhs_jns_daftar') && in_array($jd->id_jns_pendaftaran,Session::get('mhs_jns_daftar')) ) : ?>
                                                                    <i class="filter fa fa-filter"></i>
                                                                    <?php //endif; ?>
                                                                    <br>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <div class="panel panel-info">
                                                        <div class="panel-heading">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion"
                                                                    href="#collapseJenkel"><span
                                                                        class="glyphicon glyphicon-plus"></span> Jenis
                                                                    Kelamin</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseJenkel"
                                                            class="panel-collapse collapse {{ Session::has('mhs_jenkel') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::jenisKelamin() as $a)
                                                                    <a href="javascript:void(0)"
                                                                        id="jenkel-{{ $a['id'] }}"
                                                                        onclick="filter('{{ $a['id'] }}','jenkel')">{{ $a['nama'] }}</a>
                                                                    <?php
									                    	if ( Session::has('mhs_jenkel') && in_array($a['id'],Session::get('mhs_jenkel')) ) { ?>
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
                                                                    href="#collapseAgama"><span
                                                                        class="glyphicon glyphicon-plus"></span> Agama</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseAgama"
                                                            class="panel-collapse collapse {{ Session::has('mhs_agama') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::listAgama() as $r)
                                                                    <a href="javascript:void(0)"
                                                                        id="agama-{{ $r->id_agama }}"
                                                                        onclick="filter('{{ $r->id_agama }}','agama')">{{ $r->nm_agama }}</a>
                                                                    <?php
									                    	if ( Session::has('mhs_agama') && in_array($r->id_agama,Session::get('mhs_agama')) ) { ?>
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
                                                                    href="#collapseWk"><span
                                                                        class="glyphicon glyphicon-plus"></span> Waktu
                                                                    Kuliah</a>
                                                            </h4>
                                                        </div>
                                                        <div id="collapseWk"
                                                            class="panel-collapse collapse {{ Session::has('mhs_waktu_kuliah') ? 'in' : '' }}">
                                                            <div class="panel-body">
                                                                @foreach (Sia::waktuKuliah() as $val)
                                                                    <a href="javascript:void(0)"
                                                                        id="waktu_kuliah-{{ $val }}"
                                                                        onclick="filter('{{ $val }}','waktu_kuliah')">{{ $val }}</a>
                                                                    <?php
									                    	if ( Session::has('mhs_waktu_kuliah') && in_array($val,Session::get('mhs_waktu_kuliah')) ) { ?>
                                                                    <i class="filter fa fa-filter"></i>
                                                                    <?php } ?>
                                                                    <br>
                                                                @endforeach
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

                        <div class="col-md-9">

                            {{ Rmt::AlertError() }}
                            {{ Rmt::AlertSuccess() }}
                            @if (Session::has('data_error'))
                                @foreach (Session::get('data_error') as $val)
                                    er: {{ $val }}<br>
                                @endforeach
                            @endif

                            <div class="table-responsive">

                                <table border="0" width="100%" style="margin-bottom: 10px;min-width: 500px">
                                    <tr>
                                        <td width="100">
                                            @if (Sia::admin())
                                                <!-- 	<button class="btn btn-theme btn-sm"  data-toggle="modal" data-target="#modal-impor" data-backdrop="static" data-keyboard="false"">+ IMPORT</button> -->
                                            @endif
                                            <button class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#modal-ekspor"><i class="fa fa-print"></i> EKSPOR</button>
                                        </td>
                                        @if (Sia::role('akademik|admin'))
                                            <td>
                                                <a href="javascript:;" data-toggle="modal" data-target="#modal-impor-pin"
                                                    class="btn btn-theme btn-sm" data-backdrop="static"><i
                                                        class="fa fa-upload"></i> Impor PIN</a>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif


                                        <td width="300px">
                                            <form action="{{ route('mahasiswa_cari') }}" method="post" id="form-cari">
                                                <div class="input-group pull-right">
                                                    {{ csrf_field() }}
                                                    <input type="text" class="form-control input-sm" name="q"
                                                        value="{{ Session::get('mhs_search') }}">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-default btn-sm" id="reset-cari"
                                                            type="button"><i class="fa fa-times"></i></button>
                                                        <button class="btn btn-sm btn-primary"><i
                                                                class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                        @if (Sia::role('akademik|cs|personalia'))
                                            <td width="110px">
                                                <a href="{{ route('mahasiswa_add') }}"
                                                    class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus"></i>
                                                    TAMBAH</a>
                                            </td>
                                        @endif

                                    </tr>
                                </table>
                            </div>

                            <div class="table-responsive">

                                <table cellpadding="0" cellspacing="0" border="0"
                                    class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                        <tr>
                                            <th>No.</th>
                                            <th>Mahasiswa</th>
                                            <th>Gen</th>
                                            <th>Tgl Lahir</th>
                                            <th>Prodi</th>
                                            @if (Sia::role('admin|akademik|cs|personalia'))
                                                <th>Status</th>
                                                <th>Smstr</th>
                                                <th>Pembayaran</th>
                                            @endif

                                            @if (Sia::role('keuangan'))
                                                <th>Bebas Pembayaran</th>
                                            @endif

                                            @if (Sia::role('jurnal'))
                                                <th>Jur. File</th>
                                                <th>Jur. Approval</th>
                                                <th>Pub. Jurnal</th>
                                            @endif

                                            <th width="100">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">
                                        @foreach ($mahasiswa as $r)
                                            <tr>
                                                <td>{{ $loop->iteration - 1 + $mahasiswa->firstItem() }}</td>
                                                <td align="left"><a
                                                        href="{{ route('mahasiswa_detail', ['id' => $r->id]) }}">{{ $r->nim }}
                                                        - {{ $r->gelar_depan }}
                                                        {{ trim($r->nm_mhs) }}{{ !empty($r->gelar_belakang) ? ', ' . $r->gelar_belakang : '' }}</a>
                                                </td>
                                                <td>{{ $r->jenkel }}</td>
                                                <td>{{ Rmt::formatTgl($r->tgl_lahir) }}</td>
                                                <td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>

                                                @if (Sia::role('admin|akademik|cs|personalia'))
                                                    <td>{{ $r->ket_keluar }}</td>
                                                    @if ($r->ket_keluar == 'AKTIF')
                                                        <td>{{ Sia::posisiSemesterMhs($r->semester_mulai) }}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                    <td>
                                                        @if ($r->bebas_pembayaran == 0)
                                                            <i class="fa fa-ban" style="color: red"></i>
                                                        @else
                                                            <i class="fa fa-check-square" style="color: green"></i>
                                                        @endif
                                                    </td>
                                                @endif

                                                @if (Sia::role('jurnal'))
                                                    <td>
                                                        @if (empty($r->jurnal_file))
                                                            <i class="fa fa-ban" style="color: red"></i>
                                                        @else
                                                            <i class="fa fa-check-square" style="color: green"></i>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if (empty($r->jurnal_approved))
                                                            <a href="javascript:;" class="btn btn-xs btn-default"
                                                                id="approve-<?= $r->id_mhs_reg ?>"
                                                                onclick="approve('<?= $r->id_mhs_reg ?>', '1')">
                                                                <i class="fa fa-ban" style="color: red"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:;" class="btn btn-xs btn-default"
                                                                id="unapprove-<?= $r->id_mhs_reg ?>"
                                                                onclick="approve('<?= $r->id_mhs_reg ?>', '0')">
                                                                <i class="fa fa-check-square" style="color: green"></i>
                                                            </a>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if (empty($r->jurnal_published))
                                                            <a href="javascript:;" class="btn btn-xs btn-default"
                                                                id="publish-<?= $r->id_mhs_reg ?>"
                                                                onclick="publish('<?= $r->id_mhs_reg ?>', '1')">
                                                                <i class="fa fa-ban" style="color: red"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:;" class="btn btn-xs btn-default"
                                                                id="unpublish-<?= $r->id_mhs_reg ?>"
                                                                onclick="publish('<?= $r->id_mhs_reg ?>', '0')">
                                                                <i class="fa fa-check-square" style="color: green"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif

                                                @if (Sia::role('keuangan'))
                                                    <td>
                                                        <select
                                                            onchange="updateBebasBayar('{{ $r->id_mhs_reg }}',this.value)"
                                                            class="form-control">
                                                            <option value="1"
                                                                {{ $r->bebas_pembayaran == '1' ? 'selected' : '' }}>BEBAS
                                                            </option>
                                                            <option value="0"
                                                                {{ $r->bebas_pembayaran == '0' ? 'selected' : '' }}>BELUM
                                                                Bebas</option>
                                                        </select>
                                                    </td>
                                                @endif

                                                <td>
                                                    @if ($r->id_jenis_keluar == 0 && Sia::role('akademik'))
                                                        <!-- <a href="{{ route('mahasiswa_krs', ['id' => $r->id]) }}?nim={{ $r->nim }}" class="btn btn-primary btn-xs" title="KRS">KRS</a> -->
                                                    @endif

                                                    @if (Sia::role('admin'))
                                                        <a href="{{ route('mahasiswa_relogin', ['id_user' => $r->id_user]) }}"
                                                            class="btn btn-primary btn-xs" title="Jurnal">Masuk</a>
                                                    @endif

                                                    @if (Sia::role('jurnal|admin|akademik|cs'))
                                                        <!-- <a href="{{ route('mahasiswa_jurnal', ['id' => $r->id]) }}?nim={{ $r->nim }}" class="btn btn-primary btn-xs" title="Jurnal">Jurnal</a> -->
                                                    @endif

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
                                                            <li><a
                                                                    href="{{ route('mahasiswa_detail', ['id' => $r->id]) }}">Detail</a>
                                                            </li>
                                                            @if ($r->id_jenis_keluar == 0 && Sia::role('akademik'))
                                                                <li><a
                                                                        href="{{ route('mahasiswa_krs', ['id' => $r->id]) }}?nim={{ $r->nim }}">KRS</a>
                                                                </li>
                                                            @endif
                                                            @if ($r->id_jenis_keluar == 0)
                                                                <li><a href="javascript:;"
                                                                        onclick="cetakSkKuliah('{{ $r->id_mhs_reg }}')">Surat
                                                                        Keterangan Kuliah</a></li>
                                                            @endif

                                                            @if (Sia::role('admin|akademik|cs|personalia'))
                                                                @if (Sia::role('personalia|admin|cs'))
                                                                    <li class="divider"></li>
                                                                    <li><a href="javascript:void()"
                                                                            data-nim="<?= $r->nim ?>"
                                                                            data-nama="<?= $r->nm_mhs ?>"
                                                                            data-prodi="<?= $r->id_prodi ?>"
                                                                            class="show-modal"><i
                                                                                class="fa fa-credit-card"></i> Kartu
                                                                            Mahasiswa</a></li>
                                                                @endif

                                                                @if (Sia::role('admin|akademik'))
                                                                    <li class="divider"></li>
                                                                    <li><a
                                                                            href="{{ route('mahasiswa_edit', ['id' => $r->id]) }}"><i
                                                                                class="fa fa-pencil"></i> Ubah</a></li>
                                                                    <li><a href="{{ route('mahasiswa_delete', ['id' => $r->id, 'id_mhs_reg' => $r->id_mhs_reg]) }}"
                                                                            onclick="return confirm('Anda ingin menghapus data ini?')"><i
                                                                                class="fa fa-trash-o"></i> Hapus</a></li>
                                                                @endif
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if ($mahasiswa->total() == 0)
                                    &nbsp; Tidak ada data
                                @endif

                                @if ($mahasiswa->total() > 0)
                                    <div class="pull-left">
                                        Jumlah data : {{ $mahasiswa->total() }}
                                    </div>
                                @endif

                                <div class="pull-right">
                                    {{ $mahasiswa->appends(request()->except('page'))->render() }}
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

    <div id="modal-impor" class="modal fade" tabindex="-1" style="top:30%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                    class="fa fa-times"></i></button>
            <h4 class="modal-title">Impor Mahasiswa</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <form id="form-mahasiswa" action="{{ route('mahasiswa_impor') }}" enctype="multipart/form-data"
                method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="fileExcel">Upload File</label>
                    <input type="file" id="fileExcel" name="file">
                    <p class="help-block">Unggah file excel <b>.xlsx</b></p>
                </div>

                <button type="submit" id="btn-submit" class="btn btn-primary btn-sm">IMPOR</button>&nbsp; &nbsp; &nbsp;
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-default pull-right">BATAL</button>

            </form>
        </div>
        <!-- //modal-body-->
    </div>

    <div id="modal-ekspor" class="modal fade" style="top:30%" tabindex="-1" data-width="300">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                    class="fa fa-times"></i></button>
            <h4 class="modal-title">Ekspor Mahasiswa</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <center>
                <a href="{{ route('mahasiswa_excel') }}" class="btn btn-sm btn-primary"><i class="fa fa-file-text"></i>
                    EXCEL</a>&nbsp;
                <a href="{{ route('mahasiswa_print') }}" target="_blank" class="btn btn-sm btn-primary"><i
                        class="fa fa-print"></i> CETAK</a>
            </center>
        </div>
        <!-- //modal-body-->
    </div>

    <div id="modal-sk-kuliah" class="modal fade" style="top: 40% !important" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Cetak Surat Keterangan Kuliah</h4>
        </div>
        <div class="modal-body">
            <div class="ajax-message"></div>
            <input type="hidden" id="id-mhs-reg">
            <table width="100%">
                <tr>
                    <td width="200"> Set Tanggal</td>
                    <td width="100"><input type="date" id="tgl" class="form-control"
                            value="{{ Carbon::now()->format('Y-m-d') }}"></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td> Set Nomor Surat (Opsional)</td>
                    <td><input type="number" id="nomor-surat" class="form-control"></td>
                    <td></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2"><br>
                        <a href="javascript:;" onclick="doCetakSk()" class="btn btn-primary btn-sm"><i
                                class="fa fa-print"></i> Cetak</a>
                    </td>
                </tr>
            </table>

        </div>

    </div>

    <div id="modal-error" class="modal fade" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                    class="fa fa-times"></i></button>
            <h4 class="modal-title">Terjadi kesalahan</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <div class="ajax-message"></div>
            <center>
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
            </center>
        </div>
        <!-- //modal-body-->

    </div>

    <div id="modal-foto" class="modal fade" style="top: 40% !important" tabindex="-1" data-width="800">

        <div class="modal-header">
            <h5>
                <span id="judul"></span>
                <div class="pull-right">
                    <a href="{{ route('mahasiswa_kartu_mhs_sisi_depan') }}?j=s1" class="btn btn-primary btn-xs"
                        target="_blank">Cetak sisi depan S1</a>
                    <a href="{{ route('mahasiswa_kartu_mhs_sisi_depan') }}?j=s2" class="btn btn-primary btn-xs"
                        target="_blank">Cetak sisi depan S2</a>
                </div>
            </h5>
        </div>

        <div class="modal-body" style="min-height: 237px">
            <div class="row">

                <div class="col-md-6 col-sm-6">

                    <div class="alert alert-danger" style="display: none"></div>

                    <form id="coords" class="coords" action="{{ route('mahasiswa_kartu_mhs_crop') }}" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="nim" id="nim">
                        <div class="form-group">
                            <div>
                                <input type="hidden" id="x" name="x" />
                                <input type="hidden" id="y" name="y" />
                                <input type="hidden" id="w" name="w" />
                                <input type="hidden" id="h" name="h" />
                                <input type="hidden" id="pimg" name="pimg" />
                                <button type="button" class="btn btn-danger btn-crop-upload disabled pull-right"
                                    id="btn-submit-foto" style="display:none">Upload</button>

                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <span class="btn btn-inverse btn-file">
                                        <span class="fileinput-new"><i class="fa fa-picture-o"></i> Masukkan Gambar</span>
                                        <span class="fileinput-exists"><i class="fa fa-refresh"></i> Ubah</span>
                                        <input type="hidden" name="id_prodi" id="id-prodi">
                                        <input id="uploadImage" type="file" accept="image/jpeg" name="image" />
                                    </span>
                                </div>
                            </div>
                        </div><!-- //form-group-->
                        <div class="form-group" id="preview">
                            <img id="uploadPreview" style="display:none; width:200px;" />
                        </div><!-- //form-group-->
                    </form>

                </div>

                <div class="col-md-6 col-sm-6">
                    <div id="show-card"></div>
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn btn-sm btn-danger pull-left" data-dismiss="modal"
                        id="btn-close"><i class="fa fa-times"></i> Keluar</button>

                    <a href="" class="btn btn-primary btn-sm pull-right" id="btn-cetak" style="display: none"
                        target="_blank"><i class="fa fa-print"></i> Cetak</a>
                </div>
            </div>
        </div>

    </div>

    <div id="modal-impor-pin" class="modal fade" tabindex="-1" style="top:30%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                    class="fa fa-times"></i></button>
            <h4 class="modal-title">Impor PIN</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <form id="form-impor-pin" action="{{ route('mahasiswa_pasang_pin') }}" enctype="multipart/form-data"
                method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="fileExcel">Upload File</label>
                    <input type="file" class="form-control" id="fileExcel" name="file">
                    <p class="help-block">Unggah file excel <b>.xlsx</b></p>
                </div>

                <button type="submit" id="btn-submit-impor-pin" class="btn btn-theme btn-sm">IMPOR</button>&nbsp; &nbsp;
                &nbsp;
                <a href="{{ url('storage') }}/contoh-data/contoh format impor pin.xlsx" target="_blank"
                    class="btn btn-sm btn-primary pull-right">LIHAT CONTOH DATA</a>

            </form>
        </div>
        <!-- //modal-body-->
    </div>

@endsection

@section('registerscript')
    <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
    <script>
        $(document).ready(function() {

            $(".collapse.in").each(function() {
                $(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus")
                    .removeClass("glyphicon-plus");
            });

            $(".collapse").on('show.bs.collapse', function() {
                $(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass(
                    "glyphicon-minus");
            }).on('hide.bs.collapse', function() {
                $(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass(
                    "glyphicon-plus");
            });

            $('#nav-mini').trigger('click');

            @if (Session::has('bebas'))
                $.notific8('Berhasil mengubah data', {
                    life: 5000,
                    horizontalEdge: "top",
                    theme: "primary",
                    heading: " Pesan "
                });
            @endif

            $('#reset-cari').click(function() {
                var q = $('input[name="q"]').val();
                $('input[name="q"]').val('');
                if (q.length > 0) {
                    $('#form-cari').submit();
                }

            });

            /* Crop image */
            $(document).on('click', '.show-modal', function() {
                $('#modal-foto').modal('show');
                var nim = $(this).data('nim');
                var nama = $(this).data('nama');
                var prodi = $(this).data('prodi');
                $('#id-prodi').val(prodi);
                $('#nim').val(nim);
                $('#judul').html(nim + ' - ' + nama);
                $('#btn-submit-foto').hide();
                $('#preview').hide();
                $('#fileinput-new').show();
                $('#fileinput-exists').hide();
                $('#btn-cetak').attr('href', '{{ route('mahasiswa_kartu_mhs_cetak') }}?nim=' + nim);
                previewKartu(nim, prodi);

            });

            var uploadPreview = $("#uploadPreview");
            var jcrop_api, pic_width, pic_height;

            $("#uploadImage").change(function() {
                var prodi = $('#id-prodi').val();
                $('#preview').show();
                if (typeof jcrop_api != 'undefined') {
                    jcrop_api.destroy();
                }
                if ($(this).val()) {
                    $(".btn-crop-upload").fadeIn();
                } else {
                    $(".btn-crop-upload").fadeOut();
                }
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById("uploadImage").files[0]);
                oFReader.onload = function(oFREvent) {
                    uploadPreview.attr('src', oFREvent.target.result).show();
                    $("#pimg").val(oFREvent.target.result);
                    var img = new Image();
                    img.onload = function() {
                        pic_width = this.width;
                        pic_height = this.height;
                    }
                    img.src = oFREvent.target.result;
                };

                setTimeout(() => {
                    if (prodi === '61101') {
                        uploadPreview.Jcrop({
                            bgOpacity: .5,
                            aspectRatio: 2.4 / 3,
                            trueSize: [pic_width, pic_height],
                            onChange: showCoords,
                            onSelect: showCoords,
                            onRelease: clearCoords
                        }, function() {
                            jcrop_api = this;
                            jcrop_api.animateTo([10, 19, 225, 282]);
                        });
                        $('#coords').on('change', 'input', function(e) {
                            var x1 = $('#x').val(),
                                y1 = $('#y').val();
                        });
                    } else {
                        uploadPreview.Jcrop({
                            bgOpacity: .5,
                            aspectRatio: 2.4 / 3.6,
                            trueSize: [pic_width, pic_height],
                            onChange: showCoords,
                            onSelect: showCoords,
                            onRelease: clearCoords
                        }, function() {
                            jcrop_api = this;
                            jcrop_api.animateTo([10, 19, 225, 282]);
                        });
                        $('#coords').on('change', 'input', function(e) {
                            var x1 = $('#x').val(),
                                y1 = $('#y').val();
                        });
                    }

                }, 400);
            });

            $('.btn-crop-upload').on('click', function() {
                $(this).closest("form").submit();
            });

            // Submit form
            var options = {
                beforeSend: function() {
                    $('#overlay').show();
                    $("#btn-submit-foto").attr('disabled', '');
                    $("#btn-submit-foto").html(
                        "<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengupload...");
                },
                success: function(data, status, message) {
                    $('#overlay').hide();
                    if (data.error == 1) {
                        showMessage(data.msg);
                        $('.alert-danger').html(data.msg);
                    } else {
                        $('.alert-danger').hide();
                        $('#coords').resetForm();
                        $('#btn-submit-foto').removeAttr('disabled');
                        $('#btn-submit-foto').html('Upload');
                        $('#btn-submit-foto').hide();
                        $('#preview').hide();
                        $('#fileinput-new').show();
                        $('#fileinput-exists').hide();
                        previewKartu(data.nim);
                    }
                },
                error: function(data, status, message) {
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for (i = 0; i < respon.length; i++) {
                        pesan += "- " + respon[i] + "<br>";
                    }
                    if (pesan == '') {
                        pesan = message;
                    }

                    showMessage(pesan);
                }
            };

            $('#coords').ajaxForm(options);
            /* end crop */

        });

        function showCoords(c) {
            $('#x').val(c.x);
            $('#y').val(c.y);
            $('#w').val(c.w);
            $('#h').val(c.h);
            $('.btn-crop-upload').removeClass('disabled');
        }

        function clearCoords() {
            $('#coords input').val('');
            $('.btn-crop-upload').addClass('disabled');
        }

        function previewKartu(nim) {
            $('#show-card').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            $('#btn-cetak').hide();
            $('#btn-close').hide();
            setTimeout(function() {
                $.get('{{ route('mahasiswa_kartu_mhs_prev') }}', {
                    nim: nim
                }, function(data) {
                    $('#show-card').html(data);
                    $('#btn-cetak').show();
                    $('#btn-close').show();
                });
            }, 500);
        }

        function filter(value, modul) {
            $('#' + modul + '-' + value).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route('mahasiswa_filter') }}',
                data: {
                    value: value,
                    modul: modul
                },
                success: function(result) {
                    var param = '{{ Request::has('page') }}';
                    if (param === '') {
                        window.location.reload();
                    } else {
                        window.location.href = '{{ route('mahasiswa') }}';
                    }
                },
                error: function(data, status, msg) {
                    alert(msg);
                }
            })
        }

        function showMessage(pesan) {
            $('#overlay').hide();
            $('.ajax-message').html(pesan);
            $('#modal-error').modal('show');

            $('#btn-submit').removeAttr('disabled');
            $('#btn-submit').html('<i class="fa fa-floppy-o"></i> IMPOR');
            $('#btn-submit-foto').removeAttr('disabled');
            $('#btn-submit-foto').html('<i class="fa fa-floppy-o"></i> IMPOR');
        }

        function submit(modul) {
            var options = {
                beforeSend: function() {
                    $('#overlay').show();
                    $("#btn-submit-" + modul).attr('disabled', '');
                    $("#btn-submit-" + modul).html(
                        "<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengimpor...");
                },
                success: function(data, status, message) {
                    if (data.error == 1) {
                        showMessage(data.msg);
                    } else {
                        alert('Impor berhasil.. :)');
                        window.location.href = '{{ route('mahasiswa') }}';
                    }
                },
                error: function(data, status, message) {
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for (i = 0; i < respon.length; i++) {
                        pesan += "- " + respon[i] + "<br>";
                    }
                    if (pesan == '') {
                        pesan = message;
                    }
                    showMessage(pesan);
                }
            };

            $('#form-' + modul).ajaxForm(options);
        }
        submit('mahasiswa');
        submit('impor-pin');

        function cetakSkKuliah(id_mhs_reg) {
            $('#modal-sk-kuliah').modal('show');
            $('#id-mhs-reg').val(id_mhs_reg);
        }

        function doCetakSk() {
            var tgl = $('#tgl').val();
            var id = $('#id-mhs-reg').val();
            var no_surat = $('#nomor-surat').val();
            window.open('{{ route('mahasiswa_sk_kuliah') }}/' + id + '?tgl=' + tgl + '&nomor=' + no_surat);
        }

        function publish(id_mhs_reg, value) {
            var div, newValue;
            if (value === '1') {
                newValue = '0';
                div = $('#publish-' + id_mhs_reg);
            } else {
                newValue = '1';
                div = $('#unpublish-' + id_mhs_reg);
            }

            div.html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route('mahasiswa_jurnal_publish') }}',
                data: {
                    publish: value,
                    id_mhs_reg: id_mhs_reg
                },
                success: function(result) {
                    showSuccess('Berhasil mengubah data');
                    div.attr('onclick', 'publish(\'' + id_mhs_reg + '\',\'' + newValue + '\')');
                    if (value === '1') {
                        div.html('<i class="fa fa-check-square" style="color: green"></i>');
                        div.attr('id', 'unpublish-' + id_mhs_reg);
                    } else {
                        div.html('<i class="fa fa-ban" style="color: red"></i>');
                        div.attr('id', 'publish-' + id_mhs_reg);
                    }

                },
                error: function(data, status, msg) {
                    alert(data.responseText);
                    if (value === '1') {
                        div.html('<i class="fa fa-ban" style="color: red"></i>');
                    } else {
                        div.html('<i class="fa fa-check-square" style="color: green"></i>');
                    }
                }
            })
        }

        function approve(id_mhs_reg, value) {
            var div, newValue;
            if (value === '1') {
                newValue = '0';
                div = $('#approve-' + id_mhs_reg);
            } else {
                newValue = '1';
                div = $('#unapprove-' + id_mhs_reg);
            }

            div.html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route('mahasiswa_jurnal_publish') }}',
                data: {
                    approve: value,
                    id_mhs_reg: id_mhs_reg,
                    jenis: 'approval'
                },
                success: function(result) {
                    showSuccess('Berhasil mengubah data');
                    div.attr('onclick', 'approve(\'' + id_mhs_reg + '\',\'' + newValue + '\')');
                    if (value === '1') {
                        div.html('<i class="fa fa-check-square" style="color: green"></i>');
                        div.attr('id', 'unpublish-' + id_mhs_reg);
                    } else {
                        div.html('<i class="fa fa-ban" style="color: red"></i>');
                        div.attr('id', 'approve-' + id_mhs_reg);
                    }

                },
                error: function(data, status, msg) {
                    alert(data.responseText);
                    if (value === '1') {
                        div.html('<i class="fa fa-ban" style="color: red"></i>');
                    } else {
                        div.html('<i class="fa fa-check-square" style="color: green"></i>');
                    }
                }
            })
        }

        function updateBebasBayar(id_mhs_reg, ket) {
            window.location.href = '{{ route('mahasiswa_bebas_bayar') }}?value=' + ket + '&id_mhs_reg=' + id_mhs_reg;
        }

        function updateBebasPustaka(id_mhs_reg, ket) {
            window.location.href = '{{ route('mahasiswa_bebas_pustaka') }}?value=' + ket + '&id_mhs_reg=' + id_mhs_reg;
        }

        function updateBebasSkripsi(id_mhs_reg, ket) {
            window.location.href = '{{ route('mahasiswa_bebas_skripsi') }}?value=' + ket + '&id_mhs_reg=' + id_mhs_reg;
        }
    </script>
@endsection
