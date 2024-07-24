@extends('layouts.app')

@section('title','Ubah Jadwal Perkuliahan')

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
              Ubah Jadwal Perkuliahan
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ in_array('61101', Sia::getProdiUser()) ? route('jdk_update_s2') : route('jdk_update') }}" id="form-jadwal" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
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
                        <div class="col-md-10">

                            <div class="form-group">
                                <label class="control-label">Semester</label>
                                <div>
                                    {{ $jdk->nm_smt }}
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label">Program Studi</label>
                                <div>
                                    {{ $jdk->jenjang.' '.$jdk->nm_prodi }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Waktu Kuliah <span>*</span></label>
                                <div>
                                    <select class="form-control mw-1" name="waktu" id="waktu">
                                        <option value="PAGI" {{ $jdk->ket == 'PAGI' ? 'selected':'' }}>PAGI</option>
                                        <option value="SIANG" {{ $jdk->ket == 'SIANG' ? 'selected':'' }}>SIANG</option>
                                        <option value="MALAM" {{ $jdk->ket == 'MALAM' ? 'selected':'' }}>MALAM</option>
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
                                    <input type="text" class="form-control" value="{{ $jdk->kode_mk }} - {{ $jdk->nm_mk }} ({{ $jdk->sks_mk }} sks)" disabled>
                                    <!-- <div class="input-icon right"> 
                                        <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" value="{{ $jdk->kode_mk }} - {{ $jdk->nm_mk }} ({{ $jdk->sks_mk }} sks)" name="matakuliah_value" style="font-size:13px" required="" id="autocomplete-ajax">
                                        <input type="hidden" id="id-mk" value="{{ $jdk->id_mk }}" name="matakuliah">
                                        <input type="hidden" name="id_mkur" value="{{ $jdk->id_mkur }}" id="id-mkur">
                                    </div> -->
                                </div>
                            </div>

                            @if ( !empty($jdk->hari) )

                                <div class="form-group">
                                    <label class="control-label">Hari <span>*</span></label>
                                    <div>
                                        <select class="form-control mw-1" name="hari">
                                            @for( $i = 1; $i <= 7; $i++ )
                                                <option value="{{ $i }}"{{ $jdk->hari == $i ? 'selected':'' }}>{{ Rmt::hari($i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @endif

            
                            @if ( $jdk->id_prodi != 61101 )
                                <div class="form-group">
                                    <label class="control-label">Nama Kelas <span>*</span></label>
                                    <div id="kelas-kuliah">
                                        <select class="form-control mw-2" id="kelas" name="kelas">
                                            <?php foreach( Sia::kelasMhs($jdk->id_prodi) as $r ) { ?>
                                                <option value="<?= $r->kode_kelas ?>"><?= $r->kode_kelas ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="control-label">Nama Kelas <span>*</span></label>
                                    <div>
                                        {{ $jdk->kode_kls }}
                                        <input type="hidden" name="kelas" value="{{ $jdk->kode_kls }}">
                                    </div>
                                </div>
                            @endif

                              <!--   <div class="form-group">
                                    <label class="control-label">Nama Kelas <span>*</span></label>
                                    <div>
                                        {{ $jdk->kode_kls }}
                                        <input type="hidden" name="kelas" value="{{ $jdk->kode_kls }}">
                                    </div>
                                </div> -->



                            @if ( !empty($jdk->hari) )

                                <div class="form-group jdk-normal">
                                    <label class="control-label">Jam <span>*</span></label>
                                    <div id="jam-kuliah">
                                        <select class="form-control mw-3" name="jam">
                                            <?php $jamkul = Sia::jamKuliah($jdk->id_prodi, $jdk->ket) ?>
                                            <?php foreach( $jamkul as $j ) { ?>
                                                <option value="<?= $j->id ?>"{{ $j->id == $jdk->id_jam ? 'selected':'' }}><?= substr($j->jam_masuk,0,5) ?> - <?= substr($j->jam_keluar,0,5) ?> (<?= $j->ket ?>)</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group jdk-normal">
                                    <label class="control-label">Ruangan <span>*</span></label>
                                    <div>
                                        <select class="form-control mw-2" name="ruangan">
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

                                <div class="form-group jdk-normal">
                                    <label class="control-label">Kelas Khusus</label>
                                    <div>
                                        <select name="kelas_khusus" class="form-control mw-2">
                                            <option value="">Bukan Kelas Khusus</option>
                                            <option value="1" {{ $jdk->kelas_khusus == '1' ? 'selected':'' }}>Muslim</option>
                                            <option value="2" {{ $jdk->kelas_khusus == '2' ? 'selected':'' }}>Non Muslim</option>
                                        </select>
                                    </div>
                                </div>
                            @endif

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

        $('#waktu').change(function(){
            var waktu = $(this).val();
            var prodi = '{{ $jdk->id_prodi }}';
            
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
                        window.location.href='{{ route('jdk_detail') }}/'+data.msg;
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