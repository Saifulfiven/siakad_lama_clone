@extends('layouts.app')

@section('title','Pilih konsentrasi Mahasiswa')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Pilih konsentrasi Mahasiswa
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('konsentrasi_store') }}" id="form-konsentrasi" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-6">
                            <br>
                            <div class="form-group">
                                <label class="control-label">Semester</label>
                                <div>
                                    <select name="id_smt" class="form-control" style="max-width: 200px">
                                        @foreach( Sia::listSemester() as $smt )
                                            <option value="{{ $smt->id_smt }}" {{ $smt->id_smt == Sia::sessionPeriode() ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Mahasiswa</label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-mhs" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" required="" id="autocomplete-mhs" name="nama_mhs">
                                        <input type="hidden" id="mahasiswa" name="mahasiswa">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Kelas</label>
                                <div>
                                    <?php
                                        $kelas = Sia::listKelasKonsentrasi();
                                        $bagian = []
                                    ?>
                                    <select name="kelas" class="form-control" style="max-width: 200px">
                                        <option value="">Pilih kelas</option>
                                        @foreach( $kelas as $key => $val )
                                            @foreach( range('A', $val) as $bag )
                                                <option value="{{ $key }}-{{ $bag }}">{{ $key }}-{{ $bag }}</option>
                                            @endforeach
                                            <!-- <option value="XII-G1">XII-G1</option>
                                            <option value="XII-H1">XII-H1</option>
                                            <option value="XII-H2">XII-H2</option> -->
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Pilih Konsentrasi</label>
                                <div>
                                    <select name="konsentrasi" class="form-control" style="max-width: 350px">
                                        <option value="">Pilih konsentrasi</option>
                                        @foreach( Sia::listKonsentrasi(61101) as $kon )
                                            <option value="{{ $kon->id_konsentrasi }}">{{ $kon->nm_konsentrasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <hr>
                                <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> BATAL</a>
                                <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
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

        $('#autocomplete-mhs').autocomplete({
            serviceUrl: '{{ route('konsentrasi_get_mhs') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-mhs').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-mhs').hide();
            },
            onSelect: function(suggestion) {
                $('#mahasiswa').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });
    });


    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('#overlay').show();
                $("#btn-submit").attr('disabled','');
                $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                if ( data.error == 1 ) {
                    showMessage(data.msg);
                } else {
                    window.location.href='{{ route('konsentrasi') }}';
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
    submit('konsentrasi');

</script>
@endsection