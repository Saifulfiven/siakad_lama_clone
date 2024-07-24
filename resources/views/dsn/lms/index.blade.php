@extends('layouts.app')

@section('title','Learning Management System')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>{{ $r->kode_kls .' - '.$r->nm_mk }}</a></li>
    </ul>
@endsection

@section('content')

<?php $id_jadwal = $r->id ?>

<?php
    $jml_dosen = DB::table('dosen_mengajar')->where('id_jdk', $r->id)->count();
    if ( $jml_dosen == 1 && $r->id_prodi == 61101 ) {
        $jml_pertemuan = $jml_pertemuan + 6;
    }
?>

<ol class="breadcrumb">
    <li><a href="{{ route('dsn_jadwal') }}">Matakuliah</a></li>
    <li class="active">{{ $r->kode_kls .' - '.$r->nm_mk }}</li>
</ol>

<div id="content" style="padding-top: 0">

    <div class="row" >
        <div class="col-md-12">
            <div class="col-lg-8 col-md-6 col-sm-6" style="padding-left: 0">
                <button class="btn btn-theme btn-sm" data-toggle="modal" data-target="#modal-panduan">Panduan</button> &nbsp;
                <button class="btn btn-default toggle-forum" style="height: 33px;margin-top: 1px"><i class="fa fa-comments"></i> Forum <i class="karet fa fa-caret-down"></i></button> &nbsp;
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-undang">Masukkan Peserta</button>

                @if ( count($peserta_undangan) > 0 )
                    <span class="tooltip-area">
                        &nbsp; <button class="btn btn-primary btn-transparent btn-sm" data-toggle="modal" title="Peserta Undangan" data-target="#md-peserta-undangan"><i class="fa fa-users"></i></button>
                    </span>
                @endif

                @if ( count($undangan) > 0 )
                    <span class="tooltip-area">
                        &nbsp;
                        <button class="btn btn-default btn-transparent btn-sm notif-persetujuan" style="color: #f35958" data-toggle="modal" data-target="#md-notification" title="Permintaan Baru">
                            <i class="fa fa-bell-o"></i>
                            <em class="active"></em>
                        </button>
                    </span>
                @endif

            </div>

            <div class="input-group col-lg-4 col-md-6 col-sm-6" style="padding-left: 0 !important;padding-right: 0 !important">
                <select class="form-control input-sm" id="pertemuan-ke">
                    <option value="">Pilih pertemuan</option>
                    <option value="0">Intro</option>
                    @for( $i = 1; $i <= $jml_pertemuan; $i++ )
                        <option value="{{ $i }}">Pertemuan {{ $i }}</option>
                    @endfor
                </select>
                <span class="input-group-btn">
                    <button class="btn btn-theme-inverse btn-sm" type="button" onclick="openDokumen()"><i class="fa fa-plus"></i> Tambah dari Dokumen Saya</button>
                </span>

            </div>
        </div>

        <br>
        <br>

        <div class="col-md-12 learning">

            {{ Rmt::alertError() }}

            <div class="forum" style="display: none">

                <div id="konten-forum"></div>

                <div class="forum-footer">
                    <button class="toggle-forum btn btn-theme btn-sm"><i class="fa fa-times"></i> Tutup</button>
                    <button class="btn btn-theme-inverse btn-sm" data-toggle="modal" data-target="#modal-add-topik"><i class="fa fa-comments-o"></i> Buat Topik</button>
                </div>
            </div>

            @for( $i = 0; $i <= $jml_pertemuan; $i++ )

                <!-- Buat intro default -->
                @if ( $i == 0 )
                    <section class="panel">
                        <div class="panel-body">
                            <?php Rmt::makeIntro($id_jadwal, Sia::sessionDsn(), $r->nm_mk) ?>

                            <p style="margin-top: 30px;text-align: center;"><b><i>Silahkan <a href="/dsn/profil">Update Profil</a> untuk memperbarui No. HP, Email dan Foto</i></b></p>
                        </div>
                    </section>
                @endif

                <section class="panel">
                    <header class="panel-heading">
                        @if ( $r->id_prodi == '61101' )

                            @if ( $i == 0 )
                                Intro: <small>{{ $r->nm_mk }} - {{ $r->kode_kls }}</small>
                            @else
                                Pertemuan <strong>{{ $i }}</strong>
                            @endif

                        @else

                            @if ( $r->jenis == 1 )

                                @if ( $i == 0 )
                                    Intro: <small>{{ $r->nm_mk }} - {{ $r->kode_kls }}</small>
                                @elseif ( $i == 8 )
                                    <strong>Ujian Tengah Semester</strong>
                                @elseif ( $i == 16 )
                                    <strong>Ujian Akhir Semester</strong>
                                @else
                                    @if ( $jml_pertemuan > 14 && $i > 7 )
                                        Pertemuan <strong>{{ $i - 1 }}</strong>
                                    @else
                                        Pertemuan <strong>{{ $i }}</strong>
                                    @endif
                                @endif

                            @else

                                @if ( $i == 0 )
                                    Intro: <small>{{ $r->nm_mk }} - {{ $r->kode_kls }}</small>
                                @else
                                    @if ( $jml_pertemuan > 14 && $i > 7 )
                                        Pertemuan <strong>{{ $i - 1 }}</strong>
                                    @else
                                        Pertemuan <strong>{{ $i }}</strong>
                                    @endif
                                @endif

                            @endif

                        @endif

                        <div class="btn-add pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-plus"></i> Tambah <span class="caret"></span> </button>
                                <ul class="dropdown-menu custom-dropdown align-xs-left" role="menu" style="background-color: #0075b0">
                                    <li><a href="{{ route('dsn_lms_add_materi', ['id' => $r->id, 'prt' => $i]) }}">Materi</a></li>
                                    <li><a href="{{ route('dsn_lms_tugas_add', ['id' => $r->id, 'prt' => $i]) }}">Tugas</a></li>
                                    <li><a href="{{ route('kuis_add', ['id' => $r->id, 'prt' => $i]) }}">Kuis</a></li>
                                    <li><a href="{{ route('video_add', ['id' => $r->id, 'prt' => $i]) }}">Video</a></li>
                                    <li><a href="{{ route('dsn_lms_catatan_add', ['id' => $r->id, 'prt' => $i]) }}">Catatan</a></li>
                                </ul>
                            </div>
                        </div>
                    </header>

                    <div class="panel-body" style="padding: 3px 3px;min-height: 100px">
                        <form action="{{ route('dsn_lms_upload_file') }}" enctype="multipart/form-data" class="dropzone dropzone-{{ $i }}" method="post" id="dropzone{{ $i }}">
                            <div class="fallback">
                            </div>
                            <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                            <input type="hidden" name="pertemuan" value="{{ $i }}">
                            {{ csrf_field() }}

                            <?php $data = Sia::getResources($i, $r->id); ?>

                            @if ( count($data) > 0 )
                                <div id="sortable-<?= $i ?>">

                                    @foreach( $data as $res )

                                        <div class="col-md-12 container-resources" id="urut-<?= $res->id ?>">

                                            <label class="container-cek">
                                                <input type="checkbox" class="tanda-<?= $i ?>" value="<?= $res->id ?>" onchange="tanda('{{ $i }}')" style="float:left; margin-right: 10px">
                                                <span class="checkmark"></span>
                                            </label>

                                            @if ( $res->jenis == 'materi' )
                                            {{ Log::error(json_encode($res->id_resource)) }}
                                                {{ Log::error(json_encode($res->file)) }}
                                                <a href="{{ route('dsn_lms_materi_view', ['id_materi' => $res->id_resource, 'id_dosen' => 
                                                Sia::sessionDsn(), 'file' => $res->file ]) }}" target="_blank">
                                                    
                                            @elseif( $res->jenis == 'tugas' )
                                                <a href="{{ route('dsn_lms_tugas_detail', ['id_jadwal' => $r->id, 'id' => $res->id_resource]) }}">
                                            @elseif ( $res->jenis == 'kuis' )
                                                <a href="{{ route('kuis_detail', ['id_jadwal' => $r->id, 'id' => $res->id_resource]) }}">
                                            @elseif ( $res->jenis == 'video' )
                                                <a href="{{ route('video_detail', ['id_jadwal' => $r->id, 'id_video' => $res->id_resource]) }}">
                                            @else
                                                <a class="catatan">
                                            @endif

                                                @if ( $res->jenis != 'catatan' )
                                                    <div class="icon-resources">
                                                        <?php $icon = $res->jenis == 'materi' ? Rmt::icon($res->file) : $res->file; ?>
                                                        <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                                    </div>
                                                    {{ $res->judul }}

                                                    @if ( $res->jenis2 == 'ujian' )
                                                        &nbsp; <span class="label label-danger">Ujian</span>
                                                    @endif

                                                @endif

                                                @if ( !empty($res->deskripsi) && $res->jenis != 'tugas' )
                                                    @if ( $res->jenis != 'kuis' && $res->jenis != 'video' )
                                                        <br>
                                                        {!! $res->deskripsi !!}
                                                    @endif
                                                @endif
                                            </a>
                                            <span class="btn-aksi pull-right">
                                                <?php
                                                    if ( $res->jenis == 'materi' ) {
                                                        $link_edit = route('dsn_lms_materi_edit', ['id' => $r->id]).'?id_materi='.$res->id_resource;
                                                    } elseif ( $res->jenis == 'tugas' ) {
                                                        $link_edit = route('dsn_lms_tugas_edit', ['id' => $r->id]).'?id_tugas='.$res->id_resource;
                                                    } elseif ( $res->jenis == 'catatan' ) {
                                                        $link_edit = route('dsn_lms_catatan_edit', ['id' => $r->id, 'id_catatan' => $res->id_resource]);
                                                    } elseif ( $res->jenis == 'kuis' ) {
                                                        $link_edit = route('kuis_edit', ['id' => $r->id, 'id_kuis' => $res->id_resource]);
                                                    } elseif ( $res->jenis == 'video' ) {
                                                        $link_edit = route('video_edit', ['id' => $r->id, 'id_video' => $res->id_resource]);
                                                    }
                                                ?>
                                                <a href="{{ $link_edit }}" class="btn btn-default btn-xs">Ubah</a>
                                                <a href="{{ route('dsn_lms_delete_resources') }}?id={{ $res->id }}&jenis={{ $res->jenis }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs">Hapus</a>
                                            </span>
                                        </div>

                                    @endforeach

                                </div>

                            @else
                                <div id="sortable-<?= $i ?>">
                                    <span class="empty-data">Belum ada data</span>
                                </div>

                            @endif

                        </form>
                    </div>

                    <div class="panel-footer aksi-pindah-<?= $i ?> bg-theme-inverse" style="display: none">
                        Yang terpilih
                        <div class="btn-group" style="z-index: 999999">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-arrows"></i> Pindahkan ke pertemuan <span class="caret"></span> </button>
                            <ul class="dropdown-menu custom-dropdown align-xs-left" role="menu" style="background-color: #0aa699">
                                @for( $a = 0; $a <= $jml_pertemuan; $a++ )
                                    <?php if ( $a == $i ) continue ?>
                                    <li><a href="javascript:;" onclick="pindahkan('{{ $i }}', '{{ $a }}')"><?= $a == 0 ? 'Intro': 'Pertemuan '.$a ?></a></li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                </section>

            @endfor

        </div>
    </div>
</div>

<div id="modal-file" class="modal fade" data-width="700" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Dokumen Saya</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Semua file materi yang pernah anda masukkan akan berada disini.<br>
                    <p><b>Klik pada file untuk memilih</b></p>
                </div>
                <?php
                    $id_dosen = Sia::sessionDsn();

                    $materi = DB::table('lms_bank_materi')
                            ->where('id_dosen', $id_dosen)
                            ->orderBy('id', 'desc')
                            ->get();
                ?>
                @if ( count($materi) == 0 )
                    <p>Anda belum memimiliki file</p>
                @endif

                <div class="table-responsive list-materi">
                    <table class="table table-hover">
                    @foreach( $materi as $mt )
                        <?php $icon = Rmt::icon($mt->file) ?>
                        <tr onclick="pilih('{{ $mt->id }}', '{{ $r->id }}')">
                            <td width="48">
                                <img width="100%" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                            </td>
                            <td class="judul">
                                {{ $mt->file }}<br>
                                <small>{{ Carbon::parse($mt->created_at)->format('d-m-Y H:i') }}</small>
                            </td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- //modal-body-->
    <div class="modal-footer">
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-default">Tutup</button>
        </center>
    </div>
</div>

<div id="modal-panduan" class="modal fade" data-width="700" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Panduan E-Learning</h4>
    </div>

    <div class="modal-body" style="padding-top: 0">
        <iframe width="660" height="405" src="https://www.youtube.com/embed/YpVGg7ZPOQM" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<div id="modal-add-topik" class="modal fade" data-width="700" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="myModalLabel">Buat Topik</h4>
    </div>

    <div class="modal-body" style="padding-top: 0">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('dsn_lms_topik_store') }}" method="post" id="form-add-topik">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" class="form-control" name="judul">
                    </div>
                    <div class="form-group">
                        <label>Konten</label>
                        <div>
                        <textarea name="konten" class="form-control" rows="10" cols="80"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-sm" id="btn-submit-add-topik"><i class="fa fa-save"></i> Simpan</button>
                        <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal-edit-topik" class="modal fade" data-width="700" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Ubah Topik</h4>
    </div>

    <div class="modal-body" style="padding-top: 0;height: 340px">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('dsn_lms_topik_update') }}" method="post" id="form-edit-topik">
                    <div id="edit-konten"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal-undang" class="modal fade md-stickTop" data-width="600"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="myModalLabel">Masukkan Mahasiswa ke dalam kelas</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body" style="padding-top: 0;min-height: 200px">
        <div class="row">
            <div class="col-lg-12">
                <label class="control-label">Cari Mahasiswa</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="cari-mhs" placeholder="Masukkan Kata kunci NIM atau Nama">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-cari-mhs" type="button">&nbsp; &nbsp; &nbsp; <i class="fa fa-search"></i> Cari&nbsp; &nbsp; &nbsp; </button>
                    </span>
                </div>
                <hr>
                <div id="konten-cari-mhs"></div>
            </div>
        </div>
    </div>
</div>

<div id="md-peserta-undangan" class="modal fade md-stickTop" data-width="700" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="myModalLabel">Kelas Undangan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <!-- <div class="row"> -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" data-provide="data-table">
                    <thead class="custom">
                        <tr>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th width="40px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $peserta_undangan as $pu )
                            <tr>
                                <td>{{ $pu->nim }}</td>
                                <td>{{ $pu->nm_mhs }}</td>
                                <td>{{ $pu->nm_prodi }} - {{ $pu->jenjang }}</td>
                                <td>
                                    <span class="tooltip-area">
                                    <a href="{{ route('dsn_lms_approval_mhs', [$id_jadwal, $pu->id_peserta]) }}?approv=0"
                                        title="Keluarkan"
                                        class="btn btn-danger btn-xs"
                                        onclick="return confirm('Anda ingin mengeluarkan mahasiswa ini?')">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        <!-- </div> -->
    </div>
</div>

<div id="md-notification" class="modal fade md-stickTop bg-danger" tabindex="-1" data-width="500">
    <div class="modal-header bd-danger-darken" style="background: #d9534f !important;color: #fff">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title"><i class="fa fa-bell-o"></i> Pemberitahuan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body" style="padding:0">
        <div class="widget-im notification">
            <ul>
                @if ( count($undangan) > 0 )

                @foreach( $undangan as $ud )
                <li>
                    <section class="thumbnail-in">
                        <div class="widget-im-tools tooltip-area pull-right">
                            <span style="font-size: 20px;">
                                <a href="javascript:void(0)" class="im-action" data-toggle="tooltip" data-placement="left" title="Aksi"><i class="fa fa-caret-square-o-right" style="color: #098e83"></i></a>
                            </span>
                        </div>
                        <h4>Membutuhkan persetujuan anda</h4>
                        <div class="im-thumbnail bg-theme-inverse"><i class="fa fa-check"></i></div>
                        <div class="pre-text">{{ $ud->nm_mhs }} - {{ $ud->nim }} meminta bergabung</div>
                    </section>
                    <div class="im-confirm-group">
                        <div class=" btn-group btn-group-justified">
                            <a class="btn btn-inverse" href="{{ route('dsn_lms_approval_mhs', [$id_jadwal, $ud->id_peserta]) }}?approv=1" onclick="return confirm('Anda akan memasukkan mahasiswa ini dalam kelas anda?')">Setujui</a>
                            <a class="btn btn-theme im-confirm" href="{{ route('dsn_lms_approval_mhs', [$id_jadwal, $ud->id_peserta]) }}?approv=0" onclick="return confirm('Anda menolak mahasiswa ini?')">Tolak</a>
                        </div>
                    </div>
                </li>
                @endforeach

                @endif
            </ul>
        </div>
        <!-- //widget-im-->
    </div>
    <!-- //modal-body-->
</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>
<script>

    $(function(){

        $('table[data-provide="data-table"]').dataTable({
            "order": [[ 0, 'asc' ]]
        });

        $('table[data-provide="data-table"]').attr('style', 'width: 100%');
        $('.dataTables_scrollHeadInner').attr('style', 'width: 100%');

        @if ( Session::has('success') )
            setTimeout(() => {
                showSuccess('<?= Session::get('success') ?>');
            }, 100);
        @endif

        @if ( Request::get('success') )
            setTimeout(() => {
                showSuccess('Berhasil menyimpan data');
            }, 100);
        @endif

        $.get('/ckfinder.php?dsn=<?= Sia::sessionDsn() ?>');

        setTimeout(() => {
            $('.icheckbox_minimal-green').attr('style', 'float:left;margin-right:10px');
        },100);

        $('.btn-cari-mhs').click(function(){
            var cari = $('#cari-mhs').val();
            var div = $('#konten-cari-mhs');

            div.html('<br><center><i class="fa fa-spinner fa-spin" style="font-size: 20px"></i></center>');

            $.ajax({
                url: '{{ route('dsn_lms_get_mhs') }}',
                data : {
                    preventCache : new Date(),
                    cari: cari,
                    id_jadwal: '<?= $id_jadwal ?>'
                },
                success: function(data){
                    div.html(data);
                },
                error: function(data,status,message){
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for ( i = 0; i < respon.length; i++ ){
                        pesan += "- "+respon[i]+"<br>";
                    }
                    if ( pesan == '' ) {
                        pesan = message;
                    }
                    showMessage2('',pesan);
                }
            });
        });

        $('#rootwizard').bootstrapWizard({
            tabClass:"nav-wizard",
            onTabShow: function(tab, navigation, index) {
               tab.prevAll().addClass('completed');
                tab.nextAll().removeClass('completed');
                if(tab.hasClass("active")){
                    tab.removeClass('completed');
                }
                var $total = navigation.find('li').length;
                var $current = index+1;

                $('#rootwizard').find('.wizard-status span').html($current+" / "+$total);
            }
        });

        $('.toggle-forum').click(function(){
            $('.toggle-forum').find('i.karet').toggleClass("fa-caret-down");
            $('.toggle-forum').find('i.karet').toggleClass("fa-caret-up");
            $('.forum').slideToggle();
        });

    });

    <?php for( $i = 0; $i <= $jml_pertemuan; $i++ ) { ?>

        Dropzone.options.dropzone<?= $i ?> = {
            maxFilesize: 16,
            dictDefaultMessage: 'Lepaskan file untuk mengupload',
            acceptedFiles: fileAccept(),
            success: function(file, response)
            {
                appendData('<?= $i ?>',file.name, response.id, response.id_materi);
                this.removeAllFiles();
            },
            error: function(file, response)
            {
                showMessage2('',response);
                this.removeAllFiles();
                return false;
            }
        };

        $( "#sortable-<?= $i ?>" ).sortable({
            update: function(event, ui) {
                var urutan = $(this).sortable('serialize');
                updateUrutan(urutan);
            }
        });

    <?php } ?>

    function submit(modul)
    {

        var options = {
            beforeSend: function()
            {

                $('#caplet-overlay').show();
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2(modul, data.msg);
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
                $("#close-error").show();
                showMessage2(modul, pesan);
            }
        };

        $('#form-'+modul).ajaxForm(options);
    }
    submit('add-topik');
    submit('edit-topik');

    function appendData(indeks, file, id, id_materi)
    {
        var re = /(?:\.([^.]+))?$/;
        var ext = re.exec(file)[1];
        var judul = file.split('.');
        var gambar = getIcon(judul[1]);
        var html = '<div class="col-md-12 container-resources" id="urut-'+id+'">'
                        +'<label class="container-cek">'
                            +'<input type="checkbox" class="tanda-'+indeks+'" value="'+id+'" onchange="tanda('+indeks+')" style="float:left; margin-right: 10px">'
                            +'<span class="checkmark"></span>'
                        +'</label>'
                        +'<a href="{{ route('dsn_lms_materi_view') }}/'+id_materi+'/{{ $id_dosen }}/'+file+'" target="_blank">'
                            +'<div class="icon-resources">'
                                +'<img width="24" src="{{ url('resources') }}/assets/img/icon/"'+gambar+' />'
                            +'</div>'+judul[0]+'</a>'
                        +'<div class="pull-right">'
                        +'<a href="{{ route('dsn_lms_materi_edit', ['id' => $r->id]) }}?id_materi='+id_materi+'" class="btn btn-default btn-xs">Ubah</a> '
                        +'<a href="{{ route('dsn_lms_delete_resources') }}?id='+id+'&jenis=materi" onclick="return confirm(\'Anda ingin menghapus data ini?\')" class="btn btn-danger btn-xs">Hapus</a>'
                        +'</div>'
                    +'</div>';

        $('#sortable-'+indeks).append(html);

        $('.empty-data').hide();
    }

    function updateUrutan(data)
    {
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '{{ route('dsn_lms_update_urutan') }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            data : {
                preventCache : new Date(),
                urutan:data,
            },
            success: function(data){
            },
            error: function(data,status,msg){
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('',pesan);
            }
        });
    }

    function openDokumen()
    {
        var select = $('#pertemuan-ke');
        var pertemuan = select.val();

        if ( pertemuan === '' ) {
            showMessage2('', 'Silahkan pilih pertemuan terlebih dahulu.');
        } else {
            $('#modal-file').modal('show');
        }
    }

    function pilih(id, id_jadwal)
    {

        var pertemuan = $('#pertemuan-ke').val();
        var token = $('meta[name="csrf-token"]').attr('content');
        $('#caplet-overlay').show();

        $.ajax({
            url: '{{ route('dsn_lms_upload_materi') }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            data : {
                preventCache : new Date(),
                id: id,
                pertemuan:pertemuan,
                id_jadwal:id_jadwal,
            },
            success: function(data){
                $('#caplet-overlay').hide();
                var file = '';
                var potong = data.file.split('.');

                for( var i = 0; i < potong.length; i++ ) {
                    if ( i === ( potong.length -1 ) ) {
                        file += '.'+potong[i];
                    } else {
                        file += potong[i];
                    }
                }

                appendData(pertemuan, file, data.id, data.id_materi);
                showSuccess('Berhasil menambah materi');
            },
            error: function(data,status,msg){
                $('#caplet-overlay').hide();
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = msg;
                }
                showMessage2('',pesan);
            }
        });

        $('#modal-file').modal('hide');
    }

    function tanda(indeks)
    {
        var id = [];
        $(".tanda-"+indeks+":checkbox:checked").each(function(){
            id.push($(this).val());
        });

        if ( id.length === 0 ) {
            $('.aksi-pindah-'+indeks).hide();
        } else {
            $('.aksi-pindah-'+indeks).show();
        }
    }

    function pindahkan(indeks, tujuan)
    {
        var id = [];
        $(".tanda-"+indeks+":checkbox:checked").each(function(){
            id.push($(this).val());
        });

        if ( id.length === 0 ) {
            showMessage2('', 'Tidak ada data yang bisa dipindahkan. Mohon muat ulang halaman ini.');
            return;
        }

        $('#caplet-overlay').show();

        $.ajax({
            url: '{{ route('dsn_lms_pindah_pertemuan') }}',
            data : {
                data: JSON.stringify(id),
                pertemuan:tujuan
            },
            success: function(data){
                window.location.reload();
            },
            error: function(data,status,msg){
                $('#caplet-overlay').hide();
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = msg;
                }
                showMessage2('',pesan);
            }
        });
    }

    function getTopik(id_jadwal)
    {

        $('#konten-forum').html('<i class="fa fa-spinner fa-spin"></i>')
        $.ajax({
            url: '{{ route('dsn_lms_topik') }}',
            method: 'get',
            data : {
                preventCache : new Date(),
                id_jadwal:id_jadwal,
            },
            success: function(data){
                $('#konten-forum').html(data);
            },
            error: function(data,status,msg){
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = msg;
                }

                var error = '<div style="color: red">'+pesan+'. <a href="javascript:;" onclick="getTopik(\''+id_jadwal+'\')"><i class="fa fa-refresh"></i> Refresh</a></div>';
                $('#konten-forum').html(error);
            }
        });
    }

    function ubahTopik(id, judul, konten){
        $('#modal-edit-topik').modal('show');
        $('#edit-konten').html('<center><br><i class="fa fa-spinner fa-spin"></i></center>');

        $.get('{{ route('dsn_lms_topik_edit') }}/'+id, function(data){
            $('#edit-konten').html(data);
        }).fail(function(){
            showMessage2('', 'Terjadi kesalahan, mohon muat ulang halaman ini');
        });
    }

    getTopik('<?= $id_jadwal ?>');

    function gabung(id)
    {
        if ( confirm('Anda ingin memasukkan mahasiswa ini?') ) {
            window.location.href="{{ route('dsn_lms_undang_mhs') }}/{{ $id_jadwal }}/"+id;
        }
    }
</script>
@endsection
