@extends('layouts.app')

@section('title','Tambah Jadwal Antara')

@section('topMenu')
    @include('jadwal-antara.top-menu')
@endsection

@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Jadwal Antara
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('jda_store') }}" id="form-jadwal" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" id="prodi-value" value="">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('jda') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">

                            <div class="form-group">
                                <label class="control-label">Semester</label>
                                <div>
                                    @if ( !Sia::admin() )
                                        {{ Sia::sessionPeriode('nama') }}
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label">Program Studi <span>*</span></label>
                                <div>
                                    <select class="form-control mw-2" name="prodi" id="prodi">
                                        <option value="">-- Program studi --</option>
                                        @foreach( Sia::listProdi() as $r )
                                            <option value="{{ $r->id_prodi }}">{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="hidden-matakuliah">
                                <label class="control-label">Matakuliah <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="form-group" id="show-matakuliah" style="display: none">
                                <label class="control-label">Matakuliah <span>*</span></label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" name="matakuliah_value" required="" id="autocomplete-ajax">
                                        <input type="hidden" id="id-mk" name="matakuliah">
                                        <input type="hidden" id="id-mkur" name="id_mkur">
                                    </div>
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
                            <div class="form-group jdk-normal">
                                <label class="control-label">Jam <span>*</span></label>
                                <div id="jam-kuliah">
                                    <select class="form-control mw-3" disabled="">
                                        <option value="">-- Jam --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Nama Kelas <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control mw-1" name="kelas" maxlength="5" size="5">
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

                        <div class="col-md-4 jdk-normal">
                            <h4>Dosen mengajar</h4>
                            <hr>
                            <div class="table-responsive">
                                <table border="0" class="table-hover table-form" width="100%">
                                    <tr>
                                        <td width="150px">Nama Dosen</td>
                                        <td>
                                            <div style="position: relative;">
                                                <div class="input-icon right"> 
                                                    <span id="spinner-autocomplete-2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                    <input type="text" class="form-control" id="autocomplete-ajax-2">
                                                    <input type="hidden" id="id-dosen" name="dosen[]">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah rencana tatap muka</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="tatap_muka[]" value="12">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jumlah realisasi tatap muka</td>
                                        <td>
                                            <input type="number" maxlength="2" size="2" class="form-control mw-1" name="real_tm[]" value="12">
                                        </td>
                                    </tr>
                                </table>
                                <!-- <div class="add-dosen"></div>
                                <br>
                                <small>Jika lebih dari satu dosen yang mengajar, klik TAMBAH DOSEN</small><br>
                                <button type="button" class="btn btn-primary btn-xs" id="btn-add-dosen">TAMBAH DOSEN</button> -->
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

        $('#btn-add-dosen').click(function(){
            var btn = $(this);
            btn.html('<i class="fa fa-spinner fa-spin"></i> MENGAMBIL DATA..');
            btn.attr('disabled','');
            $.ajax({
                url: '{{ route('jda_add_dosen') }}',
                success: function(data){
                    btn.removeAttr('disabled');
                    btn.html('TAMBAH DOSEN');
                    $('.add-dosen').append(data);
                },
                error: function(data,status,msg){
                    alert('Terjadi gangguan saat mengambil data, periksa koneksi internet dan ulangi lagi');
                    btn.removeAttr('disabled');
                    btn.html('TAMBAH DOSEN');
                }
            });
        });

        $('#autocomplete-ajax-2').autocomplete({
            serviceUrl: '{{ route('jda_dosen') }}',
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
                $('#id-dosen').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#prodi').change(function(){
            var prodi = $(this).val();

            if ( prodi == '' ) {
                $('#hidden-matakuliah').show();
                $('#show-matakuliah').hide();
                $('#autocomplete-ajax').val('');
                $('#id-mk').val('');
            } else {
                $('#hidden-matakuliah').hide();
                $('#show-matakuliah').show();
                $('#prodi-value').val(prodi);
                $('#autocomplete-ajax').val('');
                autocomplete(prodi);
            }

            $.ajax({
                url: '{{ route('jda_ajax') }}',
                data: {tipe: 'jam', prodi: prodi},
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
                    window.location.href='{{ route('jda_detail') }}/'+data.msg;
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

    function autocomplete(prodi)
    {
        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('jda_matakuliah') }}?prodi='+prodi,
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