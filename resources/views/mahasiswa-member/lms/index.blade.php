@extends('layouts.app')

@section('title','Learning Management System')

@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
    <section class="panel">
        <header class="panel-heading" style="padding-bottom: 20px">
            E-Learning

            <div class="pull-right">
                <!-- 
                <select class="form-custom" onchange="ubahJenis(this.value)">
                    <option value="1" {{ Session::get('jeniskrs_in_jdk') == 1 ? 'selected':'' }}>PERKULIAHAN</option>
                    <option value="2" {{ Session::get('jeniskrs_in_jdk') == 2 ? 'selected':'' }}>SP</option>
                </select> -->
                <select class="form-custom" onchange="ubahSmt(this.value)">
                    @foreach( $semester as $s )
                        <option value="{{ $s->id_smt }}" {{ Session::get('smt_in_lms') == $s->id_smt ? 'selected':'' }}>{{ $s->nm_smt }}</option>
                    @endforeach
                </select>
            </div>
        </header>
          
        <div class="panel-body" style="padding: 20px 3px;">

            <div class="col-md-12">

                {{ Rmt::alertSuccess() }}
                
                <div class="row">

                    
                    <?php $jadwalku = '' ?>

                    @foreach( $jadwal as $r )
                        
                        <?php $jadwalku .= $r->id.',' ?>

                        <div class="col-md-4 col-sm-6">
                            
                            <a href="{{ route('mhs_lms_detail', [$r->id, $r->id_dosen]) }}">
                                <div class="well bg-theme-inverse" style="cursor: pointer;">
                                    <div class="widget-tile">
                                        <section>
                                            <h5>{{ str_limit($r->nm_mk,35) }}</h5>
                                            
                                            <h2><span style="font-size: 14px">{{ Rmt::hari($r->hari) }}</span> {{ substr($r->jam_masuk,0,5) }}</h2>
                                            <div class="progress progress-xs progress-white progress-over-tile">
                                                <div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="10" aria-valuemax="10"></div>
                                            </div>
                                            <label class="progress-label label-white" style="font-size: 12px">
                                                <?= $r->dosen ?>
                                            </label>
                                        </section>
                                        <div class="hold-icon"><i class="fa fa-folder-o"></i></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    @foreach( $jadwal_sp as $r )

                        <div class="col-md-4 col-sm-6">
                            
                            <a href="{{ route('mhs_lms_detail', [$r->id, $r->id_dosen]) }}">
                                <div class="well bg-primary" style="cursor: pointer;">
                                    <div class="widget-tile">
                                        <section>
                                            <h5>{{ str_limit($r->nm_mk,35) }}</h5>
                                            
                                            <h2><span style="font-size: 14px">{{ Rmt::hari($r->hari) }}</span> {{ substr($r->jam_masuk,0,5) }}</h2>
                                            <div class="progress progress-xs progress-white progress-over-tile">
                                                <div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="10" aria-valuemax="10"></div>
                                            </div>
                                            <label class="progress-label label-white" style="font-size: 12px">
                                                <?= $r->dosen ?>
                                            </label>
                                        </section>
                                        <div class="hold-icon"><i class="fa fa-folder-o"></i></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    <?php $count_jdk = count($jadwal) + count($jadwal_sp); ?>
                    @if ( empty($count_jdk) )
                        Belum ada jadwal anda pada periode {{ Sia::sessionPeriode('nama') }}
                    @endif

                </div>

            </div>

        </div>
    </section>

    <section class="panel">
        <header class="panel-heading">
            Kelas Undangan
            <div class="pull-right">
                <button class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal-gabung">Gabung ke Kelas Lain</button>
            </div>
        </header>

        <div class="panel-body">

            <div class="row">
            <?php $kelas_undangan = DB::table('lms_peserta_undangan as pu')
                ->join('jadwal_kuliah as jdk', 'jdk.id', 'pu.id_jadwal')
                ->join('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->join('matakuliah as mk', 'jdk.id_mk','=','mk.id')
                ->join('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                ->join('ruangan as r', 'jdk.ruangan','=','r.id')
                ->join('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                ->join('semester as smt','jdk.id_smt','=','smt.id_smt')
                ->select('pu.id as id_undangan', 'pu.aktif','jdk.*','mk.kode_mk','mk.nm_mk','mk.sks_mk',
                        'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
                        'jk.jam_keluar','smt.nm_smt','mkur.smt','smt.nm_smt',
                    DB::raw('
                        (SELECT group_concat(distinct dos.gelar_depan," ", dos.nm_dosen, ", ",dos.gelar_belakang SEPARATOR \'<br>\') from dosen_mengajar as dm
                        left join dosen as dos on dm.id_dosen=dos.id
                        where dm.id_jdk=jdk.id) as dosen'),
                    DB::raw('(select id_dosen from dosen_mengajar where id_jdk=jdk.id limit 1) as id_dosen'),
                    DB::raw('(SELECT COUNT(*) as agr from nilai where id_jdk=jdk.id) as terisi'))
                ->where('jdk.jenis', 1)
                ->where('pu.id_peserta', Sia::sessionMhs())
                ->where('jdk.id_smt', Sia::sessionPeriode())
                ->orderBy('jdk.hari','asc')
                ->orderBy('jk.jam_masuk','asc')
                ->get(); ?>
                 

                @if ( count($kelas_undangan) > 0 )
                    @foreach( $kelas_undangan as $ku )
                        <div class="col-md-4 col-sm-6">
                            
                            @if ( $ku->aktif == '0' )
                                <div class="label label-warning" style="position: absolute; top: 2px; right: 18px">
                                    Menunggu Persetujuan
                                </div>

                                <a href="{{ route('mhs_lms_batal_gabung', [Sia::sessionMhs(), $ku->id]) }}" onclick="return confirm('Anda ingin membatalkan permintaan masuk ke kelas ini?')" class="btn btn-danger btn-xs" style="position: absolute; bottom: 20px; right: 18px;z-index: 9999">
                                    <i class="fa fa-times"></i> Batalkan
                                </a>
                            @endif
                            <div class="well <?= $ku->aktif == 1 ? 'bg-primary':'bg-info' ?>" style="cursor: pointer;" onclick="go('<?= $ku->id ?>', '<?= $ku->id_dosen ?>', '<?= $ku->aktif == '0' ? false : true ?>')">

                                <div class="widget-tile">
                                    <section>
                                        <h5>{{ str_limit($ku->nm_mk,35) }}</h5>
                                        
                                        <h2><span style="font-size: 14px">{{ Rmt::hari($ku->hari) }}</span> {{ substr($ku->jam_masuk,0,5) }}</h2>
                                        <div class="progress progress-xs progress-white progress-over-tile">
                                            <div class="progress-bar  progress-bar-white" aria-valuetransitiongoal="10" aria-valuemax="10"></div>
                                        </div>
                                        <label class="progress-label label-white" style="font-size: 12px">
                                            <?= $ku->dosen ?>
                                        </label>
                                    </section>
                                    <div class="hold-icon"><i class="fa fa-folder-o"></i></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        Semua kelas yang anda masuki selain dari Jadwal Kuliah anda di atas akan berada di sini. Klik <b>Gabung ke Kelas Lain</b> untuk bergabung. Atau beritahu dosen Matakuliah bersangkutan untuk memasukkan anda pada kelasnya.
                    </div>
                    Anda tidak mempunyai kelas undangan
                @endif
        
            </div>
        </div>
    </section>

    </div>
</div>

<div id="modal-gabung" class="modal fade" data-width="750" style="top: 25%" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="myModalLabel">Gabung dengan kelas lain</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body" style="padding-top: 0;min-height: 200px">
        <div class="row">
            <div class="col-lg-12">
                <label class="control-label">Cari Matakuliah</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="cari-mk">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-cari-mk" type="button"><i class="fa fa-search"></i> Cari</button>
                    </span>
                </div>
                <hr>
                <div id="konten-cari-mk"></div>                        
            </div>
        </div>
    </div>
</div>
@endsection

@section('registerscript')
<script>
    $(function () {

        $('.btn-cari-mk').click(function(){
            var cari = $('#cari-mk').val();
            var div = $('#konten-cari-mk');

            div.html('<br><center><i class="fa fa-spinner fa-spin" style="font-size: 20px"></i></center>');

            $.ajax({
                url: '{{ route('mhs_lms_get_jadwal') }}',
                data : { 
                    preventCache : new Date(),
                    cari: cari,
                    jadwalku: '{{ $jadwalku }}'
                },
                success: function(data){
                    div.html(data);
                },
                error: function(data,status,message){
                    var respon = parseObj(data.responseJSON);
                    var pesan = '';
                    for ( i = 0; i < respon.length; i++ ){
                        pesan += "- "+respon[i]+"<br>";
                    }
                    if ( pesan == '' ) {
                        pesan = message;
                    }
                    showMessage2('',pesan);
                }
            });
        });
        
    });


    function ubahJenis(id)
    {
        window.location.href='{{ route('mhs_jdk') }}?ubah_jenis='+id;
    }

    function go(id_jdk, id_dsn, aktif = true)
    {
        if ( !aktif ) {
            showMessage2('', 'Sedang menunggu persetujuan dosen');
            return;
        }
        window.location.href='{{ route('mhs_lms_detail') }}/'+id_jdk+'/'+id_dsn;
    }

    function gabung(id)
    {
        $('.jdk-'+id).html('<i class="fa fa-spin fa-spinner"></i> Memproses..');
        $('.jdk-'+id).attr('disabled','');

        $.ajax({
            url: '{{ route('mhs_lms_gabung') }}/'+id,
            data : { 
                    preventCache : new Date()
                },
            success: function(data){
                location.reload();
            },
            error: function(data,status,message){
                var respon = parseObj(data.responseJSON);
                var pesan = '';
                for ( i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = message;
                }
                showMessage2('',pesan);
            }
        });
    }

    function ubahSmt(id)
    {
        window.location.href='{{ route('mhs_lms') }}?smt='+id;
    }

</script>
@endsection