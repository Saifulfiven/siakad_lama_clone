@extends('layouts.app')

@section('title','Pendaftar Seminar')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          Validasi Pembayaran Seminar
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-lg-2 col-md-4" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('jenis', this.value)">
                            <option value="all">Semua Jenis</option>
                            @foreach( Sia::jenisSeminar() as $key => $val )
                                <option value="{{ $key }}" {{ Session::get('sem.jenis') == $key ? 'selected':'' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('smt', this.value)">
                            <option value="all">Semester</option>
                            @foreach( Sia::listSemester() as $smt )
                                <option value="{{ $smt->id_smt }}" {{ Session::get('sem.smt') == $smt->id_smt ? 'selected':'' }}>{{ $smt->nm_smt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4" style="padding-right:5px;">
                        <select class="form-control input-sm" onchange="filter('status', this.value)">
                            <option value="all" {{ Session::get('sem.status') == 'all' ? 'selected':'' }}>Semua Status</option>
                            <option value="1" {{ Session::get('sem.status') == '1' ? 'selected':'' }}>Disetujui</option>
                            <option value="0" {{ Session::get('sem.status') == '0' ? 'selected':'' }}>Belum disetujui</option>
                        </select>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                        <form action="" method="get" id="form-cari">
                            <div class="input-group pull-right">
                                <input type="hidden" name="pencarian" value="1">
                                <input type="text" class="form-control input-sm" name="cari" value="{{ Session::get('sem.cari') }}">
                                <div class="input-group-btn">
                                    @if ( Session::has('sem.cari') )
                                        <button class="btn btn-danger btn-sm" id="reset-cari" type="button"><i class="fa fa-times"></i></button>
                                    @endif
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">
                        
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                            <thead class="custom">
                                <tr>
                                    <th width="20" class="text-center">No</th>
                                    <th>Mahasiswa</th>
                                    <th>Jenis</th>
                                    <th>Bukti Bayar</th>
                                    <th>Status Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $seminar as $sem )
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration - 1 + $seminar->firstItem()}}</td>
                                        <td>{{ $sem->nm_mhs }} - {{ $sem->nim }}</td>
                                        <td class="text-center">{{ Sia::jenisSeminar($sem->jenis) }}</td>
                                        <td class="text-center">
                                            @if ( !empty($sem->file) )
                                                <a href="{{ config('app.url-file-seminar') }}/{{ $sem->nim }}/{{ $sem->file }}" target="_blank" title="Lihat bukti pembayaran">
                                                    <!-- <i class="fa fa-picture-o fa-2x"></i> -->
                                                    <?php $icon = Rmt::icon($sem->file); ?>
                                                    <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                                </a>
                                            @else
                                                Belum ada diupload
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ( $sem->validasi_bauk == 1 )
                                                <i class="fa fa-check-square" style="color: green"></i> 
                                            @else
                                                <i class="fa fa-ban" style="color: red"></i> 
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ( $sem->validasi_bauk == 1 )
                                                <button class="btn btn-danger btn-xs" 
                                                    onclick="updateStatus('0', '{{ addslashes($sem->nm_mhs) }} - {{ $sem->nim }}', '{{ Sia::jenisSeminar($sem->jenis) }}', '{{ $sem->id }}', '{{ $sem->id_mhs_reg }}', '{{ $sem->jenis }}')">BATALKAN</button>
                                            @else
                                                <button class="btn btn-theme-inverse btn-xs" 
                                                    onclick="updateStatus('1', '{{ addslashes($sem->nm_mhs) }} - {{ $sem->nim }}', '{{ Sia::jenisSeminar($sem->jenis) }}', '{{ $sem->id }}', '{{ $sem->id_mhs_reg }}', '{{ $sem->jenis }}')">SETUJUI</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ( $seminar->total() == 0 )
                            &nbsp; Tidak ada data
                        @endif

                        @if ( $seminar->total() > 0 )
                            <div class="pull-left">
                                Jumlah data : {{ $seminar->total() }}
                            </div>
                        @endif

                        <div class="pull-right"> 
                            {{ $seminar->render() }}
                        </div>


                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>


<div id="modal-update" class="modal fade" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Validasi Pembayaran</h4>
    </div>
    
    <form action="{{ route('seminar_update') }}" method="post" id="form-update">
        {{ csrf_field() }}
        <input type="hidden" name="id" id="id-persetujuan">
        <input type="hidden" name="disetujui" id="disetujui">
        <input type="hidden" name="id_mhs_reg" id="id-mhs-reg">
        <input type="hidden" name="jenis" id="kode-jenis">

        <div class="modal-body">


            <table class="table" width="100%" border="0">
                <tr>
                    <td style="padding: 10px 0">Mahasiswa</td>
                    <td id="mhs"></td>
                </tr>
                <tr>
                    <td style="padding: 10px 0">Jenis Pembayaran</td>
                    <td id="nm-seminar"></td>
                </tr>
                <tr id="jumlah-bayar">
                    <td>Jumlah Bayar <span>*</span></td>
                    <td>
                        <input type="text" name="jumlah_bayar" class="form-control">
                    </td>
                </tr>
            </table>

        </div>

    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default pull-left"><i class="fa fa-times"></i> Tutup</button>

        <button type="submit" class="btn pull-right" id="btn-submit-update"></button>

    </div>
</div>

<div id="modal-error" class="modal fade" tabindex="-1" style="top:30%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Terjadi kesalahan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="ajax-message"></div>
        <hr>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
        </center>
    </div>
    <!-- //modal-body-->
</div>



@endsection

@section('registerscript')

<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

<script>

    $(function(){
        @if ( Session::has('success') )
            showSuccess('{{ Session::get('success') }}', 3000);
        @endif

        $('#reset-cari').click(function(){
            var q = $('input[name="cari"]').val();
            $('input[name="cari"]').val('');
            if ( q.length > 0 ) {
                $('#form-cari').submit();
            }
            
        });

        $(document).on( "keyup", 'input[name="jumlah_bayar"]', function( event ) {

            var selection = window.getSelection().toString();
            if ( selection !== '' ) {
                return;
            }
            
            if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                return;
            }
            
            
            var $this = $( this );
            
            var input = $this.val();

            var input = input.replace(/[\D\s\._\-]+/g, "");
            input = input ? parseInt( input, 10 ) : 0;

            $this.val( function() {
                return ( input === 0 ) ? "" : input.toLocaleString();
            } );
        });

    })

    function filter(modul, value)
    {
        window.location.href = '{{ route('seminar_filter') }}?modul='+modul+'&val='+value;
    }

    function showMessage(modul,pesan)
    {
        $('.ajax-message').html(pesan);
        $('#modal-error').modal('show');
    }

    function submit(modul)
    {
        var options = {
            beforeSend: function() 
            {
                $('body').modalmanager('loading');
            },
            success:function(data, status, message) {

                window.location.reload();

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
                showMessage(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('update');

    function updateStatus(value, mhs, nm_seminar, id, id_mhs_reg, jenis)
    {
        $('#mhs').html(mhs);
        $('#nm-seminar').html(nm_seminar);
        $('#id-persetujuan').val(id);
        $('#disetujui').val(value);
        $('#id-mhs-reg').val(id_mhs_reg);
        $('#kode-jenis').val(jenis);

        if ( value === '1' ) {
            $('#jumlah-bayar').show();
            $('#setuju').html('menyetujui');
            $('#btn-submit-update').addClass('btn-success');
            $('#btn-submit-update').removeClass('btn-danger');
            $('#btn-submit-update').html('<i class="fa fa-check-square"></i> Setujui');
        } else {
            $('#jumlah-bayar').hide();
            $('#setuju').html('membatalkan persetujuan');
            $('#btn-submit-update').addClass('btn-danger');
            $('#btn-submit-update').removeClass('btn-success');
            $('#btn-submit-update').html('<i class="fa fa-times"></i> Batalkan Persetujuan');
        }
        $('#modal-update').modal('show');
    }

</script>
@endsection