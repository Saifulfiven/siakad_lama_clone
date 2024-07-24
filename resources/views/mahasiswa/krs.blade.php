@extends('layouts.app')

@section('title','KRS Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
          KRS Mahasiswa
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">
            
            @include('mahasiswa.link-cepat')

            <div class="col-md-9">

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
                                            <select class="form-custom" onchange="ubahNim(this.value)">
                                                @foreach( $nim as $n )
                                                    <option value="{{ $n->id }}" {{ $mhs->id_reg_pd == $n->id ? 'selected':'' }}>{{ $n->nim }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Periode</th><td>: {{ Sia::sessionPeriode('nama') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Jadwal</th>
                                        <td>:
                                            <select class="form-custom" onchange="ubahJenis(this.value)">
                                                <option value="1" {{ Session::get('krs_jeniskrs') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                                                <option value="2" {{ Session::get('krs_jeniskrs') == 2 ? 'selected':'' }}>SP</option>
                                            </select>
                                        </td>
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
                                        <th>Nama</th><td>: {{ $mhs->nm_mhs }}</td>
                                    </tr>
                                    <tr>
                                        <th>Angkatan</th><td>: {{ substr($mhs->nim,0,4) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi</th><td>: {{ $mhs->jenjang }} - {{ $mhs->nm_prodi }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class="table-responsive">

                            @if ( Sia::adminOrAkademik() && Sia::sessionPeriode() >= $mhs->id_smt )
                                <br>
                                <form action="{{ route('mahasiswa_krs_store') }}" id="form-jadwal" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id_mhs_reg" value="{{ $mhs->id_reg_pd }}">
                                    <input type="hidden" name="jenis_jadwal" value="{{ Session::get('krs_jeniskrs') }}">

                                    <!-- Jika Semester pendek -->
                                    @if ( Session::get('krs_jeniskrs') == 2 )
                                        <!-- Hanya tampilkan penambahan jadwal sp jika semester genap -->
                                        @if ( substr(Sia::sessionPeriode(), 4,1) == 2 )

                                            @if ( empty($status_bayar) )
                                        
                                                <div class="alert alert-danger">Mahasiswa ini belum membayar</div>
                                            
                                            @else
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td width="100px" align="left"><b>Jadwal Antara : </b></td>
                                                        <td width="500px">
                                                            <div style="position: relative;">
                                                                <div class="input-icon right"> 
                                                                    <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                    <input type="text" class="form-control input-sm" required="" id="autocomplete-jadwal">
                                                                    <input type="hidden" id="id-jdk" name="jadwal">
                                                                    <input type="hidden" id="sks" name="sks">
                                                                    <input type="hidden" id="semester_mk" name="semester_mk">
                                                                    <input type="hidden" id="id_mk" name="id_mk">
                                                                    <input type="hidden" id="jam" name="jam">
                                                                    <input type="hidden" id="hari" name="hari">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td width="120px">
                                                            &nbsp; &nbsp; 
                                                            <button id="btn-submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> TAMBAHKAN </button>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @endif

                                        @endif
                                    @else

                                        <!-- Jika belum membayar -->
                                        @if ( empty($status_bayar) )
                                        
                                            <div class="alert alert-danger">Mahasiswa ini belum membayar</div>
                                        
                                        @else
                                            <!-- Jika krs perkuliahan 
                                                dan masih dalam masa krsan -->
                                            @if ( Rmt::dateBetween($jdw_akademik->awal_krs, $jdw_akademik->akhir_krs) )
                                                <table border="0" width="100%">
                                                    <tr>
                                                        <td width="100px" align="left"><b>Jadwal Kuliah : </b></td>
                                                        <td width="500px">
                                                            <div style="position: relative;">
                                                                <div class="input-icon right"> 
                                                                    <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                                                    <input type="text" class="form-control input-sm" required="" id="autocomplete-jadwal">
                                                                    <input type="hidden" id="id-jdk" name="jadwal">
                                                                    <input type="hidden" id="sks" name="sks">
                                                                    <input type="hidden" id="semester_mk" name="semester_mk">
                                                                    <input type="hidden" id="id_mk" name="id_mk">
                                                                    <input type="hidden" id="jam" name="jam">
                                                                    <input type="hidden" id="hari" name="hari">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td width="120px">
                                                            &nbsp; &nbsp; 
                                                            <button id="btn-submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> TAMBAHKAN </button>
                                                        </td>
                                                    </tr>
                                                </table>
                                            @else
                                                <div class="alert bg-darkorange">
                                                    Masa KRS telah berakhir/belum terbuka
                                                </div>
                                            @endif

                                        @endif

                                    @endif
                                </form>
                            @endif

                            <div class="ajax-message" style="font-size: 14px;padding-bottom: 10px"></div>
                            {{ Rmt::AlertSuccess() }}

                            <a href="{{ route('mhs_krs_lap_cetak', ['id' => $mhs->id_reg_pd])}}?nama={{ trim($mhs->nm_mhs) }}&nm_periode={{ Sia::sessionPeriode('nama') }}&id_smt={{ Sia::sessionPeriode() }}" target="_blank" class="btn btn-primary btn-sm">Cetak KRS</a>

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Waktu</th>
                                        <th>Nama matakuliah</th>
                                        <th>SKS</th>
                                        <th>Kelas / Ruang</th>
                                        <th>Dosen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody align="center" id="data-jadwal">
                                    <tr>
                                        <td colspan="7" align="center">
                                            <i class="fa fa-spinner fa-spin"></i> Sedang memuat data...
                                        </td>
                                    </tr>
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

@endsection

@section('registerscript')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
<script>
    $(function () {

        $('#nav-mini').trigger('click');
        
        loadJadwal();

            $('#autocomplete-jadwal').autocomplete({
                serviceUrl: '{{ route('mahasiswa_get_krs') }}?prodi={{ $mhs->id_prodi }}&jeniskrs={{ Session::get('krs_jeniskrs')}}',
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
                    $('#id-jdk').val(suggestion.data);
                    $('#sks').val(suggestion.sks);
                    $('#semester_mk').val(suggestion.semester_mk);
                    $('#id_mk').val(suggestion.id_mk);
                    $('#jam').val(suggestion.jam);
                    $('#hari').val(suggestion.hari);
                },
                onInvalidateSelection: function() {
                }
            });

            var options = {
                beforeSend: function() 
                {
                    $('#overlay').show();
                    $("#btn-submit").attr('disabled','');
                    $("#btn-submit").html("<i style='width:14.5px' class='fa fa-spinner fa-spin'></i>");
                },
                success:function(data, status, message) {
                    if ( data.error == 1 ) {
                        showMessage(data.msg);
                    } else {
                        loadJadwal();
                        $.notific8('Berhasil menyimpan data',{ life:5000,horizontalEdge:"bottom", theme:"success" ,heading:" Pesan "});
                        $('#overlay').hide();
                        $('.ajax-message').hide();
                        $('#btn-submit').removeAttr('disabled');
                        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
                        $('#form-jadwal').resetForm();
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

    function showMessage(pesan)
    {
        $('#overlay').hide();
        $('.ajax-message').hide();
        $('.ajax-message').html(pesan);
        $('.ajax-message').fadeIn(500);

        $('#btn-submit').removeAttr('disabled');
        $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
    }

    function ubahNim(id)
    {
        window.location.href='{{ route('mahasiswa_krs') }}/{{ $mhs->id }}?ubah_nim='+id;
    }

    function ubahJenis(id)
    {
        window.location.href='{{ route('mahasiswa_krs') }}/{{ $mhs->id }}?ubah_jenis='+id;
    }

    function loadJadwal()
    {
        $('#data-jadwal').html('<tr><td colspan="7"><center><i class="fa fa-spinner fa-spin"></i> Sedang memuat data...</center></td></tr>');
        $.ajax({
            url: '{{ route('mahasiswa_load_jadwal') }}',
            success: function(result){
                $('#data-jadwal').html(result);
            },
            error: function(data,status,msg){
                alert('Gagal mengambil data krs/jadwal, muat ulang halaman. '+msg);
            }
        });
    }
</script>
@endsection