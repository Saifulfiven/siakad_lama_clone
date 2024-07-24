@extends('layouts.app')

@section('title','Ubah Jadwal Perkuliahan')

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
              Ubah Jadwal Perkuliahan
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('jda_update') }}" id="form-jadwal" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" id="prodi-value" value="">
                    <input type="hidden" name="id" value="{{ $jdk->id }}">
                    <input type="hidden" name="jenis_jadwal" value="{{ !empty($jdk->hari) ? 'normal':'praktek' }}">
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">

                            <div class="form-group">
                                <label class="control-label">Semester</label>
                                <div>
                                    {{ $jdk->nm_smt }}
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label">Program Studi <span>*</span></label>
                                <div>
                                    <select class="form-control mw-2" name="prodi" id="prodi">
                                        <option value="">-- Program studi --</option>
                                        @foreach( Sia::listProdi() as $r )
                                            <option value="{{ $r->id_prodi }}"{{ $r->id_prodi == $jdk->id_prodi ? 'selected':'' }}>{{ $r->jenjang }} {{ $r->nm_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="hidden-matakuliah" style="display: none">
                                <label class="control-label">Matakuliah <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="form-group" id="show-matakuliah">
                                <label class="control-label">Matakuliah <span>*</span></label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" value="{{ $jdk->kode_mk }} - {{ $jdk->nm_mk }} ({{ $jdk->sks_mk }} sks)" name="matakuliah_value" required="" id="autocomplete-ajax">
                                        <input type="hidden" id="id-mk" value="{{ $jdk->id_mk }}" name="matakuliah">
                                        <input type="hidden" id="id-mkur" name="id_mkur" value="{{ $jdk->id_mkur }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group jdk-normal">
                                <label class="control-label">Hari <span>*</span></label>
                                <div>
                                    <select class="form-control mw-1" name="hari">
                                        <option value="">-- Hari --</option>
                                        @for( $i = 1; $i <= 7; $i++ )
                                            <option value="{{ $i }}"{{ $jdk->hari == $i ? 'selected':'' }}>{{ Rmt::hari($i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group jdk-normal">
                                <label class="control-label">Jam <span>*</span></label>
                                <div id="jam-kuliah">
                                    <select class="form-control mw-3" name="id_jam">
                                        <option value="">-- Jam --</option>
                                        <?php $jamkul = Sia::jamKuliah($jdk->id_prodi) ?>
                                        <?php foreach( $jamkul as $j ) { ?>
                                            <option value="<?= $j->id ?>"{{ $j->id == $jdk->id_jam ? 'selected':'' }}><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?> (<?= $j->ket ?>)</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Nama Kelas <span>*</span></label>
                                <div>
                                    <input type="text" class="form-control mw-1" value="{{ $jdk->kode_kls }}" name="kelas" maxlength="5" size="5">
                                </div>
                            </div>

                            <div class="form-group jdk-normal">
                                <label class="control-label">Ruangan <span>*</span></label>
                                <div>
                                    <select class="form-control mw-2" name="ruangan">
                                        <option value="">-- Ruangan --</option>
                                        @foreach( Sia::ruangan() as $j )
                                            <option value="{{ $j->id }}"{{ $jdk->ruangan == $j->id ? 'selected':'' }}>{{ $j->nm_ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group jdk-normal">
                                <label class="control-label">Kapasitas Kelas <span>*</span></label>
                                <div>
                                    <input type="number" class="form-control mw-1" value="{{ $jdk->kapasitas_kls }}" name="kapasitas" maxlength="2" size="2">
                                </div>
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

        autocomplete('{{ $jdk->id_prodi}}');

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