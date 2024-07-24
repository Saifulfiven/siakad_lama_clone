@extends('mobile.layouts.app')

@section('title','Kuis Detail')

@section('heading')
<style>
    #content {
        font-size: 15px !important;
    }
    .jawaban {
        position: relative;
        padding: 5px 5px 5px 20px;
    }
    .jawaban .simbol {
        position: absolute;
        top: 5px;
        left: 2px;
        font-weight: bold;
    }
    .benar {
        color: #0f31ea;
    }
</style>

@endsection

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">

            {{ Rmt::alertError() }}
            @if ( Session::has('kuis') )
                <div class="alert alert-success">
                    Berhasil menyimpan kuis, silahkan menambahkan Soal dengan klik Tombol <b>"Tambah Soal"</b>.
                </div>
            @endif

            <?php
                
                $jumlah_soal = \App\Http\Controllers\mobile\MobileController::jumlahSoal($kuis->id);
                $jml_peserta_kelas = \App\Http\Controllers\mobile\MobileController::jmlPesertaKelas($r->id);
                $jml_mengerjakan = \App\Http\Controllers\mobile\MobileController::jumlahMengerjakanKuis($kuis->id);
                

                if ( !empty($kuis->tgl_tutup) && Carbon::now() >= $kuis->tgl_tutup ) {
                    $kuis_tertutup = true;
                } else {
                    $kuis_tertutup = false;
                }

                if ( Carbon::now() < $kuis->tgl_mulai ) {
                    $kuis_terbuka = false;
                } else {
                    $kuis_terbuka = true;
                }
            ?>
            <section class="panel">
                <header class="panel-heading">
                    {{ $kuis->judul }}
                    <div class="pull-right">
                        <a href="{{ route('dsnm_lms', ['id_jdk' => $r->id, 'id_dosen' => Session::get('m_id_dosen')]) }}" class="btn-loading btn btn-success btn-xs">Kembali</a>
                    </div>
                </header>
                <div class="panel-body" style="padding-top: 13px">

                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn-loading btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-plus"></i> Tambah Soal <span class="caret"></span> </button>
                            <ul class="dropdown-menu custom-dropdown align-xs-left" role="menu" style="background-color: #0075b0">
                                <li><a href="{{ route('m_kuis_add_soal', ['id' => $r->id, 'id_kuis' => $kuis->id]) }}"><i class="fa fa-plus"></i> Buat Baru</a></li>
                                <li><a href="javascript:void(0);" data-toggle="modal" data-target="#modal-bank-soal">Dari Bank Soal</a></li>
                            </ul>
                        </div>

                        
                        <a href="{{ route('m_kuis_edit', ['id' => $r->id, 'id_kuis' => $kuis->id]) }}" class="btn-loading btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Ubah Kuis</a>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    {!! $kuis->ket !!}

                    <div class="row">
                        <div class="col-md-6" style="margin-top: 20px;">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td>Jenis Kuis </td>
                                        <td>: {{ ucfirst($kuis->jenis) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tampilan Soal</td>
                                        <td>: {{ $kuis->tampilan == 'all' ? 'Tampilkan sekaligus' : 'Tampilan persoal' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Acak Soal</td>
                                        <td>: {{ $kuis->acak == '1' ? 'Ya' : 'Tidak' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Waktu Kerja</td>
                                        <td>: {{ $kuis->waktu_kerja }} Menit</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah Soal</td>
                                        <td>: {{ $jumlah_soal }} &nbsp; 
                                            <button class="btn-loading btn btn-primary btn-xs" data-toggle="modal" data-target="#modal-soal">Kelola Soal</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="150">Jumlah Peserta</td>
                                        <?php
                                            $peserta_undangan = App\PesertaUndangan::where('id_jadwal', $r->id)
                                                ->count(); ?>
                                        <td>: {{ $jml_peserta_kelas + $peserta_undangan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah mengerjakan</td>
                                        <td>: {{ $jml_mengerjakan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Mulai</td>
                                        <td>: {{ !empty($kuis->tgl_mulai) ? Rmt::tgl_indo($kuis->tgl_mulai).' '.substr($kuis->tgl_mulai, 11) : 'Tidak terbatas' }} </td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Berakhir</td>
                                        <td>: {{ !empty($kuis->tgl_tutup) ? Rmt::tgl_indo($kuis->tgl_tutup).' '.substr($kuis->tgl_tutup, 11) : 'Tidak terbatas' }} </td>
                                    </tr>
                                    @if ( $kuis_terbuka == false && !$kuis_tertutup )
                                        <tr>
                                            <td>Kuis Terbuka dalam</td>
                                            <td>: 
                                                <span id="time-remaining-2" style="font-size: 18px;"><i class="fa fa-spin fa-spinner"></i></span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr id="sisa-waktu">
                                        <td>Sisa waktu</td>
                                        <td>: 
                                            @if ( !empty($kuis->tgl_tutup) )
                                                <span id="time-remaining" style="font-size: 18px;"><i class="fa fa-spin fa-spinner"></i></span>
                                            @else
                                                Tidak terbatas
                                            @endif
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <a href="{{ route('m_kuis_jawaban', ['id_jadwal' => $r->id, 'id' => $kuis->id]) }}?act=grade" class="btn-loading btn btn-primary"><i class="fa fa-trophy"></i> Lihat Hasil Kuis</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
      </div>
    </div>

    <div id="modal-soal" class="modal fade md-stickTop" data-width="700" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title"><b>Soal Kuis</b></h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">

                    @if ( count($soal) == 0 )
                        Belum ada soal
                    @endif

                    @foreach( $soal as $so )

                        @if ( $so->jenis_soal == 'pg' )
                            <section class="panel">
                                <header class="panel-heading">
                                    <b>Soal {{ $loop->iteration }}</b>
                                    <div class="pull-right">
                                        <a href="{{ route('m_kuis_edit_soal', ['id' => $r->id, 'id_kuis' => $kuis->id, 'id_soal' => $so->id]) }}?id_kuis_soal={{ $so->id_kuis_soal }}" class="btn-loading btn btn-warning btn-xs">Ubah</a> &nbsp; 
                                        <a onclick="return confirm('Anda ingin menghapus soal ini?')" href="{{ route('m_kuis_delete_soal', ['id_kuis_soal' => $so->id_kuis_soal, 'id_jadwal' => $r->id]) }}" class="btn-loading btn btn-danger btn-xs">Hapus</a>
                                    </div>
                                </header>
                                <div class="panel-body" style="padding-top: 13px">
                                    {!! $so->soal !!}
                                    <div class="clearfix"></div>

                                    <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'a' ? 'benar':'' }}">
                                        <div class="simbol">A.</div> {{ $so->jawaban_a }}
                                    </div>
                                    <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'b' ? 'benar':'' }}">
                                        <div class="simbol">B.</div> {{ $so->jawaban_b }}
                                    </div>


                                    <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'c' ? 'benar':'' }}">
                                        <div class="simbol">C.</div> {{ $so->jawaban_c }}
                                    </div>
                                    @if ( !empty($so->jawaban_d) )
                                        <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'd' ? 'benar':'' }}">
                                            <div class="simbol">D.</div> {{ $so->jawaban_d }}
                                        </div>
                                    @endif
                                    @if ( !empty($so->jawaban_e) )
                                        <div class="col-md-6 jawaban {{ $so->jawaban_benar == 'e' ? 'benar':'' }}">
                                            <div class="simbol">E.</div> {{ $so->jawaban_e }}
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @else
                            <section class="panel">
                                <header class="panel-heading">
                                    <b>Soal {{ $loop->iteration }}</b>
                                    <div class="pull-right">
                                        <a href="{{ route('m_kuis_edit_soal', ['id' => $r->id, 'id_kuis' => $kuis->id, 'id_soal' => $so->id]) }}?id_kuis_soal={{ $so->id_kuis_soal }}" class="btn-loading btn btn-warning btn-xs">Ubah</a> &nbsp; 
                                        <a onclick="return confirm('Anda ingin menghapus soal ini?')" href="{{ route('m_kuis_delete_soal', ['id_kuis_soal' => $so->id_kuis_soal, 'id_jadwal' => $r->id]) }}" class="btn-loading btn btn-danger btn-xs">Hapus</a>
                                    </div>
                                </header>
                                <div class="panel-body" style="padding-top: 13px">
                                    {!! $so->soal !!}
                                    <div class="clearfix"></div>
                                    <b>Keyword:</b> <i>{{ $so->keyword }}</i>
                                    
                                </div>
                            </section>
                        @endif

                    @endforeach

                </div>
            </div>
        </div>
    </div>


    <div id="modal-bank-soal" class="modal fade md-stickTop" tabindex="-1" data-width="900">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title"><b>Bank Soal</b></h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body" style="max-height: 600px; overflow-y: scroll;">
            <div id="konten-bank-soal"><center><i class="fa fa-spin fa-spinner"></i></center></div>
        </div>
        <!-- //modal-body-->
    </div>
@endsection

@section('registerscript')

<script>

    $(function () {
        'use strict';
        @if ( Session::has('success') )
            showSuccess('{{ Session::get('success') }}');
        @endif

        $('#modal-bank-soal').on('show.bs.modal', function(){

            $.ajax({
                url: '{{ route('m_kuis_get_bank_soal') }}',
                data: {kode_mk: '{{ $r->kode_mk }}', id_kuis: '{{ $kuis->id }}'},
                success: function(result){
                    setTimeout(() => {
                        $('#konten-bank-soal').html(result);
                    }, 500);
                },
                error: function(data,status,msg){
                    alert(msg);
                }
            });
        });
    });


    @if ( !empty($kuis->tgl_tutup) )

        // Set the date we're counting down to
        var countDownDate = new Date("{{ $kuis->tgl_tutup }}").getTime();

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
            
          document.getElementById("time-remaining").innerHTML = days + " hari " + hours + " jam "
          + minutes + " menit " + seconds + " detik ";
            
          // If the count down is over, write some text 
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("time-remaining").innerHTML = "<b style='color: red'>EXPIRED</b>";
          }
        }, 1000);
    @endif

    @if ( $kuis_terbuka == false && !$kuis_tertutup )

        $('#sisa-waktu').hide();
        // Set the date we're counting down to
        var countDownDate2 = new Date("{{ $kuis->tgl_mulai }}").getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

          // Get today's date and time
          var now2 = new Date().getTime();
            
          // Find the distance between now2 and the count down date
          var distance2 = countDownDate2 - now2;
            
          // Time calculations for days, hours, minutes and seconds
          var days2 = Math.floor(distance2 / (1000 * 60 * 60 * 24));
          var hours2 = Math.floor((distance2 % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes2 = Math.floor((distance2 % (1000 * 60 * 60)) / (1000 * 60));
          var seconds2 = Math.floor((distance2 % (1000 * 60)) / 1000);
            
          document.getElementById("time-remaining-2").innerHTML = days2 + " hari " + hours2 + " jam "
          + minutes2 + " menit " + seconds2 + " detik ";
            
          // If the count down is over, write some text 
          if (distance2 < 0) {
            clearInterval(x);
            $('#caplet-overlay').show();
            document.location.reload();
          }
        }, 1000);

    @endif

</script>
@endsection