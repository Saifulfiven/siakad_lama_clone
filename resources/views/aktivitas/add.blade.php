@extends('layouts.app')

@section('title','Tambah Aktivitas')


@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Aktivitas
            </header>
              
            <div class="panel-body" style="padding-top: 3px">

                <div class="ajax-message"></div>

                <form action="{{ route('akm_store') }}" id="form-aktivitas" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                    {{ csrf_field() }}
                    <div class="row" style="border-bottom: 1px solid #eee;margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="javascript:void()" onclick="window.history.back();" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-times"></i> BATAL</a>
                            <button class="btn btn-primary btn-sm pull-right" id="btn-submit" style="margin: 3px 3px" ><i class="fa fa-floppy-o"></i> SIMPAN</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10">

                            <div class="form-group">
                                <label class="control-label">Mahasiswa <span>*</span></label>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-mhs" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" required="" id="autocomplete-mhs" name="nama_mhs">
                                        <input type="hidden" id="id-mhs" name="mahasiswa">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label">Semester <span>*</span></label>
                                <div>
                                    {{ Sia::sessionPeriode('nama') }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Status Mahasiswa <span>*</span></label>
                                <div>
                                    <select name="status" class="form-control mw-2">
                                        <option value="">--Pilih Status--</option>
                                        @foreach( Sia::statusAkmMhs() as $ak )
                                            <option value="{{ $ak->id_stat_mhs }}">{{ $ak->nm_stat_mhs }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <?= Sia::Textfield('IPS','ips',false,'text','mw-1','onkeypress="return indeksPrestasi(event,this)" minlength="4"') ?>

                            <?= Sia::Textfield('IPK','ipk',false,'text','mw-1','onkeypress="return indeksPrestasi(event,this)" minlength="4"') ?>
                            <?= Sia::Textfield('Jumlah sks semester','sks_semester',false,'number','mw-1') ?>
                            <?= Sia::Textfield('Jumlah sks total','sks_total',false,'number','mw-1') ?>

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
        $('#autocomplete-mhs').autocomplete({
            serviceUrl: '{{ route('akm_mhs') }}',
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
                $('#id-mhs').val(suggestion.data);
            },
            onInvalidateSelection: function() {
            }
        });
    });

    function indeksPrestasi(evt, ele){

        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode( key );
        var value = ele.value + key;

        if ( value.length == 5 ) return false;

        var regex = /^\d+(,\d{0,2})?$/;
        if( !regex.test(value) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }

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
                    window.location.href='{{ route('akm') }}';
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
    submit('aktivitas');

</script>
@endsection