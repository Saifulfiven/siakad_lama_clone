@extends('layouts.app')

@section('title', $topik->judul)

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>{{ $r->kode_mk .' - '.$r->nm_mk }}</a></li>
    </ul>
@endsection

@section('content')
    <ol class="breadcrumb">
        <li><a href="{{ route('mhs_lms') }}">Matakuliah</a></li>
        <li><a href="{{ route('mhs_lms_detail', ['id_jdk' => $r->id, 'id_dosen' => $topik->id_dosen]) }}">{{ $r->kode_mk .' - '.$r->nm_mk }}</a></li>
        <li class="active">Diskusi: {{ $topik->judul }}</li>
    </ol>

    <div id="content">
      <div class="row">
        <div class="col-md-12" style="padding-bottom: 30px">
            <h4 class="font-bold" style="margin-bottom: 10px">{{ $topik->judul }}</h4>

            <section class="panel reply">
                <div class="panel-body">
                    <?= nl2br($topik->konten) ?>
                </div>
                <div class="panel-footer">
                    <div class="thread-info-avatar">
                        <img src="http://siakad.test/resources/assets/img/avatar.png" class="img-circle w-6 rounded-full mr-3">
                    </div>
                    <div class="text-gray-600">
                        <a class="text-green-darker mr-2">
                            <?php if ( $topik->creator == $topik->id_dosen ) { ?>
                                Anda
                            <?php } else {
                                $mhs = DB::table('mahasiswa_reg as m1')
                                        ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                                        ->select('m1.nim', 'm2.nm_mhs')
                                        ->where('m1.id', $topik->creator)
                                        ->first();
                                echo !empty($mhs) ? $mhs->nm_mhs.' - '.$mhs->nim : '-'; ?>
                            <?php } ?>
                        </a> 
                        <?= Rmt::WaktuLalu($topik->created_at) ?>

                    </div>
                </div>
            </section>

            <div id="konten-reply"> 
                @foreach( $topik->jawaban as $val )
                    <section class="panel reply reply-<?= $val->id ?>">
                        <div class="panel-body" style="padding: 10px" id="konten-reply-<?= $val->id ?>">
                            @if ( $val->is_deleted )
                                <i class="fa fa-times" style="color: red"></i> <small><i>Konten ini telah dihapus</i></small>
                            @else
                                <?= nl2br($val->konten) ?>
                            @endif
                        </div>
                        <div class="panel-footer">
                            <div class="thread-info-avatar">
                                <img src="http://siakad.test/resources/assets/img/avatar.png" class="img-circle w-6 rounded-full mr-3">
                            </div>
                            <div class="text-gray-600">
                                <a class="text-green-darker mr-2">
                                    <?php if ( $val->people == 'dsn' ) { ?>
                                        Dosen
                                    <?php } else {
                                        $mhs = DB::table('mahasiswa_reg as m1')
                                                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                                                ->select('m1.nim', 'm2.nm_mhs')
                                                ->where('m1.id', $val->id_user)
                                                ->first();
                                        echo !empty($mhs) ? $mhs->nm_mhs.' - '.$mhs->nim : '-'; ?>
                                    <?php } ?>
                                </a> 
                                <?= Rmt::WaktuLalu($val->created_at) ?>

                                <div class="pull-right">
                                    @if ( $val->is_deleted == 0 )
                                        <a href='javascript:;' data-id="{{ $val->id }}" data-konten="{{ $val->konten }}" class='edit btn btn-default btn-xs'><i class='fa fa-pencil'></i> Ubah</i></a>
                                    @endif

                                    <?php $btn_toggle = $val->is_deleted == 0 ? '<i class="fa fa-trash-o"></i> Hapus':'<i class="fa fa-refresh"></i> Kembalikan' ?>
                                    <?php $class_btn_toggle = $val->is_deleted == 0 ? 'btn-danger':'btn-warning' ?>
                                    <a href="<?= route('mhs_lms_topik_reply_toggle_delete', ['id' => $val->id, 'id_topik' => $topik->id, 'deleted' => $val->is_deleted == 1 ? 0:1, 'id_dosen' => $topik->id_dosen]) ?>" onclick="return confirm('Anda ingin <?= $val->is_deleted == 1 ? 'mengembalikan':'menghapus' ?> data ini.?')" class='btn <?= $class_btn_toggle ?> btn-xs'><?= $btn_toggle ?></a>
                                </div>
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>

            @if ( $topik->is_closed == 1 )
                <div class="alert bg-theme">
                    Topik ini telah ditutup
                </div>
            @else
                <form action="{{ route('mhs_lms_topik_reply', ['id' => $topik->id]) }}" method="post" id="form-reply">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_dosen" value="{{ $topik->id_dosen }}">
                    <div class="form-group">
                        <label>Kirim Balasan</label>
                        <textarea name="konten" class="form-control" rows="8" placeholder="Balasan"></textarea>
                    </div>
                    <div class="form-group">
                        <button id="btn-submit-reply" class="btn btn-primary"><i class="fa fa-save"></i> Kirim</button>
                    </div>
                </form>

                @if ( $topik->creator == Sia::sessionMhs() )
                    <hr>
                    <center>
                        <form action="{{ route('mhs_lms_topik_tutup', ['id' => $topik->id, 'id_dosen' => $topik->id_dosen]) }}" id="form-tutup-diskusi">
                            {{ csrf_field() }}
                            <button type="button" onclick="tutup()" class="btn btn-danger"><i class="fa fa-times"></i> Tutup Diskusi Ini</button>
                        </form>
                    </center>
                @endif
            @endif
        </div>
      </div>
    </div>

    <div id="modal-edit-reply" class="modal fade" data-width="700" tabindex="-1" style="top: 30%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Ubah Kiriman</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body" style="padding-top: 0;">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('mhs_lms_topik_reply_update') }}" method="post" id="form-edit-kiriman">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label>Konten</label>
                            <textarea name="konten" id="konten" class="form-control" rows="7"></textarea>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary btn-sm" id="btn-submit-edit-kiriman"><i class="fa fa-save"></i> Kirim</button>
                            <button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
                        </div>
                    </form>                      
                </div>
            </div>
        </div>
    </div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    @if ( Session::has('success') )
        setTimeout(() => {
            showSuccess('<?= Session::get('success') ?>');
        }, 100);
    @endif

    $('.edit').click(function(){
        var id = $(this).data('id');
        var konten = $(this).data('konten');
        $('#id').val(id);
        $('#konten').text(konten);
        $('#modal-edit-reply').modal('show');
    });

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#caplet-overlay').show();
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Mengirim...");
            },
            success:function(data, statusText, xhr, $form) {
                $('#caplet-overlay').hide();
                $("#btn-submit-"+modul).removeAttr('disabled');
                $("#btn-submit-"+modul).html('<i class="fa fa-save"></i> Kirim');
                

                if ( data.length === 0 ) {

                    $('#modal-edit-reply').modal('hide');
                    var id = $('#form-'+modul+' #id').val();
                    var konten = $('#form-'+modul+' #konten').val();
                    $('#konten-reply-'+id).html(konten);
                    $('.reply-'+id).find('a.edit').attr('data-konten', konten);

                    showSuccess('Berhasil menyimpan data');

                } else {
                    $('#form-'+modul).resetForm();
                    var konten = '<section class="panel reply">'
                                    +'<div class="panel-body">'+
                                        data.konten 
                                    +'</div>'
                                    +'<div class="panel-footer">'
                                        +'<div class="thread-info-avatar">'
                                            +'<img src="http://siakad.test/resources/assets/img/avatar.png" class="img-circle w-6 rounded-full mr-3">'
                                        +'</div>'
                                        +'<div class="text-gray-600">'
                                            +'<a class="text-green-darker mr-2"> Anda</a> Baru saja'
                                            +'<div class="pull-right">'
                                                +'<a href="javascript:;" data-id="'+data.id+'" data-konten="'+data.konten+'" class="edit btn btn-default btn-xs"><i class="fa fa-pencil"></i> Ubah</i></a>'
                                                +' <a href="'+data.delete_url+'" onclick="return confirm(\'Anda ingin menghapus data ini.?\')" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Hapus</a>'
                                            +'</div>'
                                        +'</div>'
                                    +'</div>'
                                +'</section>';
                    $('#konten-reply').append(konten);
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

                showMessage2(modul, pesan);
                $("#btn-submit-"+modul).html("<i class='fa fa-save'></i> Kirim");
            }
        };

        $('#form-'+modul).ajaxForm(options);
    }

    submit('reply');
    submit('edit-kiriman');

    function tutup()
    {
        if ( confirm('Anda ingin menutup/menandai sebagai selesai diskusi ini? Perubahan tidak akan bisa dikembalikan.') ) {
            $('#form-tutup-diskusi').submit();
        }
    }
</script>
@endsection