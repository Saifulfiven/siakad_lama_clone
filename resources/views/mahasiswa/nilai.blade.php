@extends('layouts.app')

@section('title','KHS / Nilai Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
        <section class="panel" style="padding-bottom: 50px">
            <header class="panel-heading">
              KHS / Nilai Mahasiswa
            </header>
              
            <div class="panel-body" style="padding: 3px 3px;">
                
                @include('mahasiswa.link-cepat')

                <div class="col-md-9">

                    <div class="row" style="margin-bottom: 13px">
                        <div class="col-md-12">
                            <a href="{{ route('mahasiswa') }}" style="margin: 3px 3px" class="btn btn-success btn-sm pull-right"><i class="fa fa-list"></i> DAFTAR</a>
                        </div>
                    </div>
                    
                    {{ Rmt::AlertSuccess() }}
                    @php
                        $nim = $data['nim'];
                        $mhs = $data['mhs'];
                        $semester = $data['semester'];
                        $krs = $data['krs'];
                        $mbkm = $data['mbkm'];
                        $ipk = $data['ipk'];
                        $id_mahasiswa = $data['id_mahasiswa'];
                    @endphp
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
                                            <th width="130px">Tahun Akademik</th>
                                            <td>:
                                                <select class="form-custom" onchange="ubahSmt(this.value)">
                                                    @foreach( $semester as $s )
                                                        <option value="{{ $s->id_smt }}" {{ Session::get('smt_in_nilai') == $s->id_smt ? 'selected':'' }}>{{ $s->nm_smt }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jenis</th>
                                            <td>:
                                                <select class="form-custom" onchange="ubahJenis(this.value)">
                                                    <option value="1" {{ Session::get('jeniskrs_in_nilai') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                                                    <option value="2" {{ Session::get('jeniskrs_in_nilai') == 2 ? 'selected':'' }}>SP</option>
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
                                        <tr>
                                            <th>Semester</th><td>: {{ Sia::posisiSemesterMhs($mhs->semester_mulai, Session::get('smt_in_nilai')) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="0" width="100%" style="margin-bottom: 10px">
                                    <tr>
                                        <td width="110px">&nbsp;</td>
                                        <td><a href="{{ route('mahasiswa_nilai_cetak') }}" class="btn btn-primary btn-xs pull-right" target="blank"><i class="fa fa-print"></i> CETAK KHS</a></td>
                                    </tr>
                                </table>

                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">Kode MK</th>
                                        <th rowspan="2">Nama MK</th>
                                        <th rowspan="2">SKS</th>
                                        <th colspan="2">Nilai</th>
                                        <th rowspan="2">SKS * N.Indeks</th>
                                    </tr>
                                    <tr>
                                        <th>Huruf</th>
                                        <th>Indeks</th>
                                    </tr>
                                    </thead>
                                    <tbody align="center">

                                        <?php $total_sks = 0 ?>
                                        <?php $total_nilai = 0 ?>
                                        <?php $total_bobot = 0 ?>
                                        <?php $count_krs = $krs->count() + $mbkm->count() ?>
                                        @if ( $count_krs > 0 )

                                            @foreach( $krs as $r )
                                                <?php $kumulatif = $r->sks_mk * $r->nilai_indeks ?>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $r->kode_mk }}</td>
                                                    <td align="left">{{ $r->nm_mk }}</td>
                                                    <td>{{ $r->sks_mk }}</td>
                                                    @if ( Sia::admin() )
                                                        <td>
                                                            <select onchange="updateNilai('{{ $r->id_prodi }}','{{ $r->id }}', this.value)" class="form-control" style="width: 70px" id="nil-{{ $r->id }}">
                                                                <option value="-">--</option>
                                                                @foreach( Sia::skalaNilai($r->id_prodi) as $sn )
                                                                    <option value="{{ $sn->nilai_huruf }}" {{ $sn->nilai_huruf == $r->nilai_huruf ? 'selected':'' }}>{{ $sn->nilai_huruf }}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="spinner-{{ $r->id }}"></span>
                                                        </td>
                                                    @else
                                                        <td>{{ $r->nilai_huruf }}</td>
                                                    @endif
                                                    <td>{{ number_format($r->nilai_indeks,2) }}</td>
                                                    <td>{{ number_format($kumulatif,2) }}</td>
                                                </tr>
                                                <?php
                                                    $total_sks += $r->sks_mk;
                                                    $total_nilai += $r->nilai_indeks;
                                                    $total_bobot += $kumulatif;
                                                ?>
                                            @endforeach

                                            @foreach( $mbkm as $m )
                                                <?php $kumulatif = $m->sks_mk * $m->nil_indeks ?>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $m->kode_mk }}</td>
                                                    <td align="left">{{ $m->nm_mk }}</td>
                                                    <td>{{ $m->sks_mk }}</td>
                                                    @if ( Sia::admin() )
                                                        <td>
                                                            <select onchange="updateNilai('{{ $m->id_prodi }}','{{ $m->id }}', this.value)" class="form-control" style="width: 70px" id="nil-{{ $m->id }}">
                                                                <option value="-">--</option>
                                                                @foreach( Sia::skalaNilai($m->id_prodi) as $sn )
                                                                    <option value="{{ $sn->nilai_huruf }}" {{ $sn->nilai_huruf == $m->nil_huruf ? 'selected':'' }}>{{ $sn->nilai_huruf }}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="spinner-{{ $m->id }}"></span>
                                                        </td>
                                                    @else
                                                        <td>{{ $m->nil_huruf }}</td>
                                                    @endif
                                                    <td>{{ number_format($m->nil_indeks,2) }}</td>
                                                    <td>{{ number_format($kumulatif,2) }}</td>
                                                </tr>
                                                <?php
                                                    $total_sks += $m->sks_mk;
                                                    $total_nilai += $m->nil_indeks;
                                                    $total_bobot += $kumulatif;
                                                ?>
                                            @endforeach
                                            <tr>
                                                <th colspan="3">Total</th>
                                                <th>{{ $total_sks }}</th>
                                                <th></th>
                                                <th>{{ number_format($total_nilai, 2) }}</th>
                                                <th>{{ number_format($total_bobot, 2) }}</th>
                                            </tr>

                                        @else
                                            <tr><td colspan="7">Tidak ada data</td></tr>
                                        @endif
                                    </tbody>
                                </table>

                                @if ( $count_krs > 0 && Session::get('jeniskrs_in_nilai') == 1 )

                                    <?php $ipks = Sia::ipk($ipk->tot_nilai, $ipk->tot_sks) ?>

                                    <table class="table table-bordered" style="width:450px">
                                        <tr>
                                            <th>Keterangan</th>
                                            <th>SKS Semester</th>
                                            <th>SKS Telah Dilulusi</th>
                                        </tr>
                                        <tr>
                                            <td>Kumulatif Semester</td>
                                            <td>{{ $total_sks }}</td>
                                            <td>{{ $ipk->tot_sks }}</td>
                                        </tr>
                                        <tr>
                                            <td>Indeks Prestasi Semester (IPS)</td>
                                            <td colspan="2">{{ Sia::ipk($total_bobot, $total_sks) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Indeks Prestasi Kumulatif (IPK)</td>
                                            <td colspan="2">{{ $ipks }}</td>
                                        </tr>
                                        <tr>
                                            <td>Max. Beban SKS Semester Depan</td>
                                            <td colspan="2">{{ Sia::maxSks($ipks) }}</td>
                                        </tr>
                                    </table>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </section>
    </div>
  </div>
</div>

@endsection

@section('registerscript')
<script>
    $(function(){
        $('#nav-mini').trigger('click');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        })
    });
    
    function ubahNim(id)
    {
        window.location.href='{{ route('mahasiswa_nilai',['id' => $mhs->id]) }}?ubah_nim='+id;
    }

    function ubahSmt(id)
    {
        window.location.href='{{ route('mahasiswa_nilai',['id' => $mhs->id]) }}?smt='+id;
    }
    function ubahJenis(id)
    {
        window.location.href='{{ route('mahasiswa_nilai') }}/{{ $mhs->id }}?ubah_jenis='+id;
    }

    function updateNilai(id_prodi, id_nilai, nilai)
    {
        let spinner = $('.spinner-'+id_nilai);
        let divNil = $('#nil-'+id_nilai);
        spinner.html('<i class="fa fa-spin fa-spinner"></i>');
        divNil.attr('disabled','');

        $.ajax({
            url: '{{ route('mahasiswa_nilai_update') }}',
            type: 'post',
            data: {id_prodi: id_prodi, id_nilai: id_nilai, nilai: nilai},
            success: function(result) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(data, status, msg) {
                alert(msg);
            },
            complete: function(res)
            {
                spinner.html('');
                divNil.removeAttr('disabled');
            }
        })
    }
</script>
@endsection