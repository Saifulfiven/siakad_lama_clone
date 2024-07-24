@extends('mobile.layouts.app')

@section('title','Tugas Detail')


@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <div class="panel-body" style="padding-top: 13px">

                <?php
                    $jwb = App\JawabanTugas::where('id_tugas', $tugas->id)
                                ->where('id_peserta', $mhs->id)
                                ->first();

                    $kartu_ujian = DB::table('kartu_ujian')
                                ->where('id_mhs_reg', $mhs->id)
                                ->where('id_smt', $r->id_smt)
                                ->where('jenis', $jenis_ujian)
                                ->count();

                    $btn_submit = false;

                    $nilai = !empty($jwb) && $jwb->nilai != null ? $jwb->nilai : '';
                    $komentar = !empty($jwb) ? $jwb->comment : '';
                    $jawaban = !empty($jwb) ? $jwb->jawaban : '';
                    $file = !empty($jwb) ? $jwb->file : '';
                    $remaining_attempt = !empty($jwb) && !empty($tugas->max_attempt) ? $tugas->max_attempt - $jwb->attempt : $tugas->max_attempt;
                    $updated_at = !empty($jwb) ? Rmt::tgl_indo($jwb->updated_at) : '';
                    $url = !empty($jwb) ? route('m_lms_tugas_update') : route('m_lms_tugas_store');
                    $tgl_tutup = !empty($tugas->tgl_tutup) ? Rmt::tgl_indo($tugas->tgl_tutup).' '.substr($tugas->tgl_tutup, 11, 5) : '';

                    if ( !empty($tugas->mulai_berlaku) && Carbon::now() < $tugas->mulai_berlaku ) {
                        $tugas_terbuka = false;
                    } else {
                        $tugas_terbuka = true;
                    }
                ?>

                <div class="row">
                    <div class="col-md-12" style="margin-top: 20px;">
                        @if ( $tugas->jenis == 'ujian' )
                            <span class="label label-danger">U J I A N</span>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <td>Matakuliah</td>
                                    <td>: {{ $r->kode_mk .' - '.$r->nm_mk }}</td>
                                </tr>
                                <tr>
                                    <td width="150">Status</td>
                                    <td>: <?= empty($jwb) ? 'Belum dikerjakan': '<span style="color: green"><i class="fa fa-check"></i> Telah dikerjakan</span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Max. Kirim Jawaban</td>
                                    <td>: <?= empty($tugas->max_attempt) ? 'Tidak Dibatasi' : 'Hanya '.$tugas->max_attempt.' kali percobaan' ?>
                                        {{ empty($remaining_attempt) ? '' : '(Tersisa '.$remaining_attempt.' kali)' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nilai</td>
                                    <td>: @if (!empty($nilai) )
                                            <span class="badge {{ Rmt::badge($nilai) }}">
                                                {{ $nilai }}
                                            </span>
                                        @else
                                            Belum dinilai
                                        @endif

                                        @if ( !empty($komentar) )
                                            <span class="tooltip-area" style="margin-left: 10px">
                                                <button class="btn btn-info btn-transparent btn-xs" data-toggle="modal" data-target="#modal-comment" title="Lihat komentar">
                                                    <i class="fa fa-comment"></i>
                                                </button>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Mulai</td>
                                    <td>: {{ !empty($tugas->mulai_berlaku) ? Rmt::tgl_indo($tugas->mulai_berlaku).' '.substr($tugas->mulai_berlaku, 11, 5) : 'Tidak terbatas' }} </td>
                                </tr>
                                <tr>
                                    <td>Tanggal Jatuh Tempo</td>
                                    <td>: {{ !empty($tugas->tgl_berakhir) ? Rmt::tgl_indo($tugas->tgl_berakhir).' '.substr($tugas->tgl_berakhir, 11, 5) : 'Tidak terbatas' }} </td>
                                </tr>
                                @if ( $tugas_terbuka )
                                    <tr>
                                        <td>Sisa waktu</td>
                                        <td>: 
                                            @if ( !empty($tugas->tgl_berakhir) )
                                                <span id="time-remaining" style="font-size: 18px;"><i class="fa fa-spin fa-spinner"></i></span>
                                            @else
                                                Tidak terbatas
                                            @endif
                                        </td>
                                    </tr>
                                @endif

                                <tr>
                                    <td>Tanggal Tutup</td>
                                    <td>: {{ !empty($tugas->tgl_tutup) ? Rmt::tgl_indo($tugas->tgl_tutup).' '.substr($tugas->tgl_tutup, 11, 5) : 'Tidak ada' }} </td>
                                </tr>

                            </table>
                        </div>

                        <div class="alert alert-danger pesan-berakhir" style="display: none;">
                            Tugas ini telah berakhir namun anda masih bisa mengupload tugas sampai tenggat waktu yang ditentukan oleh pengajar.<br>
                            Tugas yang diupload setelah tanggal berakhir akan ditandai terlambat.
                            <?= !empty($tgl_tutup) ? '<br>Upload sebelum <b>'.$tgl_tutup.'</b>':'' ?>

                        </div>

                        @if ( $tugas_terbuka )

                            <hr>
                            
                            {!! $tugas->deskripsi !!}
                            @if ( empty($tugas->deskripsi) )
                                Tidak ada deskripsi tugas yang dimasukkan
                            @endif

                            @if ( !empty($tugas->file) )
                                <div class="icon-resources">
                                    <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ Rmt::icon($tugas->file) }}" />
                                </div>
                                <a href="{{ route('m_lms_tugas_view_attach', ['id_tugas' => $tugas->id,'id_dosen' => $tugas->id_dosen ,'file' => $tugas->file]) }}" target="_blank">{{ $tugas->file }}</a>
                            @endif
                            
                        @endif

                        @if ( !empty($jwb) )

                            @if ( !empty($jawaban) )
                                <hr>
                                <p><strong>Jawaban Kamu</strong></p>
                                <div style="border: 1px solid #999;padding: 5px 10px;margin-bottom: 10px">
                                    {!! $jawaban !!}
                                </div>
                            @endif

                            @if ( !empty($file) )
                                <hr>
                                <p><strong>File yang kamu upload {{ $updated_at }}</strong></p>
                                <a href="{{ route('m_lms_tugas_download', ['id_tugas' => $tugas->id, 'file' => $file, 'jdk' => $r->id, 'nim' => $mhs->nim]) }}" target="_blank">
                                    <div class="container-resource">
                                        <div class="icon-resources">
                                            <?php $icon = Rmt::icon($file); ?>
                                            <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                        </div>
                                        {{ $file }}
                                    </div>
                                </a>

                            @endif

                        @endif


                        <?php $tertutup = false ?>

                        @if ( !empty($tugas->tgl_tutup) && Carbon::now() >= $tugas->tgl_tutup )

                            <?php $tertutup = true ?>

                            <hr>
                            <div class="alert alert-danger">
                                Tugas ini telah tertutup
                            </div>
                        
                        @else
                            
                            @if ( !$tugas_terbuka )
                                <div class="alert alert-info">
                                    Pengiriman tugas belum terbuka
                                </div>

                            @else

                                @if ( $tugas->jenis == 'ujian' && empty($kartu_ujian) )
                                    <div class="alert alert-danger">
                                        PERINGATAN...!<br>
                                        Kartu ujian anda belum keluar, pastikan anda telah melakukan pembayaran di semester ini.<br>
                                        Untuk informasi lebih lanjut silahkan hubungi bagian keuangan.
                                    </div>

                                @else

                                    <hr>
                                    <form action="{{ $url }}" id="form-add" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="id" value="{{ !empty($jwb) ? $jwb->id : '' }}">
                                        <input type="hidden" name="jenis_pengiriman" value="{{ $tugas->jenis_pengiriman }}">
                                        <input type="hidden" name="id_jadwal" value="{{ $r->id }}">
                                        <input type="hidden" name="id_tugas" value="{{ $tugas->id }}">
                                        <input type="hidden" name="attempt" value="{{ $remaining_attempt }}">
                                        <input type="hidden" name="nim" value="{{ $mhs->nim }}">

                                        <div class="row">
                                            <div class="col-md-12">

                                                @if ( $tugas->jenis_pengiriman == 'all' || $tugas->jenis_pengiriman == 'text' )

                                                    <?php /* Jika pengiriman dibatasi dan kesempatan percobaan telah habis dan jawaban tidak kosong */ ?>
                                                    @if ( !empty($tugas->max_attempt) && empty($remaining_attempt) && !empty($jawaban) )

                                                    @else
                                                        <?php $btn_submit = true ?>
                                                        <div class="form-group">
                                                            <label class="control-label"> Jawaban</label>
                                                            <div>
                                                                <textarea cols="10" id="jawaban" class="form-control" name="jawaban" rows="12">{{ $jawaban }}</textarea>
                                                            </div>
                                                        </div>
                                                    @endif

                                                @endif

                                                @if ( $tugas->jenis_pengiriman == 'all' || $tugas->jenis_pengiriman == 'file' )
                                                    @if ( !empty($tugas->max_attempt) && empty($remaining_attempt) && !empty($file) )

                                                    @else
                                                        <?php $btn_submit = true ?>
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ !empty($jwb) ? 'Ubah File (Kosongkan jika tidak diubah)': 'Upload File' }}
                                                            </label>
                                                            <br>
                                                            <div class="col-md-6" style="padding: 0">
                                                                <div class="form-group">
                                                                    <input type="file" name="file" class="form-control">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                @endif

                                                
                                            </div>

                                            @if ( $btn_submit )
                                                <div class="col-md-12">
                                                    <hr>
                                                    <button onclick="CKupdate()" class="btn btn-primary btn-sm" id="btn-submit-add" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> Simpan</button>
                                                </div>
                                            @endif

                                        </div>

                                    </form>
                                
                                @endif

                            @endif
                        @endif

                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>


<div id="modal-comment" class="modal fade" data-width="700" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Komentar dosen terhadap tugasmu</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        {!! !empty($komentar) ? $komentar : '' !!}
    </div>
</div>

@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/ckeditor-4-full/ckeditor.js"></script>

<script>

    var options = {
        beforeSend: function() 
        {
            $('#caplet-overlay').show();
            $("#btn-submit-add").attr('disabled','');
            $("#btn-submit-add").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
        },
        success:function(data, status, message) {
            window.location.reload();
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

    @if ( !empty($tugas->tgl_berakhir) )

        // Set the date we're counting down to
        var countDownDate = new Date("{{ $tugas->tgl_berakhir }}").getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

          // Get today's date and time
          var now = new Date("{{ Carbon::now()->format('Y-m-d H:i:s') }}").getTime();
            
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
            
          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
          document.getElementById("time-remaining").innerHTML = days + " hari " + hours + " jam "
          + minutes + " menit " + seconds + " detik ";
            
          // If the count down is over, write some text 
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("time-remaining").innerHTML = "<b style='color: red'>EXPIRED</b>";
            @if ( !$tertutup )
                $('.pesan-berakhir').show();
            @endif
          }
        }, 1000);
    @endif

    @if ( Session::has('success') )
        setTimeout(() => {
            showSuccess('{{ Session::get('success') }}');
        }, 1);
    @endif

    CKEDITOR.replace( 'jawaban', {
        startupFocus : false,
        uiColor: '#FFFFFF',
        customConfig: '/resources/assets/plugins/ckeditor-4-full/custom_config.js'
    });

    function CKupdate(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }
</script>
@endsection