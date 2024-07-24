@extends('layouts.app')

@section('title','Tambah Aktivitas MBKM')

{{-- {{ dd($peserta) }} --}}
@section('content')
    <div id="overlay"></div>
    <div id="content">
      <div class="row">
        <div class="col-md-12">
          <section class="panel">
            <header class="panel-heading">
              Tambah Aktivitas MBKM
                <div class="pull-right" style="margin-top: 0px">
                    <a href="{{ route('mbkm_edit', ['id' => $mb->id])}}" class="btn btn-warning btn-sm" style="margin: 3px 3px" ><i class="fa fa-pencil"></i> UBAH</a>
                    <a href="{{ route('mbkm') }}" style="margin: 3px 3px" class="btn btn-success btn-sm"><i class="fa fa-list"></i> DAFTAR</a>
                </div>
            </header>

            <div class="panel-body" style="padding-top: 3px">

                <div class="row">
                    <div class="col-md-12">
                        {{ Rmt::alertSuccess() }}
                    </div>

                    <div class="col-md-12">

                        <div class="table-responsive">

                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="left_column">Program Studi <font color="#FF0000">*</font></td>
                                        <td colspan="9">:  {{ $mb->nm_prodi }} {{ $mb->jenjang }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left_column">Semester <font color="#FF0000">*</font></td>
                                        <td colspan="9">: {{ $mb->nm_smt }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left_column">Jenis Aktivitas <font color="#FF0000">*</font></td>
                                        <td>: {{ $mb->nm_aktivitas }}</td>
                                    </tr>
                                    @if ( $mb->id_jenis_aktivitas == 99 )
                                        <tr>
                                            <td class="left_column">Jenis Pertukaran <font color="#FF0000">*</font></td>
                                            <td>: {{ $mb->jenis_pertukaran }}</td>
                                        </tr> 
                                    @endif
                                    <tr>
                                        <td class="left_column">Judul <font color="#FF0000">*</font></td>
                                        <td>: {{ $mb->judul_aktivitas }}</td>
                                    </tr>
                                     <tr>
                                        <td class="left_column">Lokasi</td>
                                        <td>: {{ $mb->lokasi }}</td>
                                    </tr>
                                     <tr>
                                        <td class="left_column">Nomor SK Tugas</td>
                                        <td>: {{ $mb->no_sk }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left_column" width="20%">Tanggal SK Tugas</td>
                                        <td>: {{ !empty($mb->tgl_sk) ? Rmt::formatTgl($mb->tgl_sk) : '-' }}</td>
                                    </tr>
                                     <tr>
                                        <td class="left_column">Jenis Anggota</td>
                                        <td>:  {{ Rmt::anggotaMbkm($mb->jenis_anggota) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left_column">Keterangan</td>
                                        <td>: {!! $mb->keterangan !!}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>


                    </div>

                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <div class="tabbable">
                            <ul class="nav nav-tabs" data-provide="tabdrop">
                                <li{{ Session::get('tab') == 'peserta' ? " class=active" : '' }}><a href="#peserta" data-toggle="tab">Peserta</a></li>
                                <li{{ Session::get('tab') == 'dosen' ? " class=active" : '' }}><a href="#dosen" data-toggle="tab">Pembimbing</a></li>
                            </ul>
                            <div class="tab-content">
                            
                            <!-- //peserta -->
                                <div class="tab-pane fade {{ Session::get('tab') == 'peserta' ? 'in active' : '' }}" id="peserta">
                                    <div class="table-responsive">

                                        @if ( Sia::canAction($mb->id_smt) )
                                            <div class="alert alert-info">
                                                <form action="{{ route('mbkm_store_peserta') }}" id="form-peserta" method="post">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="id_aktivitas" value="{{ $mb->id }}">
                                                    <div class="table-responsive">
                                                        <table border="0" width="100%" style="min-width: 750px">
                                                            <tr>
                                                                <td width="100px" align="left"><b>NIM/NAMA : </b></td>
                                                                <td width="350px">
                                                                    <div style="position: relative;">
                                                                        <div class="input-icon right"> 
                                                                            <span id="spinner-autocomplete-mhs" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                            <input type="text" class="form-control input-sm" required="" id="autocomplete-mhs" name="nama_mhs" style="max-width: 340px">
                                                                            <input type="hidden" id="id-mhs" name="mahasiswa">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td width="150">
                                                                    <select name="peran" class="form-control input-sm">
                                                                        @if ( $mb->jenis_anggota == 1 )
                                                                            <option value="1">Ketua</option>
                                                                            <option value="2">Anggota</option>
                                                                        @else
                                                                            <option value="3">Personal</option>
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td width="120">
                                                                    <button id="btn-submit-peserta" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> TAMBAHKAN </button>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                </form>
                                            </div>
                                        @endif

                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                            <thead class="custom">
                                                <tr>
                                                    <th width="20px">No.</th>
                                                    <th align="left">NIM</th>
                                                    <th align="left">Nama</th>
                                                    <th>Jenis/Peran</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody align="center">
                                                @foreach( $peserta as $ps )
                                                {{-- {{ dd($ps->id) }} --}}
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td align="left">{{ $ps->mhsreg->nim }}</td>
                                                        <td align="left">{{ $ps->mhsreg->mhs->nm_mhs }}</td>
                                                        <td>{{ Rmt::peranAnggota($ps->jenis_peran) }}</td>
                                                        <td>
                                                            @if ( Sia::adminOrAkademik() )
                                                            <a href="{{ route('mbkm_delete_peserta',['id' => $ps->id]) }}" onclick="return confirm('Anda ingin menghapus peserta ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                            </tbody>
                                        </table>
                                        @if ( count($peserta) == 0 )
                                            <strong>Belum ada peserta</strong>
                                        @endif
                                    </div>
                                </div>
                                <!-- //peserta -->
                                                {{-- {{ dd($data) }} --}}


                                <div class="tab-pane fade {{ Session::get('tab') == 'dosen' ? 'in active' : '' }}" id="dosen">
                                    <div class="table-responsive">

                                        @if ( Sia::adminOrAkademik() && Sia::canAction($mb->id_smt) )
                                            <a href="javascript::void(0)" data-toggle="modal" data-target="#modal-tambah" data-backdrop="static" data-keyboard="false"  class="btn btn-xs btn-primary pull-right"><i class="fa fa-plus"></i> TAMBAH DOSEN PEMBIMBING</a>
                                        @endif

                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                            <thead class="custom">
                                                <tr>
                                                    <th width="20px">No.</th>
                                                    <th width="250">Nama Dosen</th>
                                                    <th>Pembimbing Ke</th>
                                                    <th>Kategori kegiatan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody align="center">
                                                @foreach( $dosen as $d )
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td align="left">{{ $d->gelar_depan.' '.$d->nm_dosen.', '.$d->gelar_belakang }}</td>
                                                        <td>{{ $d->pembimbing_ke }}</td>
                                                        <td align="left">{{ $d->nm_kategori }}</td>
                                                        <td>
                                                            @if ( Sia::adminOrAkademik() )
                                                                <span class="tooltip-area">
                                                                    <a href="{{ route('mbkm_delete_dosen',['id' => $d->id]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @if ( count($dosen) == 0 )
                                            <strong>Pembimbing belum ada</strong>
                                        @endif
                                    </div>

                                </div>
                                <!-- //dosen -->
                                
                            </div>
                            <!-- //tab-content -->
                        </div>
                    </div>
                </div>


            </div>

        </div>
      </div>
    </div>

    <div id="modal-tambah" class="modal fade" style="top:30%" data-width="800" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Dosen Pembimbing</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('mbkm_store_dosen') }}" id="form-dosen" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id_aktivitas" value="{{ $mb->id }}">

                <div class="table-responsive">

                    <table border="0" class="table-hover table-form" width="100%">
                        <tr>
                            <td width="150px">Nama Dosen</td>
                            <td>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-dosen" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" id="autocomplete-dosen" required="">
                                        <input type="hidden" id="id-dosen" name="dosen">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Pembimbing Ke</td>
                            <td>
                                <input type="number" maxlength="2" size="2" class="form-control mw-1" name="pembimbing_ke" value="1">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Kategori Kegiatan</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                @foreach( Sia::kegiatanMbkm() as $val )
                                    <label class="radio-inline">
                                        <input type="radio" name="kegiatan" value="{{ $val->id }}" checked=""> {{ $val->nm_kategori }}
                                    </label>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                
                <hr>

                <button type="submit" id="btn-submit-dosen" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
            </form>
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
            serviceUrl: '{{ route('mbkm_mhs') }}',
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

        $('#autocomplete-dosen').autocomplete({
            serviceUrl: '{{ route('mbkm_dosen') }}',
            lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                return re.test(suggestion.value);
            },
            onSearchStart: function(data) {
                $('#spinner-autocomplete-dosen').show();
            },
            onSearchComplete: function(data) {
                $('#spinner-autocomplete-dosen').hide();
            },
            onSelect: function(suggestion) {
                $('#id-dosen').val(suggestion.data);
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

    function submit(modul)
    {

        var options = {
            beforeSend: function() 
            {
                $("#btn-submit-"+modul).attr('disabled','');
                $("#btn-submit-"+modul).html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
            },
            success:function(data, status, message) {
                window.location.reload();
            },
            error: function(data, status, message)
            {
                $("#btn-submit-"+modul).removeAttr('disabled');
                if ( modul == 'peserta' ) {
                    $("#btn-submit-"+modul).html("<i class='fa fa-plus'></i> TAMBAHKAN");
                } else {
                    $("#btn-submit-"+modul).html("<i class='fa fa-save'></i> SIMPAN");
                }

                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2(modul, pesan);
            }
        }; 

        $('#form-'+modul).ajaxForm(options);
    }
    submit('peserta');
    submit('dosen');
</script>
@endsection