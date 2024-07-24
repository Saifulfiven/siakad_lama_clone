@extends('layouts.app')

@section('title','Kartu Ujian')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Kartu Ujian
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-12">
                        
                        @if ( !empty($kartu->id) )

                            <div class="kartu-ujian">

                                <div class="header-kartu">
                                    <br>
                                    <img src="{{ url('resources') }}/assets/img/logo-white.png" width="100px">
                                    <br>
                                    <br>
                                </div>

                                <div class="konten">

                                    <?php
                                        if ( !empty(Sia::sessionMhs('foto')) ) {
                                            $foto = Sia::sessionMhs('foto');
                                        } else {
                                            $foto = Sia::sessionMhs('jenkel') == 'P' ? 'user-women.png':'user-man.png';
                                        }
                                    ?>
                                    <img src="{{ url('storage') }}/foto-mahasiswa/<?= $foto ?>" width="150px">
                                    <p></p>
                                    <span>
                                        <?= Sia::sessionMhs('nama') ?><br>
                                        <?= Sia::sessionMhs('nim') ?><br>
                                    </span>
                                    <p style="padding: 10px 0 0 0">{{ $kartu->jenis.'-'.$kartu->id_smt.''.$kartu->id }}</p>
                                    
                                </div>
                                    
                                <div class="footer-kartu">
                                    <?php if ( $jenis == 'UTS' ) { ?>
                                        KARTU UJIAN TENGAH SEMESTER (UTS)<br>
                                    <?php } else { ?>
                                        KARTU UJIAN AKHIR SEMESTER (UAS)<br>
                                    <?php } ?>
                                    STIE NOBEL INDONESIA<br>
                                    
                                    TAHUN {{ Sia::sessionPeriode('nama') }}

                                </div>
                            </div>

                        @else

                            <div class="non-kartu-ujian">
                                <div style="padding-top: 190px;text-align: center;">
                                    <i class="mdi mdi-block-helper icon"></i>
                                </div>
                                <div class="footer-kartu" style="height: 30px">
                                    Kartu ujian belum keluar
                                </div>
                            </div>
                        
                        @endif

                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>

@endsection