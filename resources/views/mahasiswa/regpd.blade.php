@extends('layouts.app')

@section('title', 'Histori Pendidikan Mahasiswa')

@section('content')

    <div id="content">
        <div class="row">
            <div class="col-md-12">
                <section class="panel" style="padding-bottom: 50px">
                    <header class="panel-heading">
                        Histori pendidikan
                    </header>

                    <div class="panel-body" style="padding: 3px 3px;">

                        @include('mahasiswa.link-cepat')

                        <div class="col-md-9">

                            {{ Rmt::AlertSuccess() }}
                            {{ Rmt::AlertError() }}
                            {{ Rmt::AlertErrors($errors) }}

                            <div class="row" style="margin-bottom: 13px">
                                <div class="col-md-12">
                                    <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px"
                                        class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" style="padding-right: 0">
                                    <div class="table-responsive">
                                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                            <tbody class="detail-mhs">
                                                <tr>
                                                    <th width="130px">Nama</th>
                                                    <td>: {{ $mhs->nm_mhs }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Jenis Kelamin</th>
                                                    <td>: {{ Sia::nmJenisKelamin($mhs->jenkel) }}</td>
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
                                                    <th>Tempat, Tgl lahir</th>
                                                    <td>: {{ $mhs->tempat_lahir }}, {{ Rmt::formatTgl($mhs->tgl_lahir) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Agama</th>
                                                    <td>: {{ $mhs->nm_agama }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <table border="0" width="100%" style="margin-bottom: 10px">
                                        <tr>
                                            <td width="110px">&nbsp;</td>
                                            @if (Sia::akademik())
                                                <td><button class="btn btn-primary btn-sm pull-right md-ajax-load"
                                                        data-toggle="modal" data-target="#modal-tambah"><i
                                                            class="fa fa-plus"></i> TAMBAH PENDIDIKAN</button></td>
                                            @endif
                                        </tr>
                                    </table>

                                    <div class="table-responsive">
                                        <table cellpadding="0" cellspacing="0" border="0"
                                            class="table table-bordered table-striped table-hover">
                                            <thead class="custom">
                                                <tr>
                                                    <th width="20px">No.</th>
                                                    <th>NIM</th>
                                                    <th>Jenis daftar</th>
                                                    <th>Periode</th>
                                                    <th>Tgl Masuk</th>
                                                    <th>Prodi</th>
                                                    <th>Konsent.</th>
                                                    <th>Kelas</th>
                                                    <th>PA</th>
                                                    <th>Kurikulum</th>
                                                    @if (Sia::adminOrAkademik())
                                                        <th style="max-width:80px">Aksi</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody align="center">
                                                <?php $no = 1; ?>
                                                @foreach ($regpd as $r)
                                                {{-- {{ dd($r) }} --}}
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $r->nim }}</td>
                                                        <td>{{ $r->nm_jns_pendaftaran }}</td>
                                                        <td>{{ $r->periode }}</td>
                                                        <td>{{ Rmt::formatTgl($r->tgl_daftar) }}</td>
                                                        <td>{{ $r->jenjang }} {{ $r->nm_prodi }}</td>
                                                        <td>{{ $r->nm_konsentrasi }}</td>
                                                        <td>{{ $r->kode_kelas }}</td>
                                                        <td>{{ $r->pa }}</td>
                                                        <td>{{ $r->mulai_berlaku }}</td>
                                                        @if (Sia::adminOrAkademik())
                                                            <td>
                                                                <a href="javascript:void(0)"
                                                                    onclick="edit('{{ $r->id_regpd }}')"
                                                                    class="btn btn-warning btn-xs" title="Ubah"><i
                                                                        class="fa fa-pencil"></i></a>
                                                                @if (Sia::canAction($r->semester_mulai) && $no > 1)
                                                                    <a href="{{ route('mahasiswa_regpddelete', ['id' => $r->id_regpd]) }}"
                                                                        onclick="return confirm('Anda ingin menghapus data ini')"
                                                                        class="btn btn-danger btn-xs" title="Hapus"><i
                                                                            class="fa fa-times"></i></a>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div>

    <div id="modal-tambah" class="modal fade" data-width="600" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Tambah histori pendidikan</h4>
        </div>
        <div class="modal-body">
            <form action="{{ route('mahasiswa_regpdstore') }}" id="form-regpd" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ $id_mahasiswa }}">
                <div class="table-responsive">
                    <table border="0" class="table-hover table-form">
                        <tr>
                            <td width="160px">Periode</td>
                            <td>
                                {{ Sia::sessionPeriode('nama') }}
                            </td>
                        </tr>
                        <tr>
                            <td width="160px">Tgl masuk <span>*</span></td>
                            <td>
                                <input type="date" class="form-control mw-2" name="tgl_masuk">
                            </td>
                        </tr>
                        <tr>
                            <td>Jenis pendaftaran <span>*</span></td>
                            <td>
                                <select class="form-control select-jenis-daftar" name="jns_pendaftaran">
                                    <option value="">-- Pilih jenis pendaftaran --</option>
                                    @foreach ($jnsPendaftaran as $jp)
                                        <option value="{{ $jp->id_jns_pendaftaran }}">{{ $jp->nm_jns_pendaftaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Jalur pendaftaran</td>
                            <td>
                                <select class="form-control" name="jalur_pendaftaran">
                                    <option value="">-- Pilih jalur pendaftaran --</option>
                                    {{-- @foreach ($jalurMasuk as $jm)
                                        <option value="{{ $jm->id_jalur_masuk }}">{{ $jm->nm_jalur_masuk }}</option>
                                    @endforeach --}}
                                    <option value="3">Penelusuran Minat dan Kemampuan (PMDK)</option>
                                    <option value="4">Prestasi</option>
                                    <option value="9">Program Internasional</option>
                                    <option value="11">Program Kerjasama Perusahaan/Institusi/Pemerintah</option>
                                    <option value="12">Seleksi Mandiri</option>
                                    <option value="13">Ujian Masuk Bersama Lainnya</option>
                                    <option value="14">Seleksi Nasional Berdasarkan Tes (SNBT)</option>
                                    <option value="15">Seleksi Nasional Berdasarkan Prestasi (SNBP)</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Program studi <span>*</span></td>
                            <td>
                                <select class="form-control select-prodi" name="prodi">
                                    <option value="">-- Pilih program studi --</option>
                                    @foreach ($prodi as $pr)
                                        <option value="{{ $pr->id_prodi }}">{{ $pr->jenjang }} {{ $pr->nm_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Konsentrasi</td>
                            <td>
                                <span class="konsentrasi">
                                    <select class="form-control" disabled="">
                                        <option value="">-- Pilih konsentrasi --</option>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Kurikulum <span>*</span></td>
                            <td>
                                <span class="kurikulum">
                                    <select class="form-control" disabled="">
                                        <option value="">-- Pilih kurikulum --</option>
                                    </select>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Waktu Kuliah <span>*</span></td>
                            <td>
                                <select class="form-control" name="waktu_kuliah">
                                    <option value="">-- Pilih waktu kuliah --</option>
                                    @foreach (Sia::waktuKuliah() as $val)
                                        <option value="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Dosen PA <span>*</span></td>
                            <td>
                                <div style="position: relative">
                                    <div class="input-icon right">
                                        <span id="spinner-autocomplete-pa" style="display: none"><i
                                                class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" id="autocomplete-ajax-pa" class="form-control">
                                    </div>
                                    <input type="hidden" name="dosen_pa" id="dosen-pa">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Kode Kelas <span>*</span></td>
                            <td>
                                <div style="position: relative">
                                    <div class="input-icon right">
                                        <input type="text" name="kode_kelas" data-always-show="true"
                                            class="form-control mw-1" maxlength="5" required>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Biaya Masuk <span>*</span></td>
                            <td>
                                <input type="number" name="biaya_masuk" value="{{ old('biaya_masuk') }}"
                                    class="form-control">
                            </td>
                        </tr>
                        <tr class="pindahan" style="display: none;">
                            <td>Asal perguruan tinggi <span>*</span></td>
                            <td>
                                <div style="position: relative">
                                    <div class="input-icon right">
                                        <span id="spinner-autocomplete-pt" style="display: none"><i
                                                class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" id="autocomplete-ajax-pt" class="form-control">
                                    </div>
                                    <input type="hidden" name="id_perguruan_tinggi" id="pt-asal">
                                </div>
                            </td>
                        </tr>
                        <tr class="pindahan" style="display: none;">
                            <td>Prodi asal <span>*</span></td>
                            <td>
                                <div id="prodi-asal">
                                    <select class="form-control" disabled="">
                                        <option value="">-- Pilih perguruan tinggi dahulu --</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <hr>
                <button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i
                        class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
            </form>
        </div>
    </div>

    <div id="modal-edit" class="modal fade" data-width="600" tabindex="-1" style="top: 10%">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4>Ubah histori pendidikan</h4>
        </div>
        <div class="modal-body" id="form-edit">

        </div>
    </div>

    <div id="modal-error" class="modal fade" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                    class="fa fa-times"></i></button>
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
    <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.mockjax.js"></script>
    <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.autocomplete.js"></script>
    <script>
        $(function() {
            $('#nav-mini').trigger('click');

            $(document).on('change', '.select-prodi', function() {
                val = this.value;
                $('.konsentrasi').html('<i class="fa fa-spinner fa-spin"></i>');
                $.ajax({
                    url: '{{ route('mahasiswa_konsentrasi') }}',
                    data: {
                        prodi: val
                    },
                    success: function(result) {
                        $('.konsentrasi').html(result);
                        getKurikulum(val);
                    },
                    error: function(err, data, msg) {
                        alert(msg);
                    }
                });
            });

            $(document).on('change', '.select-jenis-daftar', function() {
                if (this.value.trim() != 1) {
                    $('.pindahan').show();
                } else {
                    $('.pindahan').hide();
                }
            });

            $('#autocomplete-ajax-pa').autocomplete({
                serviceUrl: '{{ route('jdk_dosen') }}',
                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase),
                        'gi');
                    return re.test(suggestion.value);
                },
                onSearchStart: function(data) {
                    $('#spinner-autocomplete-pa').show();
                },
                onSearchComplete: function(data) {
                    $('#spinner-autocomplete-pa').hide();
                },
                onSelect: function(suggestion) {
                    $('#dosen-pa').val(suggestion.data);
                },
                onInvalidateSelection: function() {}
            });

            $('#autocomplete-ajax-pt').autocomplete({
                serviceUrl: '{{ route('get_pt') }}',
                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase),
                        'gi');
                    return re.test(suggestion.value);
                },
                onSearchStart: function(data) {
                    $('#spinner-autocomplete-pt').show();
                },
                onSearchComplete: function(data) {
                    $('#spinner-autocomplete-pt').hide();
                },
                onSelect: function(suggestion) {
                    $('#pt-asal').val(suggestion.data);
                    getProdiAsal(suggestion.data);
                },
                onInvalidateSelection: function() {}
            });

        });

        function getProdiAsal(id_pt_asal) {
            $('#prodi-asal').html('<i class="fa fa-spinner fa-spin"></i> mengambil program studi');
            $.ajax({
                url: '{{ route('get_all_prodi') }}',
                data: {
                    id_pt: id_pt_asal
                },
                success: function(result) {
                    $('#prodi-asal').html(result);
                },
                error: function(err, data, msg) {
                    alert(msg);
                }
            });

        }

        function getKurikulum(prodi) {
            $('.kurikulum').html('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                url: '{{ route('mahasiswa_get_kurikulum') }}',
                data: {
                    prodi: prodi
                },
                success: function(result) {
                    $('.kurikulum').html(result);
                },
                error: function(err, data, msg) {
                    alert('Gagal mengambil kurikulum' + msg);
                }
            });
        }

        function edit(id) {
            $('#form-edit').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            $('#modal-edit').modal('show');
            $.ajax({
                url: '{{ route('mahasiswa_editregpd') }}/' + id,
                data: {
                    id: id
                },
                success: function(data) {
                    $('#form-edit').html(data);
                },
                error: function(err, data, msg) {
                    alert(msg)
                }
            });
        }

        function showMessage(pesan) {
            $('#overlay').hide();
            $('.ajax-message').html(pesan);
            $('#modal-error').modal('show');

            $('#btn-submit').removeAttr('disabled');
            $('#btn-submit').html('<i class="fa fa-floppy-o"></i> SIMPAN');
        }

        function submit(modul) {
            var options = {
                beforeSend: function() {
                    $('#overlay').show();
                    $("#btn-submit").attr('disabled', '');
                    $("#btn-submit").html(
                    "<i style='width:14.5px' class='fa fa-spinner fa-spin'></i> Menyimpan...");
                },
                success: function(data, status, message) {
                    if (data.error == 1) {
                        showMessage(data.msg);
                    } else {
                        window.location.reload();
                    }
                },
                error: function(data, status, message) {
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for (i = 0; i < respon.length; i++) {
                        pesan += "- " + respon[i] + "<br>";
                    }
                    if (pesan == '') {
                        pesan = message;
                    }
                    showMessage(pesan);
                }
            };

            $('#form-' + modul).ajaxForm(options);
        }
        submit('regpd');
    </script>
@endsection
