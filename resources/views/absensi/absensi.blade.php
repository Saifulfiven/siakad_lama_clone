@extends('layouts.app')

@section('title','Kehadiran Mahasiswa')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading" style="border: none !important">
          Kehadiran Mahasiswa
          <a href="#" onclick="goBack()" style="margin: 3px 3px" class="btn btn-success btn-xs pull-right"><i class="fa fa-list"></i> KEMBALI</a>
        </header>
          
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                
                <div class="row">
                    <div class="col-md-6" style="padding-right: 0">
                        <div class="table-responsive">
                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped">
                                <tbody class="detail-mhs">
                                    <tr>
                                        <th>Nama</th><td>: {{ $mhs->nm_mhs }}</td>
                                    </tr>
                                    <tr>
                                        <th width="130px">NIM</th>
                                        <td>:{{ $mhs->nim }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="130px">Tahun Akademik</th>
                                        <td>:
                                            <select class="form-custom" onchange="ubahSmt(this.value)">
                                                @foreach( $semester as $s )
                                                    <option value="{{ $s->id_smt }}" {{ Session::get('smt_in_absen') == $s->id_smt ? 'selected':'' }}>{{ $s->nm_smt }}</option>
                                                @endforeach
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

                            <a href="" class="btn btn-primary btn-sm pull-right"><i class="fa fa-print"></i> Cetak</a>

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                <thead class="custom">
                                    <tr>
                                        <th width="20px">No.</th>
                                        <th>Waktu</th>
                                        <th>Nama matakuliah</th>
                                        <th>Ruang</th>
                                        <th>Dosen</th>
                                        <th>Kehadiran</th>
                                        <th>Pertemuan ke</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody align="center">
                                    @foreach( $jadwal as $r )
                                        <?php
                                            $abs = DB::table('nilai')
                                                ->select('id','a_1','a_2','a_3','a_4','a_5','a_6','a_7','a_8','a_9','a_10','a_11','a_12','a_13','a_14')
                                                ->where('id_mhs_reg', $mhs->id_reg_pd)
                                                ->where('id_jdk', $r->id)
                                                ->first();
                                            $jml_absen = $abs->a_1 + $abs->a_2 + $abs->a_3 + $abs->a_4 + $abs->a_5 + $abs->a_6 + $abs->a_7 + $abs->a_8 + $abs->a_9 + $abs->a_10 + $abs->a_11 + $abs->a_12 + $abs->a_13 + $abs->a_14;

                                        ?>
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td align="center">
                                                {{ empty($r->hari) ? '-': Rmt::hari($r->hari) }}<br>
                                                    {{ substr($r->jam_masuk,0,5) }} - {{ substr($r->jam_keluar,0,5) }}
                                            </td>
                                            <td align="left">
                                                {{ $r->kode_mk }} <br>
                                                {{ $r->nm_mk }} ({{ $r->sks_mk }} sks)
                                            </td>
                                            <td>{{ $r->nm_ruangan }}</td>
                                            <td align="left"><?= $r->dosen ?></td>
                                            <td>{{ $jml_absen }}</td>
                                            <td>
                                                <a href="#" data-mk="{{ $r->nm_mk }}"
                                                data-id="{{ $abs->id }}"
                                                data-id_mhs_reg="{{ Request::get('id_mhs_reg') }}"
                                                data-dosen="{{ $r->dosen }}" class="btn btn-primary btn-sm open-detail">Lihat</a>
                                            </td>
                                        </tr>
                                    @endforeach
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

<div id="modal-absen" class="modal fade" tabindex="-1" style="height: 600px;max-height: 600px;overflow-y: scroll;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Data kehadiran mahasiswa</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="table-responsive">
            <p><span id="matakuliah"></span> - <span id="dosen"></span></p>
            <a href="#" class="pull-right btn btn-primary btn-xs"><i class="fa fa-print"></i> Cetak</a>
            <table class="table table-bordered table-striped table-hover">
                <thead class="custom">
                    <tr>
                        <th>Pertemuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="konten-absen">
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
        </center>
    </div>
    <!-- //modal-body-->
</div>
@endsection

@section('registerscript')
<script>
    $(function () {
        $('.open-detail').click(function(){
            $('#konten-absen').html('>tr><td colspan="2"><center><i class="fa fa-spin fa-spinner"></i></center></td></tr>');

            var data = $(this);
            var id_nilai = data.data('id');
            var id_mhs_reg = data.data('id_mhs_reg');

            $('#matakuliah').html(data.data('mk'));
            $('#dosen').html(data.data('dosen'));
            $('#modal-absen').modal('show');

            $.ajax({
                url: '{{ route('absen_mhs_detail') }}',
                data: { id_mhs_reg: id_mhs_reg, id_nilai: id_nilai },
                success: function(result){
                    $('#konten-absen').html(result);
                },
                error: function(data,status,msg){
                    console.log(data);
                    alert(msg);
                }
            });
        });
    });

    function ubahSmt(id)
    {
        window.location.href='?smt='+id;
    }
</script>
@endsection