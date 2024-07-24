@extends('layouts.app')

@section('title','Ubah data PKM')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Ubah PKM
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                {{ Rmt::AlertError() }}
                <div class="ajax-message"></div>

                <form action="{{ route('pkm_update') }}" id="form-pkm" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $pkm->id }}">

                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('pkm') }}" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> BATAL / KEMBALI</a>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <b>Anggota:</b><br>
                                <table class="table table-bordered" style="min-width: 450px;width: 450px">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Mahasiswa</th>
                                        <th>Jabatan</th>
                                        <th></th>
                                    </tr>
                                    <?php $no = 1 ?>
                                    @foreach( $peserta as $val )
                                        <tr>
                                            <td align="center">{{ $no++ }}</td>
                                            <td>{{ $val->nim }} - {{ $val->nm_mhs }}</td>
                                            <td align="center">{{ $val->jabatan }}</td>
                                            <td align="center">
                                                @if ( $val->jabatan != 'ketua' )
                                                <a href="{{ route('pkm_anggota_delete', ['id' => $val->id]) }}" onclick="return confirm('Anda ingin menghapus anggota ini?')" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                                <button type="button" data-toggle="modal" data-target="#modal-add-anggota" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah Anggota</button>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="table-responsive">
                                <b>Pembimbing:</b><br>
                                <table class="table table-bordered" style="min-width: 400px;width: 400px">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Dosen</th>
                                        <th width="10"></th>
                                    </tr>
                                    <?php $no = 1 ?>

                                    @foreach( $pembimbing as $val )
                                        <tr>
                                            <td align="center">{{ $no++ }}</td>
                                            <td>{{ $val->nm_dosen }}</td>
                                            <td align="center">
                                                <a href="{{ route('pkm_dosen_delete', ['id' => $val->id]) }}" onclick="return confirm('Anda ingin menghapus pembimbing ini?')" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ( empty($pembimbing) )
                                        <tr><td colspan="3">Belum ada data</td></tr>
                                    @endif
                                </table>
                                <button type="button" data-toggle="modal" data-target="#modal-add-dosen" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah Pembimbing</button>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <?= Sia::TextfieldEdit('Judul <span>*</span>','judul', $pkm->judul) ?>
                        </div>

                        <div class="col-md-12 form-group">
                          <label for="kategori">Kategori <span>*</span></label>
                          <select name="kategori" class="form-control" id="kategori" style="max-width: 100%;">
                            <option value="-">Pilih Kategori</option>
                            @foreach ($kategori as $k)
                                <option value="{{ $k->kode_kategori }}" class="col-md-5">{{ $k->kode_kategori }} -- {{ $k->judul_kategori }}</option>
                            @endforeach
                          </select>
                        </div>

                        <div class="col-md-12">
                            <hr>
                            <button class="btn btn-primary" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                </form>

            </div>

        </div>
      </div>
    </div>


<div id="modal-add-anggota" class="modal fade" tabindex="-1" style="top: 40% !important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Tambah Anggota</h4>
    </div>
    <div class="modal-body">

        <div class="col-md-12">
            <form action="{{ route('pkm_anggota_store') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $pkm->id }}">

                <div class="form-group">
                    <label class="control-label">Mahasiswa (Ketikkan nama/nim u/ mencari anggota) <span>*</span></label>
                    <div style="position: relative">
                        <div class="input-icon right"> 
                            <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                            <input type="text" id="autocomplete-ajax" class="form-control">
                        </div>
                        <input type="hidden" name="mahasiswa" id="id-mhs-reg">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Jabatan <span>*</span></label>
                    <div>
                        <select name="jabatan" class="form-control" required="">
                            <option value="">-- Pilih jabatan --</option>
                            <option value="ketua">Ketua</option>
                            <option value="anggota">Anggota</option>
                        </select>
                    </div>
                </div>

                <hr>
                <button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> KELUAR</button>
                <button type="submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
                <br>
                <br>
            </form>
        </div>
    </div>
</div>


<div id="modal-add-dosen" class="modal fade" tabindex="-1" style="top: 40% !important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Tambah Pembimbing</h4>
    </div>
    <div class="modal-body">

        <div class="col-md-12">
            <form action="{{ route('pkm_dosen_store') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $pkm->id }}">

                <div class="form-group">
                    <label class="control-label">Dosen (Ketik nama dosen u/ mencari) <span>*</span></label>
                    <div style="position: relative">
                        <div class="input-icon right"> 
                            <span id="spinner-autocomplete-dsn" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                            <input type="text" id="autocomplete-ajax-dsn" class="form-control">
                        </div>
                        <input type="hidden" name="dosen" id="id-dosen">
                    </div>
                </div>

                <hr>
                <button type="button" class="btn btn-danger btn-sm pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> KELUAR</button>
                <button type="submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
                <br>
                <br>
            </form>
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

        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('pkm_get_mahasiswa2') }}',
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
                $('#id-mhs-reg').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#autocomplete-ajax-dsn').autocomplete({
            serviceUrl: '{{ route('pkm_get_dosen2') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-dsn').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-dsn').hide();
            },
            onSelect: function(suggestion) {
                $('#id-dosen').val(suggestion.data);
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
                    window.location.href='{{ route('pkm') }}';
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
    submit('pkm');

</script>
@endsection