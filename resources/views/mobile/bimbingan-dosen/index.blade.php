@extends('mobile.layouts.app')

@section('title','Bimbingan Detail')


@section('content')
<div id="overlay"></div>

<div id="content" class="content-bimbingan">

    <div class="row">
            
        <div class="col-md-12">
            <section class="panel">

                <div class="panel-body">

                    <?php
                        $jabatan = '';

                        if ( !empty(@$bimbingan[0]) ) {

                            $status_bimbingan = $data_bim->pembimbing_1 + $data_bim->pembimbing_2;

                            if ( $id_dosen == $bimbingan[0]->id_dosen ) {
                                $jabatan = 'KETUA';
                                $tab = 1;
                                $persetujuan = $data_bim->pembimbing_1;
                            } elseif ( $id_dosen == $bimbingan[1]->id_dosen ) {
                                $jabatan = 'SEKRETARIS';
                                $tab = 2;
                                $persetujuan = $data_bim->pembimbing_2;
                            } else {
                                $tab = 1;
                                $jabatan = '';
                                $persetujuan = '';
                            }

                        }
                    ?>

                    <div class="col-md-12" style="padding-right: 0;">

                        {{ Rmt::AlertError() }}
                        {{ Rmt::AlertSuccess() }}

                        <div class="row">
                            <div class="col-md-6" style="padding: 0">
                                <div class="table-responsive">
                                    <table border="0" class="table">
                                        
                                        <tr>
                                            <td colspan="2"><b>Pilih jenis seminar</b>
                                                <div style="padding-top: 10px">
                                                    <select class="form-control mw-2" onchange="filter('jenis', this.value)">
                                                        @foreach( $menguji as $m )
                                                            <option value="{{ $m->jenis }}" {{ Session::get('bim.jenis') == $m->jenis ? 'selected':'' }}>{{ Rmt::jnsSeminar($m->jenis) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="110"><b>Tahun akademik</b></td>
                                            <td>
                                                : {{ Rmt::namaTa($id_smt) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Mahasiswa</b></td>
                                            <td>: {{ $mhs->mhs->nm_mhs }} - {{ $mhs->nim }}</td>
                                        </tr>
                                        @if ( !empty(@$bimbingan[0]) )
                                            <tr>
                                                <td width="110">Pembimbing I</td>
                                                <td>: {{ Sia::namaDosen(@$bimbingan[0]->gelar_depan, @$bimbingan[0]->nm_dosen, @$bimbingan[0]->gelar_belakang) }}</td>
                                            </tr>

                                            <tr>
                                                <td>Pembimbing II</td>
                                                <td>: {{ Sia::namaDosen(@$bimbingan[1]->gelar_depan, @$bimbingan[1]->nm_dosen, @$bimbingan[1]->gelar_belakang) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                <b>Judul</b><br>
                                                {{ @$bimbingan[0]->judul_tmp }}
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if ( !empty(@$bimbingan[0]) )
                                <div class="col-md-6" style="margin-bottom: 20px;padding: 0">
                                    <div class="well {{ empty($data_bim) ? '':'bg-theme-inverse' }}" style="padding: 5px 10px 5px 10px">
                                        <h3><strong>File</strong> Skripsi/Tesis </h3>

                                        @if ( !empty($data_bim) && !empty($data_bim->file) )
                                            
                                            <p>
                                                <div class="icon-resources">
                                                    <?php $icon = Rmt::icon($data_bim->file); ?>
                                                    <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                                </div>

                                                <?php $param = ['id' => $data_bim->id,'jenis' => $data_bim->jenis, 'id_mhs_reg' => $mhs->id] ?>

                                                <a href="{{ route('m_dsn_bim_download', $param) }}" title="Download File" style="color: #fff">
                                                    <i class="fa fa-external-link"></i> {{ $data_bim->file }}
                                                </a>
                                                <!-- <br><br> -->
                                                <!-- <i class="fa fa-clock-o"></i><i> {{ Rmt::Waktulalu($data_bim->created_at) }}</i> -->
                                            </p>
                                                
                                            
                                        @else
                                            <p>Belum ada file diupload</p>
                                        @endif

                                        <div class="flip">
                                        </div>
                                    </div>

                                    <div class="well {{ $status_bimbingan == 2 ? 'bg-success' : 'bg-info' }}" style="padding: 5px 10px 5px 10px">
                                        <h3>
                                            <strong>Status</strong> Bimbingan - 
                                            @if ( $status_bimbingan == 2 )
                                                <small style="color: #fff"><i class="fa fa-check-square"></i> Selesai</small>
                                            @else
                                                <small style="color: #fff"><i class="fa fa-refresh"></i> Dalam Proses</small>
                                            @endif
                                        </h3>
                                        <p>
                                            Status Pembimbing I&nbsp; : 
                                            <?= $data_bim->pembimbing_1 == '1' ? '<b><i class="fa fa-check"></i> Selesai</b>' : '<b>Belum selesai</b>' ?><br>
                                            Status Pembimbing II : 
                                            <?= $data_bim->pembimbing_2 == '1' ? '<b><i class="fa fa-check"></i> Selesai</b>' : '<b>Belum selesai</b>' ?>
                                        </p>

                                        <form action="{{ route('m_dsn_bim_selesai') }}" method="post" id="form-persetujuan">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id" value="{{ $data_bim->id }}">
                                            <input type="hidden" name="id_dosen" value="{{ $id_dosen }}">
                                            <input type="hidden" name="id_mhs_reg" value="{{ $data_bim->id_mhs_reg }}">
                                            <input type="hidden" name="jabatan" value="{{ $jabatan }}">
                                            <input type="hidden" name="jenis" value="{{ Session::get('bim.jenis') }}">
                                            <input type="hidden" name="id_smt" value="{{ $id_smt }}">

                                            @if ( $persetujuan == '1' )

                                                @if ( $status_bimbingan != 2 )
                                                    
                                                    <input type="hidden" name="value" value="0">

                                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Anda ingin membatalkan persetujuan bimbingan mahasiswa ini telah selesai?')">
                                                        <i class="fa fa-refresh"></i>
                                                        Batalkan persetujuan
                                                    </button>

                                                @endif

                                            @else
                                                   
                                                <input type="hidden" name="value" value="1">

                                                <button class="btn btn-primary btn-sm" onclick="return confirm('Anda menganggap bimbingan mahasiswa ini telah selesai?')">
                                                    <i class="fa fa-check-square"></i>
                                                    Setujui untuk seminar
                                                </button>

                                            @endif
                                        
                                        </form>

                                    </div>

                                </div>
                            @endif

                        </div>

                        <div class="row">
                            @if ( !empty(@$bimbingan[0]) )

                                <p><b>RIWAYAT BIMBINGAN "{{ Rmt::jnsSeminar(Session::get('bim.jenis')) }}"</b></p>
                                
                                @if ( !empty($data_bim) )

                                    <?php 
                                        
                                        $riwayat_1 = App\Bimbingandetail::where('id_bimbingan_mhs', $data_bim->id)
                                                    ->where('jabatan_pembimbing', @$bimbingan[0]->jabatan)
                                                    ->get(); 

                                        $riwayat_2 = App\Bimbingandetail::where('id_bimbingan_mhs', $data_bim->id)
                                                    ->where('jabatan_pembimbing', @$bimbingan[1]->jabatan)
                                                    ->get(); 
                                    ?>
                                
                                    <div class="tabbable">
                                        <ul class="nav nav-tabs" data-provide="tabdrop">
                                            <li {{ $tab == 1 ? 'class=active':'' }}>
                                                <a href="#pbb1" data-toggle="tab">Pembimbing I</a>
                                            </li>
                                            <li {{ $tab == 2 ? 'class=active':'' }}>
                                                <a href="#pbb2" data-toggle="tab">Pembimbing II</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                        
                                            <div class="tab-pane fade {{ $tab == 1 ? 'in active' : '' }}" id="pbb1">

                                                <div class="table-responsive">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                                        <thead class="custom">
                                                            <tr>
                                                                <th width="20px">No.</th>
                                                                <th>Tanggal</th>
                                                                <th>Sub Pokok Bahasan</th>
                                                                <th>Komentar/Saran</th>
                                                                <th>Lampiran</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody align="center">
                                                            @foreach( $riwayat_1 as $r1 )
                                                                <?php $last_no = $loop->iteration; ?>
                                                                <tr>
                                                                    <td>{{ $last_no }}</td>
                                                                    <td>{{ Carbon::parse($r1->tgl_bimbingan)->format('d/m/Y') }}</td>
                                                                    <td align="left">{{ $r1->sub_bahasan }}</td>
                                                                    <td align="left">{!! $r1->komentar !!}</td>
                                                                    <td>
                                                                        @if ( !empty($r1->file) )
                                                                            <?php $param = ['id' => $r1->id, 'id_bim' => $r1->id_bimbingan_mhs, 'jenis' => @$bimbingan[0]->jenis]; ?>
                                                                            <a href="{{ route('m_dsn_bim_lampiran', $param) }}">Buka lampiran</a>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ( $id_dosen == $bimbingan[0]->id_dosen && $persetujuan != 1 )
                                                                            <span class="tooltip-area">
                                                                                <?php $link_edit = route('m_dsn_bimbingan_edit', ['id' => $r1->id, 'id_dosen' => $id_dosen]) ?>
                                                                                <a href="{{ $link_edit }}?ta={{ $data_bim->id_smt }}&mhs={{ $bimbingan[0]->id_mhs_reg }}&jenis={{ Session::get('bim.jenis') }}" class="btn btn-sm btn-warning btn-loading" title="Ubah komentar"><i class="fa fa-pencil"></i></a> &nbsp; 
                                                                                <?php $link_delete = route('m_dsn_bimbingan_delete', ['id' => $r1->id, 'id_dosen' => $id_dosen, 'jenis' => Session::get('bim.jenis')]) ?>
                                                                                <a href="{{ $link_delete }}" onclick="return confirm('Anda ingin menghapus komentar ini?')" title="Hapus Komentar" class="btn btn-sm btn-danger btn-loading"><i class="fa fa-times"></i></a> &nbsp; 
                                                                            </span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ( count($riwayat_1) == 0 )
                                                                <tr>
                                                                    <td colspan="5">Belum ada data</td>
                                                                </tr>
                                                            @endif

                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if ( $id_dosen == $bimbingan[0]->id_dosen )
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div style="margin: 0 auto;margin-top: 20px;text-align: center;">
                                                                <?php $param = ['id' => $data_bim->id, 'id_mhs_reg' => $bimbingan[0]->id_mhs_reg, 'id_dosen' => $id_dosen] ?>

                                                                @if ( $persetujuan == '0' )
                                                                    <a href="{{ route('m_dsn_bimbingan_add', $param) }}?jb={{ $jabatan }}&ta={{ $data_bim->id_smt }}&jenis={{ Session::get('bim.jenis') }}" class="btn btn-primary btn-loading">
                                                                        <i class="fa fa-plus"></i>
                                                                        Tambahkan Komentar
                                                                    </a>
                                                                @else

                                                                    <div style="color: green">
                                                                        <i class="fa fa-check-square"></i> Bimbingan Selesai
                                                                    </div>

                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>

                                            <div class="tab-pane fade {{ $tab == 2 ? 'in active' : '' }}" id="pbb2">
                                                <div class="table-responsive">
                                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                                        <thead class="custom">
                                                            <tr>
                                                                <th width="20px">No.</th>
                                                                <th>Tanggal</th>
                                                                <th>Sub Pokok Bahasan</th>
                                                                <th>Komentar/Saran</th>
                                                                <th>Lampiran</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody align="center">
                                                            @foreach( $riwayat_2 as $r2 )
                                                                <?php $last_no = $loop->iteration; ?>
                                                                <tr>
                                                                    <td>{{ $last_no }}</td>
                                                                    <td>{{ Carbon::parse($r2->tgl_bimbingan)->format('d/m/Y') }}</td>
                                                                    <td align="left">{{ $r2->sub_bahasan }}</td>
                                                                    <td align="left">{!! $r2->komentar !!}</td>
                                                                    <td>
                                                                        @if ( !empty($r2->file) )
                                                                            <?php $param = ['id' => $r2->id, 'id_bim' => $r2->id_bimbingan_mhs, 'jenis' => @$bimbingan[0]->jenis]; ?>
                                                                            <a href="{{ route('m_dsn_bim_lampiran', $param) }}">Buka lampiran</a>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ( $id_dosen == @$bimbingan[1]->id_dosen )
                                                                            <span class="tooltip-area">
                                                                                <?php $link_edit = route('m_dsn_bimbingan_edit', ['id' => $r2->id, 'id_dosen' => $id_dosen]) ?>
                                                                                <a href="{{ $link_edit }}?ta={{ $data_bim->id_smt }}&mhs={{ $bimbingan[0]->id_mhs_reg }}&jenis={{ Session::get('bim.jenis') }}" class="btn btn-sm btn-warning btn-loading" title="Ubah komentar"><i class="fa fa-pencil"></i></a> &nbsp; 
                                                                                <?php $link_delete = route('m_dsn_bimbingan_delete', ['id' => $r2->id, 'id_dosen' => $id_dosen, 'jenis' => Session::get('bim.jenis')]) ?>
                                                                                <a href="{{ $link_delete }}" onclick="return confirm('Anda ingin menghapus komentar ini?')" title="Hapus Komentar" class="btn btn-sm btn-danger btn-loading"><i class="fa fa-times"></i></a> &nbsp; 
                                                                            </span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ( count($riwayat_2) == 0 )
                                                                <tr>
                                                                    <td colspan="5">Belum ada data</td>
                                                                </tr>
                                                            @endif

                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if ( $id_dosen == @$bimbingan[1]->id_dosen )
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div style="margin: 0 auto;margin-top: 20px;text-align: center;">


                                                                <?php $param = ['id' => $data_bim->id, 'id_mhs_reg' => $bimbingan[1]->id_mhs_reg, 'id_dosen' => $id_dosen] ?>

                                                                @if ( $persetujuan == '0' )
                                                                    <a href="{{ route('m_dsn_bimbingan_add', $param) }}?jb={{ $jabatan }}&ta={{ $data_bim->id_smt }}&jenis={{ Session::get('bim.jenis') }}" class="btn btn-primary">
                                                                        <i class="fa fa-plus"></i>
                                                                        Tambahkan Komentar
                                                                    </a>
                                                                @else

                                                                    <div style="color: green">
                                                                        <i class="fa fa-check-square"></i> Bimbingan Selesai
                                                                    </div>

                                                                @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>

                                        </div>
                                    </div>

                                @else

                                    <div class="alert alert-warning">
                                        Belum ada data, silahkan tunggu hingga mahasiswa mengupload file skripsi/tesisnya.
                                    </div>
                                @endif

                            @else

                                <hr>
                                <p>Belum ada data</p>

                            @endif
                        </div>
                    </div>
                </div>

            </section>
        </div>
        
    </div>
    <!-- //content > row-->
@endsection

@section('registerscript')
<script>

    $(document).ready(function(){

        $('#form-persetujuan').submit(function(){
            $('#caplet-overlay').show();
        })
    });

</script>

@endsection