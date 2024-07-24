<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Session, Carbon;

class KartuUjianController extends Controller
{

    public function index(Request $r)
    {
        // Set Filter
            if ( !empty($r->smt) ) {
                Session::set('ku_smt', $r->smt);
            }

            if ( !empty($r->jenis) ) {
                Session::set('ku_jenis', $r->jenis);
            }

            if ( !empty($r->status) ) {
                Session::set('ku_status', $r->status);
            }

            if ( !empty($r->prodi) ) {
                Session::set('ku_prodi', $r->prodi);
            }

            if ( !Session::has('ku_smt') ) {
                $this->setSession();
            }
        // End set

        $filter_prodi = '';
        
        if ( Session::get('ku_prodi') != 'all' ) {
            $filter_prodi = "AND m1.id_prodi = ".Session::get('ku_prodi');
        } else {
            $prodi_user = Sia::getProdiUser();
            $filter_prodi = "AND m1.id_prodi in (".implode(',', $prodi_user).")";
        }

        if ( Session::get('ku_status') == 'NON-AKTIF' ) {
            
            $query = DB::select("SELECT m1.id as id_mhs_reg, m1.nim, m2.nm_mhs, pr.jenjang, pr.nm_prodi
                        FROM krs_status as ks
                        left join mahasiswa_reg as m1 on ks.id_mhs_reg = m1.id
                        left join mahasiswa as m2 on m1.id_mhs = m2.id
                        left join prodi as pr on m1.id_prodi = pr.id_prodi
                        where ks.id_smt = ".Session::get('ku_smt')."
                        and ks.id_mhs_reg not in 
                            (SELECT id_mhs_reg from kartu_ujian where jenis='".Session::get('ku_jenis')."' and id_smt=".Session::get('ku_smt')." and id_mhs_reg = ks.id_mhs_reg)
                        $filter_prodi
                        group by ks.id_mhs_reg
                ");
            $data['mahasiswa'] = $query;

        } else {

            $query = DB::table('kartu_ujian as ku')
                        ->leftJoin('mahasiswa_reg as m1', 'ku.id_mhs_reg', 'm1.id')
                        ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                        ->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
                        ->select('m1.id as id_mhs_reg','m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi')
                        ->where('id_smt', Session::get('ku_smt'))
                        ->where('jenis', Session::get('ku_jenis'));
            if ( Session::get('ku_prodi') != 'all' ) {
                $query->where('m1.id_prodi', Session::get('ku_prodi'));
            }

            $data['mahasiswa'] = $query->get();
        }


        $data['semester'] = Sia::listSemester();

        return view('kartu-ujian.index', $data);
    }

    private function setSession()
    {
        Session::set('ku_smt', Sia::sessionPeriode());
        Session::set('ku_status', 'NON-AKTIF');
        Session::set('ku_prodi', 'all');
        Session::set('ku_jenis', 'UTS');
    }

    public function update(Request $r)
    {
        if ( $r->status == 'A' ) {

            $data = [
                'id_mhs_reg' => $r->id_mhs_reg,
                'id_smt' => Session::get('ku_smt'),
                'jenis' => Session::get('ku_jenis')
            ];

            DB::table('kartu_ujian')->insert($data);

        } else {

            DB::table('kartu_ujian')
                ->where('id_mhs_reg', $r->id_mhs_reg)
                ->where('id_smt', Session::get('ku_smt'))
                ->where('jenis', Session::get('ku_jenis'))
                ->delete();
        }
    }

    public function statusKartu(Request $r)
    {

        if ( !empty($r->smt) ) {
            Session::set('jdu_semester', $r->smt);
        }

        if ( !empty($r->jns) ) {
            Session::set('jdu_jenis_ujian', $r->jns);
        }

        if ( !Session::get('jdu_semester') ) {
            $this->setSessionFilterKu();
        }

        $query = Sia::jadwalUjian()
                        ->where('jdk.id_smt', Session::get('jdu_semester'))
                        ->where('jdu.jenis_ujian', Session::get('jdu_jenis_ujian'));

        if ( !empty($r->cari) ) {
            $query->where(function($q)use($r){
                $q->where('mk.kode_mk', 'like', '%'.$r->cari.'%')
                    ->orWhere('mk.nm_mk', 'like', '%'.$r->cari.'%')
                    ->orWhere('p.nama', 'like', '%'.$r->cari.'%')
                    ->orWhere('r.nm_ruangan', 'like', '%'.$r->cari.'%');
            });
        }

        $data['jadwal'] = $query->paginate(10);
        return view('kartu-ujian.status', $data);

    }

    private function setSessionFilterKu()
    {
        Session::set('jdu_semester', Sia::sessionPeriode());
        Session::set('jdu_jenis_ujian', 'UTS');
    }

    public function statusKartudetail($id)
    {
        $jdu = DB::table('jadwal_ujian as jdu')
                ->leftJoin('pengawas as p', 'jdu.id_pengawas', 'p.id')
                ->leftJoin('ruangan as r', 'jdu.id_ruangan', 'r.id')
                ->select('jdu.*', 'p.nama', 'r.nm_ruangan')
                ->where('jdu.id', $id)->first();

        $peserta_ujian = Sia::pesertaUjian($id); ?>

        <div class="table-responsive">
            <table border="0" class="table-form" width="100%">
                <tr>
                    <td width="150">HARI</td>
                    <td> : <?= Rmt::hari($jdu->hari) ?></td>
                    <td>RUANGAN</td>
                    <td> : <?= $jdu->nm_ruangan ?></td>
                </tr>
                <tr>
                    <td>TANGGAL UJIAN</td>
                    <td> : <?= Carbon::parse($jdu->tgl_ujian)->format('d-m-Y') ?></td>
                    <td>JUMLAH PESERTA</td>
                    <td> : <?= $jdu->jml_peserta ?></td>
                </tr>
                <tr>
                    <td>JAM MASUK</td>
                    <td> : <?= substr($jdu->jam_masuk,0,5) ?> s/d <?= substr($jdu->jam_selesai,0,5) ?></td>
                    <td width="150px">PENGAWAS</td>
                    <td> : <?= $jdu->nama ?></td>
                </tr>
            </table>
            
            <hr>

            <p><b>Peserta Ujian</b></p>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                <thead class="custom">
                    <tr>
                        <th width="10">No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Kartu</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $no = 1 ?>
                    <?php foreach( $peserta_ujian as $r ) {
                        $kartu = DB::table('kartu_ujian')
                                ->where('id_smt', Session::get('jdu_semester'))
                                ->where('jenis', Session::get('jdu_jenis_ujian'))
                                ->where('id_mhs_reg', $r->id)
                                ->count() ?>
                        <tr>
                            <td><?= $no++ ?></td>   
                            <td><?= $r->nim ?></td> 
                            <td><?= $r->nm_mhs ?></td>
                            <td align="center">
                                <?= $kartu > 0 ? '<i style="color:green" class="fa fa-check"></i>':'<i style="color:red" class="fa fa-times"></i>' ?>        
                            </td>
                        </tr>
                    <?php } ?>  
                </tbody>
            </table>
        </div>
        <?php
    }
}