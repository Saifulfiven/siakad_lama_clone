<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sia, Rmt, DB, Session;

class KuesionerController extends Controller
{

    public function index(Request $r)
    {

    	if ( !empty($r->smt) ) {
    		Session::set('kues_semester', $r->smt);
    	}

    	if ( !empty($r->prodi) ) {
    		Session::set('kues_prodi', $r->prodi);
    	}

    	if ( !empty($r->jenis) ) {
    		Session::set('kues_jenis', $r->jenis);
    	}

    	if ( !Session::get('kues_semester') ) {
    		$this->setSessionFilter();
    	}

    	$query = $this->jadwalKuliah()
    				->where('jdk.id_prodi', Session::get('kues_prodi'))
    				->where('jdk.id_smt', Session::get('kues_semester'))
    				->where('kuj.ket', Session::get('kues_jenis'));

    	$data['kuesioner'] = $query->get();

    	return view('kuesioner.index', $data);
    }

    private function setSessionFilter()
    {
    	Session::set('kues_semester', Sia::sessionPeriode());
    	$prodi_user = Sia::getProdiUser();
    	Session::set('kues_prodi', @$prodi_user[0]);
    	Session::set('kues_jenis', 'MID');
    }

    public function detail(Request $r)
    {

        $peserta_kelas = DB::table('nilai as n')
                            ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                            ->where('jdk.id', $r->id_jdk)
                            ->count();

        $jml_responden = DB::table('kues')
                            ->where('id_jdk', $r->id_jdk)
                            ->where('id_dosen', $r->id_dosen)
                            ->count();

    	$komponen = DB::table('kues as k')
    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
    				->select('kk.*')
    				->where('k.id_jdk', $r->id_jdk)
    				->where('k.id_dosen', $r->id_dosen)
    				->where('kk.jenis', 'pg')
    				->groupBy('kk.id')
    				->orderBy('kk.urutan')
    				->get();
    	$no = 1;
    	$jml_hasil = 0;
    	$total_nilai = 0; ?>

        <br>
        <a href="<?= route('kues_hasil_cetak_detail', [
            'id_jdk' => $r->id_jdk, 
            'id_dosen' => $r->id_dosen, 
            'jenis' => Session::get('kues_jenis'),
            'dosen' => $r->dosen,
            'matakuliah' => $r->matakuliah,
            'kelas' => $r->id_kls,
            'ruangan' => $r->ruangan,
            'prodi' => $r->prodi,
            ]) ?>" target="_blank" class="btn btn-sm btn-primary pull-right">
            <i class="fa fa-print"></i> Cetak
        </a>

        <table class="table table-bordered">
        	<?php foreach( $komponen as $ko ) { ?>
        		<thead class="custom">
	            	<tr>
	            		<th style="text-align: left">
	            			<?= $no++ ?>. <?= $ko->judul == 'blank' ? 'Kriteria' : $ko->judul ?>
	            		</th>
                        <th>Total Skor</th>
                        <th>Grade</th>
	            	</tr>
	            </thead>
                    <?php
                    $isi = DB::table('kues as k')
		    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
		    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
		    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
		    				->select('kki.id','kki.pertanyaan','kh.penilaian')
		    				->where('k.id_jdk', $r->id_jdk)
		    				->where('k.id_dosen', $r->id_dosen)
		    				->where('kk.id', $ko->id)
		    				->where('kh.penilaian','<>', 0)
		    				->groupBy('kki.id')
		    				->get();
		    		
                    $subtot_nilai = 0;
		    		$subtot_nilai = 0;
		    		$no2 = 1;

                	foreach( $isi as $is ) { 

                		$nilai = DB::table('kues as k')
		    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
		    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
		    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
		    				->select('kki.pertanyaan','kh.penilaian')
		    				->where('k.id_jdk', $r->id_jdk)
		    				->where('k.id_dosen', $r->id_dosen)
		    				->where('kki.id', $is->id)
		    				->where('kh.penilaian','<>', 0);

		    			$sum_nilai = $nilai->sum('kh.penilaian');
		    			$count_nilai = $nilai->count();
                		?>

                		<?php $jml_hasil += $count_nilai; ?>
                        <?php $grade_1 = !empty($count_nilai) ? $sum_nilai / $count_nilai : 0 ?>

                    	<tr>
                    		<td><?= $no2++ ?>. <?= $is->pertanyaan ?></td>
                            <td width="80" align="center"><?= $sum_nilai ?></td>
                    		<td width="40"><?= round($grade_1, 2) ?></td>
                    	</tr>
                    	<?php $subtot_nilai += $sum_nilai; ?>
                    
                    <?php } ?>
                    <?php $total_nilai += $subtot_nilai; ?>
            <?php } ?>
        </table>

        <?php $rata_rata = !empty($total_nilai) ? $total_nilai / $jml_hasil : 0 ?>

        <table>
            <tr>
                <td><b>JUMLAH RESPONDEN</b></td>
                <td>: <b><?= $jml_responden ?></b> dari <b><?= $peserta_kelas ?></b> orang</td>
        	<tr>
            	<td width="150"><b>TOTAL SKOR</b></td>
            	<td>: <b><?= $total_nilai ?></b></td>
            </tr>
            <tr>
            	<td><b>RATA-RATA</b></td>
            	<td>: <b><?= round($rata_rata, 1) ?></b></td>
            </tr>
            <tr>
            	<td><b>GRADE</b></td>
            	<td>: <b><?= Sia::kuesionerGrade($rata_rata) ?></b></td>
            </tr>
        </table>

        <div style="padding-top: 30px">
        	<h4><b><u>Kritik/Saran</u></b></h4>
        	<?php
        		$responden = DB::table('kues')
        					->where('id_jdk', $r->id_jdk)
		    				->where('id_dosen', $r->id_dosen)
		    				->get();
		    	$no3 = 1; ?>
		    	<ol style="list-style-type: none">
		    	<?php foreach( $responden as $res ) { 

		    		$komen = DB::table('kues as k')
		    				->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
		    				->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
		    				->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
		    				->select('kki.pertanyaan','kh.penilaian_text')
		    				->where('k.id_jdk', $r->id_jdk)
		    				->where('k.id_dosen', $r->id_dosen)
		    				->where('k.id', $res->id)
		    				->where('kh.penilaian', 0)
		    				->where('kh.penilaian_text','<>','')
		    				->get(); ?>
		    		<li style="padding-top: 10px;">
		    			<ul style="list-style-type: circle;padding-left: 15px">
		    				<?php foreach( $komen as $kom ) { ?>
			    				<li><b><?= $kom->pertanyaan ?></b><br>
                    				<?= $kom->penilaian_text ?>
                    			</li>
                    		<?php } ?>
                		</ul>
                	</li>
               	<?php } ?>
               	</ol>
        </div>

        <div>
        	<br>
            <button type="button" data-dismiss="modal" class="btn btn-submit btn-sm pull-right">Keluar</button>
        </div>
    
        <?php
    }

    public function cetak(Request $r)
    {

    	$query = $this->jadwalKuliah()
    				->where('jdk.id_prodi', Session::get('kues_prodi'))
    				->where('jdk.id_smt', Session::get('kues_semester'))
    				->where('kuj.ket', Session::get('kues_jenis'));

    	$data['kuesioner'] = $query->get();

    	$data['prodi'] = DB::table('prodi')
    					->where('id_prodi', Session::get('kues_prodi'))
    					->first();
    					
    	$data['ta'] = DB::table('semester')
    					->where('id_smt', Session::get('kues_semester'))
    					->first();

    	return view('kuesioner.cetak', $data);
    }

    public function cetakPerMk(Request $r)
    {
        $data['peserta_kelas'] = DB::table('nilai as n')
                            ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                            ->where('jdk.id', $r->id_jdk)
                            ->count();

        $data['jml_responden'] = DB::table('kues')
                            ->where('id_jdk', $r->id_jdk)
                            ->where('id_dosen', $r->id_dosen)
                            ->count();

        $data['komponen'] = DB::table('kues as k')
                    ->leftJoin('kues_hasil as kh', 'kh.id_kues', 'k.id')
                    ->leftJoin('kues_komponen_isi as kki', 'kh.id_komponen_isi', 'kki.id')
                    ->leftJoin('kues_komponen as kk', 'kk.id', 'kki.id_komponen')
                    ->select('kk.*')
                    ->where('k.id_jdk', $r->id_jdk)
                    ->where('k.id_dosen', $r->id_dosen)
                    ->where('kk.jenis', 'pg')
                    ->groupBy('kk.id')
                    ->orderBy('kk.urutan')
                    ->get();

        $data['ta'] = DB::table('semester')
                        ->where('id_smt', Session::get('kues_semester'))
                        ->first();

        return view('kuesioner.cetak-per-mk', $data);
    }

    private function jadwalKuliah()
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
                ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                ->join('kues as ku', 'ku.id_jdk','=', 'jdk.id')
                ->join('dosen as dos', 'ku.id_dosen', 'dos.id')
                ->join('kues_jadwal as kuj', 'kuj.id','=', 'ku.id_kues_jadwal')
                ->select('jdk.*','mkur.id_mk','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'pr.jenjang','pr.nm_prodi','mkur.smt', 'dos.id as id_dosen','dos.gelar_depan','dos.nm_dosen','dos.gelar_belakang', 'ku.id_dosen', 'ku.id as id_kues')
                ->where('jdk.jenis', 1)
                ->orderBy('jdk.kode_kls')
                ->orderBy('dos.nm_dosen','asc')
                ->groupBy('ku.id');
        return $data;
    }
}
