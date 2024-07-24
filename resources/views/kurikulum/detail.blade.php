@extends('layouts.app')

@section('title','Detail Kurikulum')


@section('content')
  {{-- {{ dd($mk_kur) }} --}}
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Detail Kurikulum
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>
                {{ Rmt::AlertSuccess() }}
                <div class="row" style="margin-bottom: 13px">
                    <div class="col-md-12">
                        <a href="{{ route('kurikulum') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                        <a href="{{ route('kurikulum_edit',['id' => $kur->id]) }}" style="margin: 3px 3px" class="btn btn-warning btn-sm pull-right"><i class="fa fa-pencil"></i> UBAH</a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table border="0" class="table table-hover">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th width="160px">Nama Kurikulum</th>
                                        <td colspan="5">: {{ $kur->nm_kurikulum }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th>
                                        <td>: {{ $kur->jenjang }} {{ $kur->nm_prodi }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>: <?= Sia::statusKurikulum($kur->aktif) ?></td>
                                        <th width="80px">SKS Wajib</th>
                                        <td>: {{ $kur->jml_sks_wajib }}</td>
                                        <th width="80px">SKS Pilihan</th>
                                        <td>: {{ $kur->jml_sks_pilihan }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @include('kurikulum.matakuliah')

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

        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('kurikulum_matakuliah', ['prodi' => $kur->id_prodi]) }}',
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
                $('#kode-mk').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });

        $('#btn-salin-mk').click(function(){
            var btn = $(this);
            var id_kurikulum = $('#salin-mk').val();
            if ( id_kurikulum == '' ) {
                alert('Anda belum memilih kurikulum yang ingin di salin');
                return false;
            }

            btn.html('<i class="fa fa-spinner fa-spin"></i> Sedang Menyalin..');
            $('#overlay').show();

            $.ajax({
                url : '{{ route('kurikulum_mk_salin') }}',
                data : {id_kurikulum:id_kurikulum,kurikulum_tujuan: '{{ $kur->id }}' },
                success: function(data){

                    if ( data.error == 1 ) {
                        $('#overlay').hide();
                        btn.html('SALIN MATAKULIAH');
                        alert(data.msg);
                    } else {
                        window.location.reload();
                    }
                },
                error: function(data,status,msg){
                    $('#overlay').hide();
                    btn.html('SALIN MATAKULIAH');
                    alert(msg);
                }
            });
        });
    });

    function showMessage(pesan)
    {
        $('#overlay').hide();
        
        $('.ajax-message-mk').html(pesan);
        $('#modal-error').modal('show');

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
                    window.location.href='{{ route('kurikulum_detail',['id' => $kur->id]) }}';
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

    submit('matakuliah');

</script>
@endsection