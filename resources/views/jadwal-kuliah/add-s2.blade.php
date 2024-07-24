@extends('layouts.app')

@section('title','Tambah Jadwal Perkuliahan')

@section('topMenu')
    @include('jadwal-kuliah.top-menu')
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Jadwal Perkuliahan
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('jdk_store_s2') }}" id="form-jadwal" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" id="prodi-value" value="">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('jdk') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">

                            <div class="form-group">
                                <label class="control-label">Semester</label>
                                <div>
                                    @if ( !Sia::admin() )
                                        {{ Sia::sessionPeriode('nama') }}
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Jenis Jadwal</label>
                                <div>
                                    <select class="form-control mw-1" id="jenis-jadwal" name="jenis_jadwal">
                                        <option value="normal">Normal</option>
                                        <option value="praktek">Praktek</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group jdk-normal">
                                <label class="control-label">Waktu Kuliah <span>*</span></label>
                                <div>
                                    <select class="form-control mw-2" name="waktu" id="waktu">
                                        <option value="">-- Waktu Kuliah --</option>
                                        <option value="PAGI">PAGI</option>
                                        <option value="SIANG">SIANG</option>
                                        <option value="MALAM">MALAM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="data-jadwal" style="display: none">

                                <div class="form-group" id="hidden-prodi">
                                    <label class="control-label">Program Studi <span>*</span></label>
                                    <div>
                                        <select class="form-control mw-2" disabled="">
                                            <option value="">-- Program Studi --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" id="show-prodi" style="display: none">
                                    <label class="control-label">Program Studi <span>*</span></label>
                                    <div>
                                        <select class="form-control mw-2" name="prodi" id="prodi" onchange="selectProdi(this.value)">
                                            <option value="">-- Program studi --</option>
                                            @foreach( Sia::listProdi() as $r )
                                                <option value="{{ $r->id_prodi }}">{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="" id="hidden-matakuliah">
                                    <!-- <label>Matakuliah <span>*</span></label> -->
                                    <div>
                                        <input type="text" class="form-control" disabled placeholder="Mata kuliah">
                                    </div>
                                </div>
                                <div class="" id="show-matakuliah" style="display: none">
                                    <div style="position: relative;">
                                        <div class="input-icon right"> 
                                            <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                            <input type="text" style="font-size:13px" class="form-control" name="matakuliah_value" required="" id="autocomplete-ajax" placeholder="Mata kuliah">
                                            <input type="hidden" id="id-mk" name="matakuliah">
                                            <input type="hidden" name="id_mkur" id="id-mkur">
                                            <input type="hidden" name="sks" id="sks-mk">
                                            <input type="hidden" name="semester_mk" id="semester-mk">
                                        </div>
                                    </div>
                                </div>

                                <br>

                                <div class="form-group jdk-normal">
                                    <label class="control-label">Jadwal Untuk</label>
                                    <div>
                                        <select name="jadwal_for" onchange="jenisDaftar(this.value)" class="form-control mw-2">
                                            <option value="reguler">Reguler</option>
                                            <option value="Ekstensi" selected="">Ekstensi</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group jdk-normal jml-pertemuan">
                                    <label class="control-label">Jumlah Pertemuan</label>
                                    <div>
                                        <select class="form-control mw-1" onchange="jmlPertemuan(this.value)">
                                            @for( $i = 1; $i <= 16; $i++ )
                                                <option value="{{ $i }}" {{ $i == 12 ?'selected':'' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        <span class="help-block">Pertemuan tatap muka/Diluar ujian</span>
                                    </div>
                                </div>

                                <div class="form-group jdk-normal">
                                    <label class="control-label">Hari <span>*</span></label>
                                    <div>
                                        <select class="form-control mw-1" name="hari">
                                            <option value="">-- Hari --</option>
                                            @for( $i = 1; $i <= 7; $i++ )
                                                <option value="{{ $i }}">{{ Rmt::hari($i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Nama Kelas <span>*</span></label>
                                    <div id="kelas-kuliah">
                                        <select class="form-control mw-1" disabled="">
                                            <option value="">-- Kelas --</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="kelas" id="kode-kelas">
                                </div>

                                <div class="form-group jdk-normal jam-kuliah" style="display: none">
                                    <label class="control-label">Jam <span>*</span></label>
                                    <div id="jam-kuliah">
                                        <select class="form-control mw-3" disabled="">
                                            <option value="">-- Jam --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group jdk-normal">
                                    <label class="control-label">Ruangan <span>*</span></label>
                                    <div>
                                        <select class="form-control mw-2" name="ruangan">
                                            <option value="">-- Ruangan --</option>
                                            @foreach( Sia::ruangan() as $j )
                                                <option value="{{ $j->id }}">{{ $j->nm_ruangan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group jdk-normal">
                                    <label class="control-label">Kapasitas Kelas <span>*</span></label>
                                    <div>
                                        <input type="number" class="form-control mw-1" value="{{ Sia::kapasitasDefault() }}" name="kapasitas" maxlength="2" size="2">
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-5 jdk-normal">

                            <h4>Dosen mengajar</h4>
                            <hr>
                            <div class="table-responsive">
                                <table border="0" class="table-hover table-form" width="100%">
                                    <tr><td colspan="2"><strong>Dosen 1</strong></td></tr>
                                    <tr>
                                        <td width="150px">Nama Dosen</td>
                                        <td>
                                            <div style="position: relative;">
                                                <div class="input-icon right"> 
                                                    <span id="spinner-autocomplete-1" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                    <input type="text" class="form-control" id="autocomplete-ajax-1">
                                                    <input type="hidden" id="id-dosen" name="dosen[]">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah rencana tatap muka</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="tatap_muka[]" value="6">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah realisasi tatap muka</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="real_tm[]" value="6">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dosen Ke</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="dosen_ke[]" value="1">
                                        </td>
                                    </tr>

                                    <tr><td colspan="2"><strong>Dosen 2</strong></td></tr>
                                    <tr>
                                        <td width="150px">Nama Dosen</td>
                                        <td>
                                            <div style="position: relative;">
                                                <div class="input-icon right"> 
                                                    <span id="spinner-autocomplete-2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                    <input type="text" class="form-control" id="autocomplete-ajax-2">
                                                    <input type="hidden" id="id-dosen2" name="dosen[]">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah rencana tatap muka</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="tatap_muka[]" value="6">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah realisasi tatap muka</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="real_tm[]" value="6">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dosen Ke</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="dosen_ke[]" value="2">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div id="tgl-pertemuan" style="display: none">
                            <hr>

                            <h4>Tanggal pertemuan</h4>
                            <hr>
                            <div class="table-responsive" id="list-pertemuan">
                                @for( $i = 1; $i <= 12; $i++ )
                                    <div class="col-md-3 col-lg-3 col-sm-3">
                                        <table class="table-hover table-form">
                                            <tr>
                                                <td width="100">Pertemuan {{ $i }} : </td>
                                                <td><input type="date" name="pertemuan[{{ $i }}]" class="form-control"></td>
                                            </tr>
                                            <tr>
                                                <td>Jam Pert. {{ $i }} : </td>
                                                <td>
                                                    <select class="form-control mw-3" name="jamper[{{ $i }}]">
                                                        <?php $jamkul = Sia::jamKuliah(61101); ?>
                                                        <?php foreach( $jamkul as $j ) { ?>
                                                            <option value="<?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?>"><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    @if ( $i % 4 == 0 )
                                        <div class="clearfix"></div>
                                        <hr>
                                    @endif
                                @endfor
                            </div>
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
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>
    $(function () {
        'use strict';

        $('#nav-mini').trigger('click');

        $('#jenis-jadwal').change(function(){
            var jenis_jadwal = $(this).val();
            if ( jenis_jadwal == 'normal' ) {
                $('.jdk-normal').show();
                resetForm();
                $('.data-jadwal').hide();
            } else {
                $('.jdk-normal').hide();
                $('.jdk-normal select').val('');
                $('.jdk-normal input[type="text"]').val('');
                $('.data-jadwal').show();
                $('#hidden-prodi').hide();
                $('#show-prodi').show();
            }
        });

            $('#autocomplete-ajax-1').autocomplete({
                serviceUrl: '{{ route('jdk_dosen') }}',
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
                    $('#id-dosen').val(suggestion.data);
                },
                onInvalidateSelection: function() {
                }
            });

            $('#autocomplete-ajax-2').autocomplete({
                serviceUrl: '{{ route('jdk_dosen') }}',
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
                    $('#id-dosen2').val(suggestion.data);
                },
                onInvalidateSelection: function() {
                }
            });

        $('#waktu').change(function(){
            var waktu = $(this).val();

            $('#prodi').val('');
            $('#jam-kuliah select').val('');
            $('#jam-kuliah select').attr('disabled','');
            $('#kelas-kuliah select').attr('disabled','');


            resetForm();
            
            if ( waktu == '' ) {
                $('.data-jadwal').hide();
                $('#hidden-prodi').show();
                $('#show-prodi').hide();
                $('#tgl-pertemuan').hide();
            } else {
                $('#prodi option[value="61101"]').attr("selected",true);
                selectProdi(61101);
                $('.data-jadwal').show();
                $('#hidden-prodi').hide();
                $('#show-prodi').show();
                $('#tgl-pertemuan').show();
            }
        });

        $(document).on('change', '#kelas', function(){
            var kls = $(this).val();
            var ket = kls.split('|');

            $('#kode-kelas').val(ket[0]);
        });

            var options = {
                beforeSend: function() 
                {
                    var id_mk = $('#autocomplete-ajax').val();
                    if ( id_mk == '' ) {
                        alert('Matakuliah masih kosong');
                        return false;
                    }
                    $('#overlay').show();
                    $("#btn-submit").attr('disabled','');
                    $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                },
                success:function(data, status, message) {
                    if ( data.error == 1 ) {
                        showMessage(data.msg);
                    } else {
                        window.location.href='{{ route('jdk') }}';
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
                    showMessage(pesan);
                }
            }; 

            $('#form-jadwal').ajaxForm(options);

    });
    
    function jenisDaftar(value)
    {
        if ( value == 'reguler' ) {
            $('.jam-kuliah').show();
            $('#tgl-pertemuan').hide();
            $('.jml-pertemuan').hide();
        } else {
            $('.jam-kuliah').hide();
            $('#tgl-pertemuan').show();
            $('.jml-pertemuan').show();
        }
    }

    function selectProdi(value)
    {
        var prodi = value;
        var waktu = $('#waktu').val();

        if ( prodi == '' ) {
            resetForm();
        } else {
            $('#hidden-matakuliah').hide();
            $('#show-matakuliah').show();
            $('#prodi-value').val(prodi);
            $('#autocomplete-ajax').val('');
            autocomplete(prodi);
        }

        $('#jam-kuliah select').val('');
        $('#jam-kuliah select').attr('disabled','');

        $.ajax({
            url: '{{ route('jdk_ajax') }}',
            data: {tipe: 'kelas', prodi: prodi, waktu: waktu},
            beforeSend: function( xhr ) {
                $('#kelas-kuliah').html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function(data){
                $('#kelas-kuliah').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
            }
        });

        $.ajax({
            url: '{{ route('jdk_ajax') }}',
            data: {tipe: 'jam', prodi: prodi, ket:waktu},
            beforeSend: function( xhr ) {
                $('#jam-kuliah').html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function(data){
                $('#jam-kuliah').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
            }
        });
    }

    function jmlPertemuan(value)
    {
        $.ajax({
            url: '{{ route('jdk_ajax') }}',
            data: {tipe: 'pertemuan', pertemuan: value},
            beforeSend: function( xhr ) {
                $('#list-pertemuan').html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function(data){
                $('#list-pertemuan').html(data);
            },
            error: function(data,status,msg){
                alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi. Pesan Error: '+msg);
            }
        });
    }

    function resetForm()
    {
        $('#hidden-matakuliah').show();
        $('#show-matakuliah').hide();
        $('#autocomplete-ajax').val('');
        $('#id-mk').val('');
    }

    function autocomplete(prodi)
    {
        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('jdk_matakuliah') }}?prodi='+prodi,
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete').hide();
            },
            onSelect: function(suggestion) {
                $('#id-mk').val(suggestion.data);
                $('#id-mkur').val(suggestion.id_mkur);
                $('#sks-mk').val(suggestion.sks);
                $('#semester-mk').val(suggestion.semester_mk);
            },
            onInvalidateSelection: function() {
            }
        });
    }

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

</script>
@endsection