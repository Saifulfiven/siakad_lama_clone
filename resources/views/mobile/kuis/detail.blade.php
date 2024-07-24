@extends('mobile.layouts.app')

@section('title','Kuis Detail')


@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">

            <div class="panel-body" style="padding-top: 13px">
                {!! $kuis->ket !!}

                <?php
                    $telah_kuis = DB::table('lmsk_telah_kuis')
                            ->where('id_kuis', $kuis->id)
                            ->where('id_peserta', $mhs->id)
                            ->where('sisa_waktu',0)->count();

                    $kartu_ujian = DB::table('kartu_ujian')
                                ->where('id_mhs_reg', $mhs->id)
                                ->where('id_smt', $r->id_smt)
                                ->where('jenis', $jenis_ujian)
                                ->count();

                    $jumlah_soal = \App\Http\Controllers\mobile\MobileController::jumlahSoal($kuis->id);

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

                <div class="row">
                    <div class="col-md-12" style="margin-top: 20px;">
                        @if ( $kuis->jenis == 'ujian' )
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
                                    <td>: <?= empty($telah_kuis) ? 'Belum dikerjakan': '<span style="color: green"><i class="fa fa-check"></i> Telah dikerjakan</span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Jenis Kuis </td>
                                    <td>: {{ ucfirst($kuis->jenis) }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu Kerja</td>
                                    <td>: {{ $kuis->waktu_kerja }} Menit</td>
                                </tr>
                                <tr>
                                    <td>Jumlah Soal</td>
                                    <td>: {{ $jumlah_soal }}</td>
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

                        <div class="alert alert-danger pesan-berakhir" style="display: none;">
                            Tugas ini telah berakhir namun anda masih bisa mengupload tugas sampai tenggat waktu yang ditentukan oleh pengajar.<br>
                            Tugas yang diupload setelah tanggal berakhir akan ditandai terlambat.
                            <?= !empty($tgl_tutup) ? '<br>Upload sebelum <b>'.$tgl_tutup.'</b>':'' ?>

                        </div>
                        
                        <div class="col-md-12">
                            <hr>
                            @if ( $kuis->jenis == 'ujian' && empty($kartu_ujian) )
                                <div class="alert alert-danger">
                                    PERINGATAN...!<br>
                                    Kartu ujian anda belum keluar, pastikan anda telah melakukan pembayaran di semester ini.<br>
                                    Untuk informasi lebih lanjut silahkan hubungi bagian keuangan.
                                </div>

                                <a href="" class="btn btn-primary" disabled=""><i class="fa fa-pencil"></i> Kerjakan Sekarang</a>
                            @else

                                @if ( $kuis_tertutup )

                                    <div class="alert alert-danger">
                                        Waktu Kuis telah berakhir
                                    </div>

                                @else

                                    @if ( $kuis_terbuka == false )
                                        <div class="alert alert-warning">
                                            Kuis/Ujian belum terbuka
                                        </div>
                                        <a href="" class="btn btn-primary" disabled=""><i class="fa fa-pencil"></i> Kerjakan Sekarang</a>
                                    @else

                                        @if ( $jumlah_soal == 0 )
                                            <div class="alert alert-danger">
                                                Kuis/Ujian belum memiliki soal. Silahkan tunggu dosen anda selesai membuat soal
                                            </div>
                                            <a href="" class="btn btn-primary" disabled=""><i class="fa fa-pencil"></i> Kerjakan Sekarang</a>
                                        @else
                                            
                                            @if ( $telah_kuis > 0 )
                                                <div class="alert alert-info">
                                                    Anda telah mengerjakan kuis ini.
                                                </div>
                                            @else
                                                <!-- KERJA -->
                                                <a href="{{ route('m_kerja_kuis', ['id_jdk' => $r->id, 'id_kuis' => $kuis->id,'nim' => $mhs->nim]) }}" class="btn btn-primary kerja-kuis btn-loading"><i class="fa fa-pencil"></i> Kerjakan Sekarang</a>
                                            @endif

                                        @endif

                                    @endif
                                @endif

                            @endif
                        </div>

                    </div>
                </div>
            </div>

        </div>
      </div>
    </div>

@endsection

@section('registerscript')

<script>
    $(function () {
        'use strict';
        @if ( !empty($kuis->tgl_tutup) )

            // Set the date we're counting down to
            var countDownDate = new Date("{{ $kuis->tgl_tutup }}").getTime();

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
                $('.kerja-kuis').attr('disabled','');
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
              var now2 = new Date("{{ Carbon::now()->format('Y-m-d H:i:s') }}").getTime();
                
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
    });
</script>
@endsection