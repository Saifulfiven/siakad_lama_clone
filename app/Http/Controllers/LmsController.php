<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\Dosen, App\Topik;

class LmsController extends Controller
{

    public function index(Request $r)
    {
        if ( !Session::has('lms.smt') ) {
            Session::put('lms.smt', [Sia::sessionperiode()]);
        }

        $query = Sia::jadwalKuliahLms();

        $query->whereNotNull('jdk.id_jam');

        $this->filter($query);

        $data['jadwal'] = $query->orderBy('jdk.created_at','desc')->paginate(20);
	    return view('lms.index', $data);
    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('lms.cari',$r->cari);
        } else {
            Session::pull('lms.cari');
        }

        return redirect(route('lms'));
    }

    public function setFilter(Request $r)
    {
        Session::pull('lms.prodi');
        Session::pull('lms.smt');

        if ( is_array($r->prodi) && count($r->prodi) > 0 ) {
            foreach( $r->prodi as $pr ) {
                Session::push('lms.prodi', $pr);
            }
        }
        
        if ( is_array($r->smt) && count($r->smt) > 0 ) {
            foreach( $r->smt as $smt ) {
                Session::push('lms.smt', $smt);
            }
        }

        if ( $r->remove ) {
            Session::pull('lms');
        }
        return redirect()->back();
    }

    public function filter($query)
    {

        if ( Session::has('lms.cari') ) {
            $query->where(function($q){
                $q->where('mk.kode_mk', 'like', '%'.Session::get('lms.cari').'%')
                    ->orWhere('mk.nm_mk', 'like', '%'.Session::get('lms.cari').'%')
                    ->orWhere('jdk.kode_kls', 'like', '%'.Session::get('lms.cari').'%');
            });
        }

        if ( Session::has('lms.prodi') ) {
            $query->whereIn('jdk.id_prodi',Session::get('lms.prodi'));
        } else {
            $query->whereIn('jdk.id_prodi', Sia::getProdiUser());
        }
        if ( Session::has('lms.smt') ) {
            $query->whereIn('jdk.id_smt', Session::get('lms.smt'));
        }
    }

    public function detail(Request $r, $id_dosen, $id_jdk)
    {

        $jenis = Session::has('lms.jenis') ? Session::get('lms.jenis') : $r->jenis;

        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id_jdk)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id)->toArray();
        
        $data['id_jdk'] = $id_jdk;
        $data['dosen'] = Dosen::find($id_dosen);

        $data['jml_pertemuan'] = Sia::jmlPertemuan($id_jdk);

        return view('lms.detail', $data);
    }

    public function materiView(Request $r, $id_materi, $id_dosen)
    {
        $materi = DB::table('lms_materi as m')
                ->leftJoin('lms_bank_materi as bm', 'm.id_bank_materi', 'bm.id')
                ->where('m.id', $id_materi)
                ->where('m.id_dosen', $id_dosen)
                ->select('m.*','bm.file')
                ->first();

        if ( !empty($materi) ) {

            $file_materi = !empty($materi->file) ? $materi->file : 'undefined';

            $file = config('app.lms-materi').'/'.$id_dosen.'/'.$file_materi;
            if ( file_exists($file) ) {
                return Response::file($file);
            } else {
                echo "<center><h4>File tidak ditemukan</h4></center>";
            }

        } else {
            echo 'Tidak ada data';
        }
    }

    public function lmsTopik(Request $r)
    {
        $id_dosen = $r->id_dosen;

        $topik = Topik::where('id_jadwal', $r->id_jadwal)
                        ->where('id_dosen', $id_dosen)
                        ->orderBy('created_at', 'desc')
                        ->get();

        foreach( $topik as $val ) { ?>

            <div class="thread-card" style="<?= $val->is_closed == 1 ? 'border-color: #0aa699':'' ?>">
                <a>
                    <h4 class="font-bold text-gray-900">
                        <?= $val->judul ?>
                        <span class="pull-right">
                            <?php $jawaban =  $val->jawaban()->count(); ?>
                            <i class="fa fa-comment text-gray-600"></i>
                            <?= $jawaban ?>
                        </span>
                    </h4>
                    <p class="text-gray-600">
                        <?= str_limit($val->konten, 150) ?>
                    </p>
                </a>

                <div class="thread-info-avatar">
                    <img src="http://siakad.test/resources/assets/img/avatar.png" class="img-circle w-6 rounded-full mr-3">
                </div>
                <div class="text-gray-600">
                    <a class="text-green-darker mr-2 font-bold">
                        <?php if ( $val->creator == $id_dosen ) { ?>
                            Dosen Matakuliah
                        <?php } else {
                            $mhs = DB::table('mahasiswa_reg as m1')
                                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                                    ->select('m1.nim', 'm2.nm_mhs')
                                    ->where('m1.id', $val->creator)
                                    ->first();
                            echo !empty($mhs) ? $mhs->nm_mhs.' - '.$mhs->nim : '-'; ?>
                        <?php } ?>
                    </a> 
                    <?= Rmt::WaktuLalu($val->created_at) ?>

                </div>
            </div>

        <?php }

        if ( count($topik) == 0 ) { ?>
            <div class="alert alert-info" style="margin-bottom: 0">
                Belum ada data
            </div>
        <?php }
    }

    private function dataPenggunaan()
    {
        $tugas = 'SELECT COUNT(*) from lms_tugas as t 
                    left join jadwal_kuliah as jk on t.id_jadwal = jk.id
                    where t.id_dosen=d.id and jk.id_smt=jdk.id_smt';

        $materi = 'SELECT COUNT(*) from lms_materi as t 
                    left join jadwal_kuliah as jk on t.id_jadwal = jk.id
                    where t.id_dosen=d.id and jk.id_smt=jdk.id_smt';

        $catatan = 'SELECT COUNT(*) from lms_catatan as t 
                    left join jadwal_kuliah as jk on t.id_jadwal = jk.id
                    where t.id_dosen=d.id and jk.id_smt=jdk.id_smt';

        $topik = 'SELECT COUNT(*) from lms_topik as t 
                    left join jadwal_kuliah as jk on t.id_jadwal = jk.id
                    where t.id_dosen=d.id and jk.id_smt=jdk.id_smt';

        $data = DB::table('dosen_mengajar as dm')
                ->join('dosen as d', 'dm.id_dosen', 'd.id')
                ->join('jadwal_kuliah as jdk', 'dm.id_jdk', 'jdk.id')
                ->leftJoin('prodi as pr', 'jdk.id_prodi', 'pr.id_prodi')
                ->select('pr.nm_prodi','pr.jenjang', 'd.id','d.nm_dosen','d.gelar_depan','d.gelar_belakang',
                    DB::raw('('.$tugas.') as tugas'),
                    DB::raw('('.$materi.') as materi'),
                    DB::raw('('.$catatan.') as catatan'),
                    DB::raw('('.$topik.') as topik'));
                // ->where('jdk.id_smt', Sia::sessionPeriode())
                // ->whereIn('jdk.id_prodi', Sia::getProdiUser())
                

        if ( Session::has('lms.prodi') ) {
            $data->whereIn('jdk.id_prodi',Session::get('lms.prodi'));
        } else {
            $data->whereIn('jdk.id_prodi', Sia::getProdiUser());
        }
        if ( Session::has('lms.smt') ) {
            $data->whereIn('jdk.id_smt', Session::get('lms.smt'));
        }

        $query = $data->where('jdk.jenis', 1)
                ->orderBy('d.nm_dosen')
                ->groupBy('dm.id_dosen')
                ->get();

        return $query;
    }

    public function penggunaan(Request $r)
    {
        $data = $this->dataPenggunaan();

        $no = 1; ?>

        <table class="table table-bordered table-hover" id="data-table">
            <thead class="custom">
                <th>No</th>
                <th>Dosen</th>
                <th>Prodi</th>
                <th>Materi</th>
                <th>Tugas</th>
                <th>Catatan</th>
                <th>Topik</th>
                <th>Total</th>
            </thead>
            <tbody align="center">
                <?php foreach( $data as $val ) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td align="left"><?= Sia::namaDosen($val->gelar_depan, $val->nm_dosen, $val->gelar_belakang); ?></td>
                        <td align="left"><?= $val->nm_prodi.' - '.$val->jenjang ?></td>
                        <td><?= $val->materi ?></td>
                        <td><?= $val->tugas ?></td>
                        <td><?= $val->catatan ?></td>
                        <td><?= $val->topik ?></td>
                        <td><?= $val->materi + $val->tugas + $val->catatan + $val->topik ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <hr>
        <a href="<?= route('lms_penggunaan_ekspor') ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Ekspor</a>
        <a href="javascript:;" class="btn btn-danger btn-sm pull-right" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</a>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/dataTables.bootstrap.js"></script>
        <script>
            $(function(){
                $('#data-table').dataTable({
                    "order": [[ 0, 'asc' ]]
                });
            });
        </script>
        <?php
    }

    public function penggunaanEkspor(Request $r)
    {
        $data['dosen'] = $this->dataPenggunaan();

            try {
                Excel::create('Data Penggunaan E-Learning', function($excel)use($data) {

                    $excel->sheet('New sheet', function($sheet)use($data) {

                        $sheet->loadView('lms.excel', $data);

                    });

                })->download('xlsx');
            } catch(\Exception $e) {
                echo $e->getMessage();
            }
    }
}