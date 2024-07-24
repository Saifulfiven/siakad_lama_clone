@extends('layouts.app')

@section('title','Tambah Jadwal Ujian')

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a><b>Tambah Jadwal Ujian</b></a></li>
    </ul>
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
                <b>Program Studi : </b> {{ $prodi->jenjang }} {{ $prodi->nm_prodi }} &nbsp; &nbsp; &nbsp; 
                <b>Semester : </b> {{ Session::get('jdu_semester') }} &nbsp; &nbsp; &nbsp; 
                <b>Jenis Ujian : </b> {{ Session::get('jdu_jenis_ujian') }}
                <a href="{{ route('jdu') }}" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> KEMBALI</a>
            </header>
            <div class="panel-body" style="padding-top: 13px">

                {{ Rmt::AlertError() }}
                {{ Rmt::AlertSuccess() }}

                <div class="row">
                    <div class="col-md-12">

                        <div class="table-responsive">
                            <table border="0" width="100%" style="margin-bottom: 10px">
                                <tr>
                                    <td>
                                        Pilih salah satu jadwal berikut
                                    </td>

                                    <td width="300px">
                                        <form action="{{ route('jdu_add') }}" method="get" id="form-cari">
                                            <div class="input-group pull-right">
                                                <input type="text" class="form-control input-sm" name="cari" value="{{ Request::get('cari') }}">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-default btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
                                                    <button  class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>

                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Waktu</th>
                                        <th>Matakuliah</th>
                                        <th>Smstr</th>
                                        <th>Kelas /<br>Ruang</th>
                                        <th>Dosen Mengajar</th>
                                        <th>Jml Mhs</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    @foreach($jadwal as $r)
                                        <tr onclick='openModal(
                                                        "{{ $r->kode_mk }} - {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)",
                                                        "{{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}",
                                                        "{{ $r->nm_ruangan }}",
                                                        "{{ $r->dosen }}",
                                                        "{{ empty($r->terisi) ? "":$r->terisi }}",
                                                        "{{ $r->id }}")'
                                            style="cursor: pointer;">
                                            <td>{{ $loop->iteration - 1 + $jadwal->firstItem() }}</td>
                                            <td>
                                                {{ empty($r->hari) ? '-': Rmt::hari($r->hari) }} : 
                                                {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
                                            </td>
                                            <td align="left">
                                                {{ $r->kode_mk }} - {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)
                                            </td>
                                            <td>{{ $r->smt }}</td>
                                            <td>{{ $r->kode_kls }} / {{ $r->nm_ruangan }}</td>                                    <td align="left"><?= $r->dosen ?></td>
                                            <td>{{ empty($r->terisi) ? '':$r->terisi }}</td>                                </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if ( $jadwal->total() == 0 )
                                &nbsp; Tidak ada data
                            @endif

                            @if ( $jadwal->total() > 0 )
                                <div class="pull-left">
                                    Jumlah data : {{ $jadwal->total() }}
                                </div>
                            @endif

                            <div class="pull-right"> 
                                {{ $jadwal->render() }}
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
      </div>
    </div>


<div id="modal-jdu" class="modal fade container" data-width="900" tabindex="-1">
    <div class="modal-header">
        <a href="{{ route('pengawas') }}" target="_blank" class="btn btn-xs btn-warning pull-right"><i class="fa fa-plus"></i> Tambah Pengawas</a>
        <h4>Buat jadwal ujian ({{ Session::get('jdu_jenis_ujian') }})</h4>
    </div>
    <div class="modal-body">
        
        <div class="col-md-12">

            <div class="ajax-message"></div>
            <button id="close-error" class="btn btn-warning btn-xs" style="display: none">OK, Saya mengerti</button>

            <table class="table" width="100%">
                <tr>
                    <td width="90">Matakuliah </td> <td> : <span id="matakuliah">Senin</span></td>
                    <td width="90">Ruangan</td><td> : <span id="ruangan">402</span></td>
                </tr>
                <tr>
                    <td>Waktu </td> <td> : <span id="waktu">Senin</span></td>
                    <td>Dosen </td> <td> : <span id="dosen">Dosen</span></td>
                </tr>
            </table>
            <hr>
        </div>

        <form action="{{ route('jdu_store') }}" method="post" id="form-add-jdu">

            {{ csrf_field() }}
            <input type="hidden" name="id_jdk" id="id_jdk">
            <input type="hidden" id="total-peserta">

            <div class="col-md-6">
                <div class="table-responsive">
                    <table border="0" class="table-form" width="100%">

                        <tr>
                            <td width="150px">JUMLAH KELAS</td>
                            <td>
                                <select class="form-control mw-1" name="jml_kelas" id="jml-kelas">
                                    <option value="1">1 Kelas</option>
                                    <option value="2">2 Kelas</option>
                                </select>
                            </td>
                        </tr>
                        <tr><td colspan="2"><b>RUANGAN 1</b></td></tr>

                        <!-- Ruang 1  -->
                            <tr>
                                <td width="150px">JUMLAH MAHASISWA</td>
                                <td>
                                    <input type="text" class="form-control mw-1" id="jml-mhs-1" disabled="">
                                    <input type="hidden" name="jml_peserta" id="jml-mhs-1-hide">
                                </td>
                            </tr>
                            <tr>
                                <td>TANGGAL UJIAN</td>
                                <td>
                                    <input type="date" class="form-control mw-2" name="tgl_ujian" id="tgl-ujian-1">
                                </td>
                            </tr>
                            <tr>
                                <td>HARI</td>
                                <td>
                                    <select class="form-control mw-1" name="hari" id="hari-1">
                                        <option value="">-- Hari --</option>
                                        @for( $i = 1; $i <= 7; $i++ )
                                            <option value="{{ $i }}">{{ Rmt::hari($i) }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>JAM MASUK</td>
                                <td>
                                    <input type="time" name="jam_masuk" class="form-custom mw-1" id="jam-masuk-1"> s/d 
                                    <input type="time" name="jam_selesai" class="form-custom mw-1" id="jam-selesai-1">
                                </td>
                            </tr>
                            <tr>
                                <td>RUANGAN</td>
                                <td>
                                    <select class="form-control mw-2" name="ruangan">
                                        <option value="">-- Ruangan --</option>
                                        @foreach( Sia::ruangan() as $j )
                                            <option value="{{ $j->id }}">{{ $j->nm_ruangan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150px">PENGAWAS</td>
                                <td>
                                    <div style="position: relative;">
                                        <div class="input-icon right"> 
                                            <span id="spinner-autocomplete-1" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                            <input type="text" class="form-control" id="autocomplete-pengawas-1">
                                            <input type="hidden" id="pengawas-1" name="pengawas">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <!-- End -->

                    </table>
                </div>
            </div>

            <div class="col-md-6" id="ruangan-2" style="display: none">
                <div class="table-responsive">
                    <table border="0" class="table-form" width="100%">
                        <tr>
                            <td colspan="2">
                                <button type="button" id="salin" class="btn btn-xs btn-warning">Salin Data Ruang 1</button>
                                <br>
                            </td>
                        </tr>
                        <tr><td colspan="2"><b>RUANGAN 2</b></td></tr>
                        <!-- Ruang 2 -->
                            <tr>
                                <td width="150px">JUMLAH MAHASISWA</td>
                                <td>
                                    <input type="text" class="form-control mw-1" name="jml_mhs_2" id="jml-mhs-2" disabled="">
                                    <input type="hidden" name="jml_peserta_2" id="jml-mhs-2-hide">
                                </td>
                            </tr>
                            <tr>
                                <td>TANGGAL UJIAN</td>
                                <td>
                                    <input type="date" class="form-control mw-2" name="tgl_ujian_2" id="tgl-ujian-2">
                                </td>
                            </tr>
                            <tr>
                                <td>HARI</td>
                                <td>
                                    <select class="form-control mw-1" name="hari_2" id="hari-2">
                                        <option value="">-- Hari --</option>
                                        @for( $i = 1; $i <= 7; $i++ )
                                            <option value="{{ $i }}">{{ Rmt::hari($i) }}</option>
                                        @endfor
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>JAM MASUK</td>
                                <td>
                                    <input type="time" name="jam_masuk_2" class="form-custom mw-1" id="jam-masuk-2"> s/d 
                                    <input type="time" name="jam_selesai_2" class="form-custom mw-1" id="jam-selesai-2">
                                </td>
                            </tr>
                            <tr>
                                <td>RUANGAN</td>
                                <td>
                                    <select class="form-control mw-2" name="ruangan_2">
                                        <option value="">-- Ruangan --</option>
                                        @foreach( Sia::ruangan() as $j )
                                            <option value="{{ $j->id }}">{{ $j->nm_ruangan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="150px">PENGAWAS</td>
                                <td>
                                    <div style="position: relative;">
                                        <div class="input-icon right"> 
                                            <span id="spinner-autocomplete-2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                            <input type="text" class="form-control" id="autocomplete-pengawas-2">
                                            <input type="hidden" id="pengawas-2" name="pengawas_2">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <!-- End -->
                    </table>
                </div>

                <br>

            </div>
            <div class="col-md-12">
                <hr>
                <button class="btn btn-primary pull-right" id="btn-submit">Simpan</button>
                <input type="reset" class="tutup-modal btn btn-submit pull-left" id="btn-keluar" value="Batal / Keluar">
                <br>
                <br>
                <br>
            </div>
        </form>
    </div>
</div>
@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>
    $(function () {
        'use strict';

        $('#nav-mini').trigger('click');

        $('.tutup-modal').click(function(){
            $('#ruangan-2').hide();
            $('#modal-jdu').modal('hide');
        });

        $('#salin').click(function(){
            var tgl = $('#tgl-ujian-1').val();
            var hari = $('#hari-1').val();
            var jam_masuk_1 = $('#jam-masuk-1').val();
            var jam_selesai_1 = $('#jam-selesai-1').val();

            $('#tgl-ujian-2').val(tgl);
            $('#hari-2').val(hari);
            $('#jam-masuk-2').val(jam_masuk_1);
            $('#jam-selesai-2').val(jam_selesai_1);
        });

        $('#jml-kelas').change(function(){
            var jml_kls = $(this).val();
            var tot_peserta = $('#total-peserta').val();
            if ( jml_kls == 1 ) {
                $('#ruangan-2').hide();
                $('#jml-mhs-1').val(tot_peserta);
                $('#jml-mhs-1-hide').val(tot_peserta);
                $('#jml-mhs-2').val('');
                $('#jml-mhs-2-hide').val('');
            } else {
                var mhs_2 = Math.round(tot_peserta/2);
                $('#jml-mhs-1').val(mhs_2);
                $('#jml-mhs-1-hide').val(mhs_2);
                $('#jml-mhs-2').val(tot_peserta - mhs_2);
                $('#jml-mhs-2-hide').val(tot_peserta - mhs_2);
                $('#ruangan-2').show();
            }
        });

            $('#autocomplete-pengawas-1').autocomplete({
                serviceUrl: '{{ route('jdu_pengawas') }}',
                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                },
                onSearchStart: function(data) {
                    $('#spinner-autocomplete-1').show();
                },
                onSearchComplete: function(data) {
                    $('#spinner-autocomplete-1').hide();
                },
                onSelect: function(suggestion) {
                    $('#pengawas-1').val(suggestion.data);
                },
                onInvalidateSelection: function() {
                }
            });

            $('#autocomplete-pengawas-2').autocomplete({
                serviceUrl: '{{ route('jdu_pengawas') }}',
                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                },
                onSearchStart: function(data) {
                    $('#spinner-autocomplete-2').show();
                },
                onSearchComplete: function(data) {
                    $('#spinner-autocomplete-2').hide();
                },
                onSelect: function(suggestion) {
                    $('#pengawas-2').val(suggestion.data);
                },
                onInvalidateSelection: function() {
                }
            });

            var options = {
                beforeSend: function() 
                {
                    $('#overlay').show();
                    $("#btn-submit").attr('disabled','');
                    $("#btn-keluar").attr('disabled','');
                    $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                },
                success:function(data, status, message) {
                    if ( data.error == 1 ) {
                        $("#close-error").show();
                        showMessage(data.msg);
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
                    showMessage(pesan);
                }
            }; 

            $('#form-add-jdu').ajaxForm(options);

        $("#close-error").click(function(){
            $('.ajax-message').hide();
            $(this).hide();
        });
    });
    
    function openModal(mk,waktu,ruangan,dosen,terisi,id_jdk)
    {
        $('#modal-jdu').modal({ backdrop: 'static' }, 'show');
        $('#matakuliah').html(mk);
        $('#ruangan').html(ruangan);
        $('#waktu').html(waktu);
        $('#dosen').html(dosen);
        $('#id_jdk').val(id_jdk);
        $('#jml-mhs-1').val(terisi);
        $('#jml-mhs-1-hide').val(terisi);
        $('#total-peserta').val(terisi);
    }


    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-keluar').removeAttr('disabled');
        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

</script>
@endsection