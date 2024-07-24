@extends('mobile.layouts.app')

@section('title','Tambah Kuis')

@section('content')

    <?php
        $tgl_mulai = Carbon::now()->format('d F Y H:i');
        $tgl_berakhir = '';
        if ( !empty($jadwal_ujian) ) {
            $tgl_mulai = Carbon::parse($jadwal_ujian->tgl_ujian.' '.$jadwal_ujian->jam_masuk)->format('d F Y H:i');
            $tgl_berakhir = Carbon::parse($jadwal_ujian->tgl_ujian.' '.$jadwal_ujian->jam_selesai)->format('d F Y H:i');
        }
    ?>

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
                Tambah Kuis: <u>{{ $r->nm_mk }}</u>
            </header>
            <div class="panel-body" style="padding-top: 13px">

                <form action="{{ route('m_kuis_store') }}" id="form-add" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_jadwal" value="{{ $r->id }}">

                    <input type="hidden" name="pertemuan" value="{{ Request::get('prt') }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Judul <span>*</span></label>
                                <input type="text" class="form-control" name="judul">
                            </div>

                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Tanggal Mulai
                                        <span class="petunjuk" title="Peserta tidak akan dapat mengerjakan sebelum tanggal ini.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="row">
                                        <div class="input-group date col-md-12 tgl-kuis" data-picker-position="bottom-left">
                                            <input type="text" class="form-control" name="tgl_mulai" value="{{ $tgl_mulai }}">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Tanggal Akhir
                                        <span class="petunjuk" title="Jika diisi,<br>
                                            Kuis akan ditutup setelah tanggal ini">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="row">
                                        <div class="input-group date tgl-kuis col-md-12" data-picker-position="bottom-left">
                                            <input type="text" class="form-control" name="tgl_berakhir" value="{{ $tgl_berakhir }}" onchange="getWaktuKerja(this.value)">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-times"></i></button>
                                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Waktu pengerjaan
                                        <span class="petunjuk" title="Waktu untuk mengerjakan kuis dalam menit.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div class="row">
                                        <div>
                                            <div class="input-group">
                                                <input type="text" name="waktu" id="waktu" class="form-control">
                                                <span class="input-group-addon">Menit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="form-group">
                                <label class="control-label"> Petunjuk Soal (Opsional)</label>
                                <div>
                                    <textarea cols="10" id="ket" class="form-control" name="ket" rows="12"></textarea>
                                </div>
                            </div>

                            <div class="col-md-4" style="padding-left: 0">
                                <div class="form-group">
                                    <label class="control-label">Jenis Kuis
                                        <span class="petunjuk" title="Ujian : Kuis hanya akan bisa dikerjakan apabila pembayaran telah memenuhi syarat.<br>Kuis : Bisa dikerjakan tanpa bergantung pada pembayaran mahasiswa.">
                                            <i class="fa fa-question-circle"></i>
                                        </span>
                                    </label>
                                    <div>
                                        <select name="jenis" class="form-control">
                                            <option value="ujian">Ujian</option>
                                            <option value="kuis">Kuis</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Tampilan Kuis</label>
                                    <div>
                                        <select name="tampilan" class="form-control">
                                            <option value="single">Tampilkan Persoal</option>
                                            <option value="all">Tampilkan Sekaligus</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Acak Urutan Pertanyaan</label>
                                    <div>
                                        <select name="acak" class="form-control">
                                            <option value="0">Tidak</option>
                                            <option value="1">Acak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <button class="btn btn-primary btn-sm" id="btn-submit-add" onclick="CKupdate()" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Lanjutkan & Tambahkan Soal</button> &nbsp; &nbsp; 
                            <a href="javascript:void();" onclick="history.back(-1);" style="margin: 3px 3px" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Batal</a>
                        </div>

                    </div>

                </form>

            </div>

        </div>
      </div>
    </div>

@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/ckeditor-4-full/ckeditor.js"></script>
<script>

    $(function () {
        'use strict';

        $('.tgl-kuis').datetimepicker({
            bornIn:"#main",
            weekStart: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            format: "dd MM yyyy hh:ii"
        });

        CKEDITOR.replace( 'ket', {
            startupFocus : false,
            uiColor: '#FFFFFF',
            customConfig: '/resources/assets/plugins/ckeditor-4-full/custom_config.js'
        });

        @if ( !empty($tgl_berakhir) )
            var w_mulai = new Date('{{ $tgl_mulai }}');
            var w_akhir = new Date('{{ $tgl_berakhir }}');
            var waktu_kerja = diff_minutes(w_mulai, w_akhir);
            $('#waktu').val(waktu_kerja);
        @endif


        var options = {
            beforeSend: function() 
            {
                $('#caplet-overlay').show();
                $("#btn-submit-add").attr('disabled','');
                $("#btn-submit-add").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage2('add', data.msg);
                } else {
                    window.location.href="{{ route('m_kuis_detail') }}/{{ $r->id }}/"+data.id;
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
                showMessage2('add', pesan);
            }
        }; 

        $('#form-add').ajaxForm(options);
    });

    function CKupdate(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }

    function diff_minutes(dt2, dt1) 
    {
        var diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= 60;
        return Math.abs(Math.round(diff));
    }

    function getWaktuKerja(value)
    {
        var mulai = new Date('{{ $tgl_mulai }}');
        var akhir = new Date(value);

        var waktu = diff_minutes(mulai, akhir);

        $('#waktu').val(waktu);
    }

</script>
@endsection