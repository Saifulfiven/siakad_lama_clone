@extends('layouts.app')

@section('title','Nilai Transfer')

@section('content')

    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
              Nilai Transfer
            </header>
              
            <div class="panel-body" style="padding: 3px 3px;">
                
                @include('mahasiswa.link-cepat')

                <div class="col-md-9">

                    {{ Rmt::AlertSuccess() }}
                    {{ Rmt::AlertError() }}
                    {{ Rmt::AlertErrors($errors) }}

                    <div class="row" style="margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" style="padding-right: 0">
                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                    <tbody class="detail-mhs">
                                        <tr>
                                            <th width="130px">NIM</th>
                                            <td>: 
                                                <select class="form-custom" id="ganti-nim">
                                                    @foreach( $mhs_reg as $val )
                                                        <option value="{{ $val->id }}|{{ $val->nim }}" {{ Session::get('konfersi_data')[0].'|'.Session::get('konfersi_data')[1] == $val->id.'|'.$val->nim ? 'selected':'' }}>{{ $val->nim }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="130px">Nama</th>
                                            <td>: {{ $mhs->nm_mhs }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                           <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                    <tbody class="detail-mhs">
                                        <tr>
                                            <th>Program Studi</th>
                                            <td>: {{ $mhs->jenjang }} {{ $mhs->nm_prodi }}</td>
                                        </tr>
                                        <tr>
                                            <th>Angkatan</th>
                                            <td>: {{ substr($mhs->semester_mulai, 0, 4) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <table border="0" width="100%" style="margin-bottom: 10px">
                                <tr>
                                    <td width="110px">&nbsp;</td>
                                    <td>
                                        <div class="pull-right">
                                                <button class="btn btn-primary btn-sm md-ajax-load"  data-toggle="modal" data-target="#modal-tambah"><i class="fa fa-plus"></i> TAMBAH NILAI KONFERSI</button>
                                            <a href="{{ route('mahasiswa_konfersi_cetak') }}" target="blank" class="btn btn-primary btn-sm md-ajax-load"><i class="fa fa-print"></i> CETAK NILAI TRANSFER</a>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div class="table-responsive">

                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                    <tr>
                                        <th rowspan="2" style="width:5%">No.</th>
                                        <th colspan="4">Nilai PT Asal </th>
                                        <th colspan="5">Konversi Nilai PT Baru (diakui)</th>
                                        <th rowspan="2" width="70px"></th>
                                    </tr>
                                    <tr>
                                      <th>Kode MK</th>
                                      <th>Nama MK</th>
                                      <th>SKS</th>
                                      <th>Nilai<br>Huruf</th>
                                      <th>Kode MK</th>
                                      <th>Nama MK</th>
                                      <th>SKS</th>
                                      <th>Nilai <br>Huruf</th>
                                      <th>Nilai<br>Angka</th>
                                    </tr>
                                    </thead>

                                    <tbody align="center">
                                        @if ( $nilai->count() > 0 )

                                            <?php $tot_sks_t = 0 ?>
                                            <?php $tot_sks_diakui = 0 ?>

                                            @foreach( $nilai as $r )
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $r->kode_mk_asal }}</td>
                                                    <td align="left">{{ $r->nm_mk_asal }}</td>
                                                    <td>{{ $r->sks_asal }}</td>
                                                    <td>{{ $r->nilai_huruf_asal }}</td>
                                                    <td>{{ $r->kode_mk }}</td>
                                                    <td align="left">{{ $r->nm_mk }}</td>
                                                    <td>{{ $r->sks_mk }}</td>
                                                    <td>{{ $r->nilai_huruf_diakui }}</td>
                                                    <td>{{ number_format($r->nilai_indeks,2) }}</td>
                                                    @if ( Sia::adminOrAkademik() )
                                                        <td>
                                                            <a href="{{ route('mahasiswa_konfersi_delete', ['id' => $r->id]) }}" onclick="return confirm('Anda ingin menghapus data ini')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                        </td>
                                                    @endif
                                                </tr>
                                                <?php $tot_sks_t += $r->sks_asal ?>
                                                <?php $tot_sks_diakui += $r->sks_mk ?>
                                            @endforeach

                                            <tr>
                                                <td colspan="3">Jumlah SKS</td>
                                                <td>{{ $tot_sks_t }}</td>
                                                <td colspan="3"></td>
                                                <td>{{ $tot_sks_diakui }}</td>
                                                <td colspan="3"></td>
                                            </tr>

                                        @else
                                            <tr><td colspan="11">Belum ada data</td></tr>
                                        @endif
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
      </div>
    </div>

    <div id="modal-tambah" class="modal fade" data-width="600" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Tambah nilai konfersi</h4>
        </div>
        <div class="modal-body">

            <form action="{{ route('mahasiswa_konfersi_store') }}" method="post" id="form-matakuliah" class="form-horizontal">
                
                {{ csrf_field() }}
                <input type="hidden" name="id_mhs_reg" value="{{ Session::get('konfersi_data')[0] }}">
                <input type="hidden" name="id_prodi" value="{{ $mhs->id_prodi }}">

                <div class="clearfix" style="padding-bottom: 5px"></div>
                <div class="table-responsive">
                    <table border="0" class="table-form" width="100%">
                        <tbody>
                            <tr>
                                <td width="150px">Kode MK Asal <font color="#FF0000">*</font></td>
                                <td>
                                    <input type="text" name="kodemtk_t" class="form-custom" max-length="18" size="18" style="width:30%">
                                </td>
                            </tr>
                            <tr>
                                <td>Mata Kuliah Asal <font color="#FF0000">*</font></td>
                                <td>
                                <input type="text" name="namamtk_t" id="namamtk_t" class="form-custom" maxlength="50" style="width:90%"></td>
                            </tr>
                            <tr>
                                <td>SKS Asal <font color="#FF0000">*</font></td>
                                <td>
                                <input type="number" name="sks_t" id="sks_t" class="form-custom" maxlength="2" size="2" style="width:100px"></td>
                            </tr>
                            <tr>
                                <td>Nilai Huruf Asal <font color="#FF0000">*</font></td>
                                <td>
                                    <input type="text" name="huruf_t" class="form-control" style="width: 50px">
                                </td>
                            </tr>
                            <tr>
                                <td>Mata Kuliah Diakui  <font color="#FF0000">*</font></td>
                                <td>
                                    <div style="position: relative;">
                                        <div class="input-icon right"> 
                                            <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                            <input type="text" class="form-control" id="autocomplete-ajax">
                                            <input type="hidden" id="matakuliah" name="matakuliah">
                                            <input type="hidden" id="sks" name="sks_diakui">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Nilai Huruf Diakui <font color="#FF0000">*</font></td>
                                <td>
                                    <select name="huruf" class="form-custom" style="width: 70px">
                                        <option value="">--</option>
                                        @foreach( Sia::skalaNilai($mhs->id_prodi) as $sn )
                                            <option value="{{ $sn->nilai_huruf }}">{{ $sn->nilai_huruf }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button class="btn btn-sm btn-primary pull-right" id="btn-submit"><i class="fa fa-save"></i> SIMPAN NILAI KONFERSI</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-error" class="modal fade" tabindex="-1">
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
    <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
    <script>
        $(function() {
            $('#nav-mini').trigger('click');

            $('#autocomplete-ajax').autocomplete({
                serviceUrl: '{{ route('mahasiswa_niltransfer_get_mk') }}?id_prodi={{ $mhs->id_prodi }}',
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
                    $('#matakuliah').val(suggestion.data);
                    $('#sks').val(suggestion.sks);
                },
                onInvalidateSelection: function() {
                }
            });

            $('#ganti-nim').change(function(){
                var data = $(this).val();
                var dataArr = data.split('|');
                window.location.href='?id_reg_pd='+dataArr[0]+'&nim='+dataArr[1];
            });

                var options = {
                    beforeSend: function() 
                    {
                        $('#overlay').show();
                        $("#btn-submit").attr('disabled','');
                        $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                    },
                    success:function(data, status, message) {
                        if ( data.error == 1 ) {
                            $("#close-error").show();
                            showMessage(data.msg);
                        } else {
                            window.location.reload();
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
                        $("#close-error").show();
                        showMessage(pesan);
                    }
                }; 

                $('#form-matakuliah').ajaxForm(options);
        });

        function showMessage(pesan)
        {
            $('#overlay').hide();
            $('.ajax-message').html(pesan);
            $('#modal-error').modal('show');

            $('#btn-submit').removeAttr('disabled');
            $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN NILAI KONFERSI');
        }

    </script>
@endsection