@extends('layouts.app')

@section('title','Absensi Perkuliahan')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>ABSENSI PERKULIAHAN</a></li>
    </ul>
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <div class="pull-left" style="padding-top: 5px">
                            <b>INPUT ABSENSI</b>
                        </div>
                        <a href="javascript:;" onclick="window.history.back()" style="margin: 3px 3px" class="btn btn-success btn-xs pull-right"><i class="fa fa-list"></i> KEMBALI</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <!-- <div class="ajax-message"></div> -->
                        {{ Rmt::AlertSuccess() }}
                        {{ Rmt::AlertError() }}

                        <div class="table-responsive">
                            <table border="0" class="table table-striped">
                                <tbody class="detail-mhs">

                                    <tr>
                                        <th>Matakuliah</th>
                                        <td>: {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)</td>
                                        <th width="160px">Kelas / Ruangan</th>
                                        <td>: {{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>
                                    </tr>
                                    <tr>
                                        <th width="160px">Semester</th>
                                        <td width="400px">: {{ $r->nm_smt }}</td>
                                        <th>Program Studi</th>
                                        <td>: {{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <hr>

                <div class="row">
                    <div class="col-md-12">

                        <?php 

                        $sekarang = Carbon::now();
                        $absen_mhs = App\AbsenMhs::where('id_jdk', $id_jdk)
                                            ->where('updated_at','>', $sekarang)->first();

                        $waktu_berakhir = '';

                        ?>

                        <div class="col-md-8" style="padding: 0">

                            @if ( !empty($absen_mhs) )

                                <?php 

                                    $waktu_berakhir = $absen_mhs->updated_at;

                                ?>

                                <div class="alert alert-info">
                                    Absensi terbuka untuk <strong>pertemuan {{ $absen_mhs->pertemuan_ke }}</strong> dengan waktu <strong>{{ $absen_mhs->waktu }} menit</strong>
                                    <div class="pull-right" id="sisa-waktu"><i class="fa fa-spinner fa-spin"></i></div>
                                </div>

                            @else
                                
                                <div class="pull-left">
                                    <button class="btn btn-theme btn-sm hidden-xs" data-toggle="modal" data-target="#modal-buat-absen"><i class="fa fa-check-square-o"></i> BUKA ABSENSI MAHASISWA</button>
                                    &nbsp; 
                                </div>

                            @endif

                        </div>

                        <div class="col-md-4" style="padding: 0">

                            <div class="pull-right">
                                <a href="{{ route('dsn_absen_mhs_cetak', ['id' => $id_jdk]) }}?jenis={{ Request::get('jenis') }}"
                                    class="btn btn-primary btn-sm hidden-xs" target="_blank"><i class="fa fa-print"></i> CETAK</a>
                                &nbsp; 
                                <a class="btn btn-inverse btn-sm" data-toggle="modal" data-target="#modal-dosen"><i class="fa fa-file-text"></i> Berita Acara Perkuliahan</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="custom">
                                  <tr>
                                    <th rowspan="2" class="text-center" style="width: 5px">NO</th>
                                    <th rowspan="2" class="text-center" style="width: 40px">NIM</th>
                                    <th rowspan="2" width="300">NAMA</th>
                                    <th colspan="16" class="text-center" style="padding:1px">Pertemuan</th>
                                  </tr>
                                  <tr>

                                    <?php for ( $per = 1; $per <= $jml_pertemuan; $per++ ) { ?>
                                        <th style="padding:1px;font-size:11px" class="text-center" width="60">
                                            <?= $per ?>
                                        </th>
                                    <?php } ?>
                                  </tr>
                                </thead>

                                <tbody>

                                    @foreach( $peserta as $r )

                                      <tr>
                                        <td class="text-center"><?= $loop->iteration ?></td>
                                        <td class="text-center"><?= $r->nim ?></td>
                                        <td><?= trim(ucwords(strtolower($r->nm_mhs))) ?></td>
                                        <?php
                                        $absen = [
                                            $r->a_1,$r->a_2,$r->a_3,$r->a_4,$r->a_5,
                                            $r->a_6,$r->a_7,$r->a_8,$r->a_9,$r->a_10,
                                            $r->a_11,$r->a_12,$r->a_13,$r->a_14
                                        ];

                                        for ( $per = 1; $per <= $jml_pertemuan; $per++ ) { ?>
                                            <td style='padding:15px 1px;text-align: center !important;'>

                                                <input style="margin-right:0 !important" type="checkbox" 
                                                    data-mhs="<?= $r->id_mhs_reg ?>"
                                                    data-nil="<?= $r->id_nilai ?>"
                                                    data-pertemuan="<?= $per ?>"
                                                    name="absen[<?= $r->id_mhs_reg ?>][<?= $per ?>]"
                                                    <?= $absen[$per-1] == 1 ? 'checked':'' ?>>
                                            </td>
                                        <?php } ?>
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
    </div>


    <!-- Large modal -->
    <div id="modal-dosen" class="modal fade" data-width="800" tabindex="-1">
        <div class="modal-header">
            <h4>Berita Acara Perkuliahan
            <div class="pull-right">
                <a href="{{ route('dsn_absen_dsn_cetak', ['id' => $id_jdk]) }}?jenis={{ Request::get('jenis') }}&pst={{ count($peserta) }}" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-print"></i> Cetak</a>
                &nbsp; 
                <button class="btn btn-primary btn-xs btn-submit"><i class="fa fa-save"></i> Simpan & keluar</button>
            </div>
            </h4>
         </div>

        <div class="modal-body">
            <form id="form-dosen" action="{{ route('dsn_store_absen_dsn') }}">
            
                <div class="col-md-12">
                    <?php
                    $absen_dsn = DB::table('absen_dosen')
                                    ->where('id_jdk', $id_jdk)
                                    ->where('id_dosen', Sia::sessionDsn())
                                    ->orderBy('pertemuan')
                                    ->orderBy('id')
                                    ->groupBy('pertemuan')
                                    ->get(); ?>

                    @foreach( $absen_dsn as $r )
                        
                        <input type="hidden" name="id[]" value="{{ $r->id }}">

                        <section class="panel">
                            <div class="panel-heading xs">
                                <h4><strong>PERTEMUAN </strong> ke <strong>{{ $r->pertemuan }}</strong>
                                &nbsp;  <?= $r->masuk == 1 ? '<i class="fa fa-check" style="color: #2ecc71"></i>':'<i class="fa fa-ban" style="color: red"></i>' ?></h4>
                            </div>

                            <div class="panel-body">

                                <table border="0" width="100%">
                                    <tr>
                                        <td width="100">Tanggal</td>
                                        <td width="200">
                                            <input type="date" name="tanggal[]" class="form-control" <?= empty($r->tgl) ? "":"value='$r->tgl'" ?>>
                                        </td>
                                        <td width="100" style="padding-left:10px">Jam Masuk</td>
                                        <td width="110">
                                            <input name="jam_masuk[]" type="time" class="form-control input-small" value="<?= $r->jam_masuk ?>">
                                        </td>
                                        <td width="100" style="padding-left:10px">Jam Keluar</td>
                                        <td width="110">
                                            <input name="jam_keluar[]" type="text" class="form-control input-small" value="<?= $r->jam_keluar ?>">
                                        </td>
                                        <td></td>
                                  </tr>

                                  <tr>
                                      <td colspan="7" style="text-align:left">
                                        <br>
                                        Pokok Bahasan<br>
                                        <textarea name="bahasan[]" class="form-control" rows="4"><?= $r->pokok_bahasan ?></textarea>
                                      </td>
                                  </tr>
                                </table>

                            </div>

                        </section>

                    @endforeach

                    <button class="btn btn-primary btn-xs btn-submit-dosen pull-right"><i class="fa fa-save"></i> Simpan & keluar</button>

                </div>

            </form>
        </div>
    </div>

    <div id="modal-buat-absen" class="modal fade" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Buka Absen</h4>
        </div>
        <div class="modal-body">

            <form action="{{ route('dsn_absen_buka') }}" id="form-buka-absen" method="post" role="form">
                {{ csrf_field() }}

                <input type="hidden" name="id_jdk" value="{{ $id_jdk }}">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Pertemuan Ke</label>
                        
                        <?php 
                            $absened = App\AbsenMhs::where('id_jdk', $id_jdk)
                                        ->pluck('pertemuan_ke')->toArray();
                        ?>

                        <select class="form-control" name="pertemuan">
                            @for ( $per = 1; $per <= $jml_pertemuan; $per++ )
                                
                                <?php if ( in_array($per, $absened) ) continue ?>

                                <option value="{{ $per }}">{{ $per }}</option>
                            @endfor 
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Batas Waktu</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="waktu" value="60">
                            <span class="input-group-addon">Menit</span>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <hr>
                <button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> BATALKAN</button>
                <button type="submit" id="btn-submit-buka-absen" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
                <br>
                <br>
            </form>

        </div>
    </div>


    <div id="modal-error" class="modal fade" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">Terjadi kesalahan</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <div class="ajax-message"></div>
            <hr>
            <center>
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
            </center>
        </div>
        <!-- //modal-body-->
    </div>

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script>

    @if ( $waktu_berakhir != '' )
        // Timer
            // Set the date we're counting down to
            var countDownDate = new Date("{{ $waktu_berakhir }}").getTime();

            // Update the count down every 1 second
            var x = setInterval(function() {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            var days_ = days == 0 ? '' : days + " hari ";
            var hours_ = hours == 0 ? '00:' : hours+":";
        
              // Output the result in an element with id="demo"
              document.getElementById("sisa-waktu").innerHTML = days_ + hours_
              + minutes + ":" + seconds;
                
              // If the count down is over, write some text 
              if (distance < 0) {
                clearInterval(x);
                window.location.reload();
                document.getElementById("sisa-waktu").innerHTML = "EXPIRED";
              }
            }, 1000);

    @endif

    $(function () {
        'use strict';

        $('.btn-submit').on('click', function(){
            $('#form-dosen').submit();
        });

        $('input').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
        });

        $('input').on('ifChecked', function(){
            var mhs = $(this).data('mhs');
            var nil = $(this).data('nil');
            var abs = 1;
            var pertemuan = $(this).data('pertemuan');
            simpan(mhs,nil,abs,pertemuan);
        });

        $('input').on('ifUnchecked', function(){
            var mhs = $(this).data('mhs');
            var nil = $(this).data('nil');
            var abs = 'NULL';
            var pertemuan = $(this).data('pertemuan');
            simpan(mhs,nil,abs,pertemuan);
        });
    });

    function simpan(mhs,nil,abs,pertemuan)
    {
        $.ajax({
            url: '{{ route('dsn_store_absen_mhs') }}',
            data: { nil: nil,mhs:mhs,abs:abs,pertemuan:pertemuan },
            success: function(result){
                if ( result.error == 1 ) {
                    alert('Gagal menyimpan, coba muat ulang halaman lalu ulangi kembali');
                    console.log(result.error);
                }
                console.log(result);

            },
            error: function(err,data,msg){
                alert('Gagal menyimpan, coba muat ulang halaman lalu ulangi kembali');
                console.log(err);
            }
        });
    }

    function showMessage(pesan, modul)
    {
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');

        $('#btn-submit-'+modul).removeAttr('disabled');
        if ( modul == 'buka-absen' ) {
            $('#btn-submit-'+modul).html('<i class="fa fa-floppy-o"></i> Simpan');
        } else {
            $('#btn-submit-'+modul).html('<i class="fa fa-floppy-o"></i> Simpan & keluar');
        }
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
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
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage(pesan, modul);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('dosen');
    submit('buka-absen');
</script>
@endsection