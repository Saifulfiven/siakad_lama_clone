@extends('layouts.app')

@section('title','Detail Pembayaran Mahasiswa')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>PEMBAYARAN</a></li>
    </ul>
@endsection

@section('content')
    <div id="overlay"></div>

    <div id="content">
    
        <div class="row">
                
            <div class="col-md-6">
                <section class="panel">
                    <header class="panel-heading">
                        Pembayaran semester mahasiswa
                    </header>

                    <div class="panel-body">

                        <div class="col-md-12" style="padding: 0">
                            <?php
                                $smt_mulai = $mhs->semester_mulai > Rmt::smtMulaiOnTunggakan() ? $mhs->semester_mulai : Rmt::smtMulaiOnTunggakan();
                                $tunggakan = Sia::tunggakan($mhs->id_mhs_reg, $smt_mulai, Sia::sessionPeriode('berjalan')); ?>

                            @if ( $tunggakan > 0 )
                                <div class="alert bg-danger">
                                    <p>Tunggakan Pembayaran Anda <b>Rp {{ Rmt::rupiah($tunggakan) }}</b></p>
                                    <p>Untuk membayar tunggakan anda pilih opsi "BAYAR TUNGGAKAN" pada saat melakukan request pembayaran/Melakukan Pembayaran.</p>
                                </div>
                            @endif

                            <?php

                                $potongan = Sia::totalPotonganPerMhs($mhs->id_mhs_reg, $mhs->semester_mulai, Request::get('smt'));

                                $total_bayar = 0;
                                $sisa_bayar = 0;
                                $total_tagihan = 0;

                                ?>
                            <div class="table-responsive">
                                <!-- Data mahasiswa -->
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                    <tbody class="detail-mhs">
                                        <tr>
                                            <th width="130px">Nama</th>
                                            <td>: {{ $mhs->nm_mhs }} </td>
                                        </tr>
                                        <tr>
                                            <th width="130px">NIM</th>
                                            <td>: {{ $mhs->nim }}</td>
                                        </tr>
                                        <tr>
                                            <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Semester</th><td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai, Request::get('smt')) }}</td>
                                        </tr>
                                        <th>Tahun Akademik</th>
                                        <td>:
                                            <select class="form-custom" onchange="ubahSmt(this.value)">
                                                @foreach( $semester as $s )
                                                    <option value="{{ $s->id_smt }}" {{ Request::get('smt') == $s->id_smt ? 'selected':'' }}>{{ $s->nm_smt }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

                @if ( $mhs->akm == 'C' || $mhs->akm == 'D' )
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                Tidak ada data pembayaran ditampilkan, mahasiswa ini sedang CUTI atau DOUBLE DEGREE pada semester ini
                            </div>
                        </div>
                    </div>

                @else

                    <div class="col-md-6">
                        <button class="btn btn-sm btn-theme" id="btn-add-bayar">
                            <i class="fa fa-plus"></i> Lakukan Pembayaran
                        </button>
                        <hr style="margin: 10px">

                        {{ Rmt::AlertSuccess() }}
                        {{ Rmt::AlertError() }}

                        <h2 style="font-size: 18px">Menunggu <strong>Pembayaran</strong></h2>
                        <hr style="margin: 10px">
                        @if ( count($briva) > 0 )
                            @foreach( $briva as $bri )
                                <div class="well bg-theme">
                                    <div class="widget-tile" style="padding: 5px 5px 0 5px">
                                        <section style="padding: 5px">
                                            <div class="hidden-xs">
                                                <span style="position:absolute; right: 0; top: 0; font-size: 12px;text-transform: none;">
                                                    <a href="{{ route('mhs_delete_briva', ['id' => $bri->id]) }}" class="btn btn-default btn-xs" onclick="return confirm('Anda ingin membatalkan pembayaran ini? Mohon untuk tidak membatalkan transaksi apabila anda telah transfer uang')">
                                                        Batalkan Pembayaran ini
                                                    </a>
                                                </span>
                                                <span style="position:absolute; right: 0; bottom: 0; font-size: 12px;text-transform: none;">
                                                    <a href="#" data-toggle="modal" data-target="#modal-petunjuk" class="btn btn-default btn-xs">
                                                        <i class="fa fa-question-circle"></i> Cara Pembayaran
                                                    </a>
                                                </span>
                                            </div>

                                            <h5>
                                                <?php
                                                    if ( $bri->jenis_bayar == 0 ) {
                                                        $jenis = 'Biaya Kuliah';
                                                    } elseif ( $bri->jenis_bayar == 88 ) {
                                                        $jenis = 'Tunggakan';
                                                    } elseif ( $bri->jenis_bayar == 99 ) {
                                                        $jenis = 'Semester Pendek';
                                                    } else {
                                                        $getJns = DB::table('jenis_pembayaran')
                                                                    ->where('id_jns_pembayaran', $bri->jenis_bayar)
                                                                    ->first();

                                                        $jenis = $getJns->ket;
                                                    }
                                                ?>
                                                Bayar<strong> {{ $jenis }}</strong>
                                            </h5>
                                            <h2>Rp {{ Rmt::rupiah($bri->jml) }}</h2>
                                            
                                            <div style="color: #fff">
                                                <i class="fa fa-bell-o"></i> 
                                                Bayar Sebelum <strong>{{ Rmt::tgl_indo($bri->exp_date) }}
                                                {{ $bri->exp_date->format('H:i') }} WITA</strong>
                                                <p><i class="fa fa-cloud"></i> 
                                                    Nomor Virtual Account <strong>76266-{{ $bri->cust_code }}</strong></p>
                                            </div>

                                            <div class="visible-xs">
                                                <a href="{{ route('mhs_delete_briva', ['id' => $bri->id]) }}" class="btn btn-default btn-xs pull-right" onclick="return confirm('Anda ingin membatalkan pembayaran ini?')">
                                                    Batalkan Pembayaran ini
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#modal-petunjuk" class="btn btn-default btn-xs pull-left">
                                                    <i class="fa fa-question-circle"></i> Cara Pembayaran
                                                </a>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert bg-success">
                                Tidak ada hasil
                            </div>
                        @endif
                    </div>

                    <div class="col-md-12">
                        <section class="panel">
                            <div class="panel-body">
                                <div class="tabbable">
                                    <ul class="nav nav-tabs" data-provide="tabdrop">
                                        <li class="active"><a href="#tagihan" data-toggle="tab">Tagihan</a></li>
                                        <li><a href="#pembayaran" data-toggle="tab">Pembayaran</a></li>
                                        <li><a href="#history" data-toggle="tab">History Transaksi</a></li>
                                    </ul>
                                    <div class="tab-content">
                                    
                                        <div class="tab-pane fade in active" id="tagihan">

                                            <!-- Data tagihan -->
                                            <div class="col-md-4" style="padding: 0">
                                                <table class="table table-bordered">
                                                    <thead class="custom">
                                                        <tr>
                                                            <th colspan="2">Tagihan Pembayaran Semester</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Nama Tagihan</th>
                                                            <th>Nominal</th>
                                                        </tr>
                                                    </thead>

                                                    <?php if ( !empty($tagihan) ) { ?>
                                                        <tbody>

                                                            <?php if ( Sia::posisiSemesterMhs($mhs->semester_mulai, Request::get('smt')) > 1 ) { ?>

                                                                <tr>
                                                                    <td>BPP</td>
                                                                    <td>Rp <?= Rmt::rupiah($tagihan->bpp) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Potongan</td>
                                                                    <td><?= 'Rp '.Rmt::rupiah($potongan) ?></td>
                                                                </tr>
                                                                <?php $total_tagihan = $tagihan->bpp ?>

                                                            <?php } else { ?>

                                                                <tr>
                                                                    <td>BPP</td>
                                                                    <td>Rp <?= Rmt::rupiah($tagihan->bpp) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>SPP</td>
                                                                    <td>Rp <?= Rmt::rupiah($tagihan->spp) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Seragam</td>
                                                                    <td>Rp <?= Rmt::rupiah($tagihan->seragam) ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Lain-lain</td>
                                                                    <td><?= empty($tagihan->lainnya) ? '0' : 'Rp '.Rmt::rupiah($tagihan->lainnya) ?></td>
                                                                </tr>
                                                                
                                                                @if ( !empty($potongan) )
                                                                    
                                                                    <?php $potong = \App\PotonganBiayaKuliah::where('id_mhs_reg', $mhs->id_mhs_reg)->get(); ?>
                                                                    
                                                                    @foreach( $potong as $po )
                                                                        <tr>
                                                                            <td>Potongan {{ $po->jenis_potongan }} {{ !empty($po->ket) ? '('.$po->ket.')':'' }}</td>
                                                                            <td>Rp <?= Rmt::rupiah($po->potongan) ?></td>
                                                                        </tr>
                                                                    @endforeach

                                                                @endif

                                                                <?php $total_tagihan = $tagihan->bpp + $tagihan->spp + $tagihan->seragam + $tagihan->lainnya ?>

                                                            <?php } ?>

                                                            <tr>
                                                                <td><b>TOTAL</b></td>
                                                                <td><b>Rp <?= Rmt::rupiah($total_tagihan - $potongan) ?></b></td>
                                                            </tr>
                                                            
                                                        </tbody>
                                                    <?php } else { ?>
                                                        <tr><td colspan="2">Tagihan belum diinput pada tahun masuk mahasiswa ini</td></tr>
                                                    <?php } ?>
                                                </table>
                                            </div>

                                        </div>

                                        <div class="tab-pane fade" id="pembayaran">
                                            <!-- History pembayaran -->
                                            <div class="col-md-8" style="padding: 0">
                                                <table class="table table-bordered">
                                                    <thead class="custom">
                                                        <tr>
                                                            <th colspan="5">History Pembayaran Biaya Kuliah</th>
                                                        </tr>
                                                        <tr>
                                                            <th width="10">No</th>
                                                            <th>Tanggal Bayar</th>
                                                            <th>Jenis</th>
                                                            <th>Ket</th>
                                                            <th>Jml bayar</th>
                                                        </tr>
                                                    </thead>
                                                    <?php if ( $pembayaran->count() == 0 ) { ?>
                                                        <tr><td colspan="5">Tidak ada history</td></tr>
                                                    <?php } else { ?>
                                                        <tbody align="center">

                                                            <?php $loop = 1 ?>
                                                            <?php foreach( $pembayaran as $pmb ) { ?>
                                                                <tr>
                                                                    <td>{{ $loop++ }}</td>
                                                                    <td><?= Carbon::parse($pmb->tgl_bayar)->format('d/m/Y') ?></td>
                                                                    <td>Biaya Kuliah</td>
                                                                    <td>{{ $pmb->ket }}</td>
                                                                    <td align="left">Rp <?= Rmt::rupiah($pmb->jml_bayar) ?></td>
                                                                </tr>
                                                                <?php $total_bayar += $pmb->jml_bayar ?>
                                                            <?php } ?>

                                                            <tr>
                                                                <td colspan="3" align="right" style="padding-right: 10px"><b>TOTAL</b></td>
                                                                <td align="left"><b>Rp <?= Rmt::rupiah($total_bayar) ?></b></td>
                                                            </tr>


                                                            <?php if ( !empty($tagihan) ) { ?>
                                                                <tr><td colspan="46"></td></tr>
                                                                <tr>
                                                                    <td colspan="3" align="right" style="padding-right: 10px"><b>SISA PEMBAYARAN</b></td>
                                                                    <td align="left"><b>Rp <?= Rmt::rupiah($total_tagihan - $total_bayar - $potongan) ?></b></td>
                                                                </tr>
                                                            <?php } ?>

                                                        </tbody>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                            <!-- End -->
                                        </div>

                                        <div class="tab-pane fade" id="history">
                                            <div class="col-md-8" style="padding: 0">
                                                <table class="table table-bordered">
                                                    <thead class="custom">
                                                        <tr>
                                                            <th colspan="5">History Transaksi</th>
                                                        </tr>
                                                        <tr>
                                                            <th width="10">No</th>
                                                            <th>Tanggal Bayar</th>
                                                            <th>Jenis</th>
                                                            <th>Ket</th>
                                                            <th>Jml bayar</th>
                                                        </tr>
                                                    </thead>
                                                    <?php if ( $transaksi->count() == 0 ) { ?>
                                                        <tr><td colspan="5">Tidak ada history</td></tr>
                                                    <?php } else { ?>
                                                        <tbody align="center">

                                                            <?php $loop = 1 ?>
                                                            <?php foreach( $transaksi as $tr ) { ?>
                                                                <tr>
                                                                    <td>{{ $loop++ }}</td>
                                                                    <td><?= Carbon::parse($tr->updated_at)->format('d/m/Y H:i') ?></td>
                                                                    <td align="left">
                                                                        @if ( $tr->jenis_bayar == 88 )
                                                                            Tunggakan
                                                                        @elseif ( $tr->jenis_bayar == 99 )
                                                                            Semester Pendek
                                                                        @elseif ( $tr->jenis_bayar == 0 )
                                                                            Biaya Kuliah
                                                                        @else
                                                                            {{ $tr->jenisBayar->ket }}
                                                                        @endif
                                                                    </td>
                                                                    <td align="left">{{ $tr->ket }}</td>
                                                                    <td align="left">Rp <?= Rmt::rupiah($tr->jml) ?></td>
                                                                </tr>
                                                            <?php } ?>

                                                        </tbody>
                                                    <?php } ?>
                                                </table>
                                                <a href="#" id="show-transaksi" class="pull-right">Lihat semua transaksi</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                @endif
                
        </div>
        <!-- //content > row-->
            
    </div>
    <!-- //content-->

<div id="modal-bayar" class="modal fade" data-width="500" tabindex="-1" style="top: 30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Lakukan Pembayaran</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <form action="{{ route('mhs_bayar') }}" id="form-bayar" method="post" class="form-horizontal" data-collabel="4" data-alignlabel="left">
            {{ csrf_field() }}
            <input type="hidden" name="smt" value="{{ Sia::posisiSemesterMhs($mhs->semester_mulai, Request::get('smt')) }}">
            <input type="hidden" name="jml_bayar_num" id="jml_bayar_num">
            <input type="hidden" name="tagihan" value="{{ $total_tagihan - $total_bayar - $potongan }}">
            <div class="form-group">
                <label class="control-label">Jenis Pembayaran </label>
                <div>
                    <select class="form-control" name="jenis_bayar" onchange="ubahJenis(this.value)" required="">
                        <option value="0">Biaya Kuliah</option>
                        @foreach( Sia::jenisBayar(Sia::sessionMhs('prodi')) as $jb )
                            <option value="{{ $jb->id_jns_pembayaran }}">{{ $jb->ket }}</option>
                        @endforeach
                        <option value="99">Semester Pendek</option>
                        @if ( $tunggakan > 0 )
                            <option value="88">BAYAR TUNGGAKAN</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Jumlah</label>
                <div>
                    <div class="input-group">
                        <span class="input-group-addon">Rp </span>
                        <input type="text" name="jml_bayar" required="" class="form-control" placeholder="Jumlah Pembayaran">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Keterangan</label>
                <div>
                    <input type="text" name="ket" class="form-control" placeholder="Keterangan">
                </div>
            </div>
            <div class="form-group offset">
                <div>
                    <button type="submit" id="btn-submit" class="btn btn-theme btn-sm">Bayar</button>
                    <button type="button" class="btn btn-sm pull-right" class="close" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modal-error" class="modal fade" tabindex="-1" style="top: 30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Terjadi kesalahan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="ajax-message"></div>
        <br>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
        </center>
    </div>
    <!-- //modal-body-->
</div>

<div id="modal-transaksi" class="modal fade" data-width="700" tabindex="-1" style="height: 600px;max-height: 600px;overflow-y: scroll;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">History Transaksi</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="custom">
                    <tr>
                        <th colspan="5">History Transaksi</th>
                    </tr>
                    <tr>
                        <th width="10">No</th>
                        <th>Tanggal Bayar</th>
                        <th>Jenis</th>
                        <th>Ket</th>
                        <th>Jml bayar</th>
                    </tr>
                </thead>
                <tbody class="konten-transaksi">
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
        </center>
    </div>
    <!-- //modal-body-->
</div>

@foreach( $briva as $bri )
    <div id="modal-petunjuk" class="modal fade" tabindex="-1" data-width="600">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">PANDUAN PEMBAYARAN</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            @include('mahasiswa-member.pembayaran.cara-pembayaran')
        </div>
        <!-- //modal-body-->
    </div>
@endforeach

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

<script>
    $(document).ready(function(){
        $(document).on( "keyup", 'input[name="jml_bayar"]', function( event ) {

            var selection = window.getSelection().toString();
            if ( selection !== '' ) {
                return;
            }
            
            if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                return;
            }
            
            var $this = $( this );
            
            var input = $this.val();

            var input = input.replace(/[\D\s\._\-]+/g, "");
            input = input ? parseInt( input, 10 ) : 0;

            $this.val( function() {
                return ( input === 0 ) ? "" : input.toLocaleString();
            } );

            isiFieldBayar();

        });

        var btn_bayar = $('#btn-add-bayar');
        @if ( count($briva) > 0 )
            btn_bayar.click(function(){
                showMessage('Selesaikan dahulu pembayaran saat ini.');
            });
        @else
            btn_bayar.click(function(){
                $('#modal-bayar').modal('show');
            });
        @endif

        $('#show-transaksi').click(function(){
            $('.konten-transaksi').html('<tr><td colspan="5" align="center"><i class="fa fa-spinner fa-spin"></i></td></tr>');
            $('#modal-transaksi').modal('show');
            $.ajax({
                url: '{{ route('mhs_history') }}',
                success: function(result){
                    $('.konten-transaksi').html(result);
                },
                error: function(data,status,msg){
                    showMessage(msg);
                }
            });
        });

        @if ( Session::has('mail-reminder') )
            $.ajax({
                url: "{{ url('/api/mail-pembayaran-reminder') }}/{{ Session::get('mail-reminder') }}",
                data: { token: 'bf0b9355-0aad-477b-b131-b85502cd6556' },
                success: function(result){
                    console.log('Berhasil mengirim email');
                },
                error: function(data,status,msg){
                    console.log('Gagal mengirim email. '+msg);
                }
            });
        @endif

        @if ( Session::has('mail-batal') )
            $.ajax({
                url: "{{ url('/api/mail-pembayaran-batal') }}/{{ Session::get('mail-batal') }}",
                data: { token: 'bf0b9355-0aad-477b-b131-b85502cd6556' },
                success: function(result){
                    console.log('Berhasil mengirim email');
                },
                error: function(data,status,msg){
                    console.log('Gagal mengirim email. '+msg);
                }
            });
        @endif

        @if ( Session::has('mail-sukses') )
            <?php $mail_sukses = Session::get('mail-sukses'); ?>
            var nim = '{{ $mail_sukses['nim'] }}';
            var id = '{{ $mail_sukses['id'] }}';
            $.ajax({
                url: "{{ url('/api/mail-pembayaran-sukses') }}/"+nim+"/"+id,
                data: { token: 'bf0b9355-0aad-477b-b131-b85502cd6556' },
                success: function(result){
                    console.log('Berhasil mengirim email');
                },
                error: function(data,status,msg){
                    console.log("Gagal mengirim email. Nim: {{ $mail_sukses['nim'] }}. ID: {{ $mail_sukses['id'] }}"+msg);
                }
            });
        @endif

    });

    function ubahJenis(value)
    {
        if ( value == 0 ) {
            $('input[name="tagihan"]').val(<?= $total_tagihan - $total_bayar - $potongan ?>);
        } else if ( value == 88 ) {
            $('input[name="tagihan"]').val(<?= $tunggakan ?>);
        }

        if ( value == 88 ) {
            $('input[name="jml_bayar"]').val('{{ Rmt::rupiah($tunggakan) }}');
            $('input[name="jml_bayar_num"]').val('{{ $tunggakan }}');
        } else {
            $('input[name="jml_bayar"]').val('');
            $('input[name="jml_bayar_num"]').val('');
        }
    }
    function isiFieldBayar()
    {
        var jml_bayar = $('input[name="jml_bayar"]').val();
        jml_bayar = jml_bayar.replace(/\./g, '');
        $('#jml_bayar_num').val(jml_bayar);
    }

    function ubahSmt(id)
    {
        window.location.href='?smt='+id;
    }

    function showMessage(pesan)
    {
        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html(' BAYAR ');
        $('#overlay').hide();
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");

                if ( !validasi() ) {
                    return false;
                }

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

                showMessage(pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('bayar');

    function validasi()
    {
        var jml_bayar = $('#jml_bayar_num').val();
        var jenis_bayar = $('select[name="jenis_bayar"]').val();
        var sisa_bayar = <?= $total_tagihan - $total_bayar - $potongan ?>;
        if ( jenis_bayar == 0 ) {
            if ( jml_bayar > sisa_bayar ) {
                showMessage('Apabila jenis pembayaran anda Biaya Kuliah maka jumlah pembayaran tidak boleh melebihi Sisa Pembayaran anda');
                return false;
            }
        }
/*
        if ( jml_bayar < 50000 ) {
            showMessage('Minimal jumlah pembayaran adalah Rp 50.000');
            return false;
        }
*/
        @if ( $tunggakan > 0 )
            if ( jenis_bayar == 88 ) {
                var tunggakan = {{ $tunggakan }};
                if ( jml_bayar > tunggakan ) {
                    showMessage(tunggakan+' & '+jml_bayar+'Jumlah yang anda bayar melebihi jumlah tunggakan anda.');
                    return false;
                }
            }
        @endif

        return true;
    }
</script>
@endsection