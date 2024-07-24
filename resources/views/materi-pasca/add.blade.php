@extends('layouts.app')

@section('title','Tambah Materi')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Materi
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('materi_store') }}" id="form-matakuliah" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-8">
                            <?= Sia::Textfield('Judul <span>*</span>','judul',false,'text','mw-4') ?>                   
                            <div class="form-group">
                                <label class="control-label">Matakuliah <span>*</span></label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" style="font-size:13px" class="form-control" name="matakuliah_value" id="autocomplete-ajax" placeholder="Mata kuliah">
                                        <input type="hidden" id="mk-terganti" name="matakuliah">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">File Materi<span>*</span></label>
                                <div>
                                    <input type="file" name="file" class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-8">
                            <hr>
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
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

    $(function(){
        $('#autocomplete-ajax').autocomplete({
            serviceUrl: '{{ route('materi_matakuliah') }}',
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
                $('#mk-terganti').val(suggestion.data);
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
                    window.location.href='{{ route('materi') }}';
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