<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Rmt, Carbon;

trait DosenMengajar
{

    public function dosenMengajar(Request $r)
    {
        $ta = empty($r->ta) ? \Sia::sessionPeriode() : $r->ta;
        $jenis = empty($r->jenis) ? 1 : $r->jenis;

        $prodi = \Sia::getProdiUser();

        $query = DB::table('dosen_mengajar as dm')
                    ->join('dosen as d', 'dm.id_dosen', 'd.id')
                    ->join('jadwal_kuliah as jdk', 'dm.id_jdk', 'jdk.id')
                    ->select('d.id','d.nm_dosen','d.gelar_depan','d.gelar_belakang','d.hp','d.alamat')
                    ->where('jdk.id_smt', $ta)
                    ->where('jdk.jenis', $jenis)
                    ->orderBy('d.nm_dosen')
                    ->groupBy('dm.id_dosen');

        // if ( \Sia::admin() ) {
        //     $query->whereIn('jdk.id_prodi', $prodi);
        // } else {
            $query->whereIn('jdk.id_prodi', $prodi);
        // }

        if ( !empty($r->cari) ) {
            $query->where(function($q)use($r){
                    $q->where('d.nm_dosen', 'like', '%'.$r->cari.'%')
                        ->orWhere('d.nidn', 'like', '%'.$r->cari.'%')
                        ->orWhere('d.jenis_dosen', 'like', '%'.$r->cari.'%');
                });
        }

        if ( !empty($r->jenkel) ) {
            $query->where('d.jenkel', $r->jenkel);
        }
        
        $data['dosen'] = $query->paginate(20);

        $data['prodi'] = $prodi[0];

	    return view('dosen.dosen-mengajar', $data);
    }

    public function dosenMengajarSk(Request $r, $id = NULL)
    {

        if ( empty($r->all) ) {

            $data['dsn'] = \App\Dosen::find($id);

            $data['jadwal'] = DB::table('jadwal_kuliah as jdk')
                        ->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
                        ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                        ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                        ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                        ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                        ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                        ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
                        ->select('jdk.*','mk.nm_mk','mk.sks_mk',
                                'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
                                'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl')
                        ->where('dm.id_dosen', $id)
                        ->whereIn('jdk.id_prodi', \Sia::getProdiUser())
                        ->where('jdk.id_smt', $r->ta)
                        ->where('jdk.jenis', $r->jenis)
                        ->orderBy('mkur.smt')
                        ->get();

        } else {

            $data['dosen'] = DB::table('dosen_mengajar as dm')
                    ->join('dosen as d', 'dm.id_dosen', 'd.id')
                    ->join('jadwal_kuliah as jdk', 'dm.id_jdk', 'jdk.id')
                    ->select('d.id','d.nm_dosen','d.gelar_depan','d.gelar_belakang','d.hp','d.alamat')
                    ->where('jdk.id_smt', $r->ta)
                    ->where('jdk.jenis', $r->jenis)
                    ->where('jdk.id_prodi', $r->prodi)
                    ->orderBy('d.nm_dosen')
                    ->groupBy('dm.id_dosen')
                    ->get();
        }

        return view('dosen.sk-mengajar', $data); 
    }

    public function absensiDosen(Request $r)
    {
        return view('dosen.print-absensi-dosen');
    }

    public function dosenMengajarCetak(Request $r)
    {
        $ta = empty($r->ta) ? \Sia::sessionPeriode() : $r->ta;
        $jenis = empty($r->jenis) ? 1 : $r->jenis;
        $prodi = \Sia::getProdiUser();

        $query = DB::table('dosen_mengajar as dm')
                    ->join('dosen as d', 'dm.id_dosen', 'd.id')
                    ->join('jadwal_kuliah as jdk', 'dm.id_jdk', 'jdk.id')
                    ->select('d.id','d.nm_dosen','d.gelar_depan','d.gelar_belakang','d.hp','d.alamat')
                    ->where('jdk.id_smt', $ta)
                    ->where('jdk.jenis', $jenis)
                    ->whereIn('jdk.id_prodi', $prodi)
                    ->orderBy('d.nm_dosen')
                    ->groupBy('dm.id_dosen');
        
        $data['dosen'] = $query->get();

        $data['ta'] = DB::table('semester')
                        ->where('id_smt', $ta)
                        ->first();

        return view('dosen.print-dosen-mengajar', $data);
    }

    public function dosenMengajarSkMk(Request $r)
    {
        $jadwal = DB::table('jadwal_kuliah as jdk')
                    ->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
                    ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                    ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                    ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                    ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                    ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                    ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
                    ->select('jdk.*','mk.nm_mk','mk.sks_mk',
                            'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
                            'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl')
                    ->where('dm.id_dosen', $r->id_dosen)
                    ->where('pr.jenjang', 's2')
                    ->where('jdk.id_smt', $r->ta)
                    ->where('jdk.jenis', $r->jenis)
                    ->orderBy('mkur.smt')
                    ->get(); ?>

        <input type="hidden" name="id_dosen" value="<?= $r->id_dosen ?>">
        <input type="hidden" name="ta" value="<?= $r->ta ?>">
        <input type="hidden" name="jenis" value="<?= $r->jenis ?>">
        <div class="form-group">
            <label class="control-label">Tanggal SK/Cetak</label>
            <div>
                <input type="date" value="" name="tgl_cetak" class="form-control" required="">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">Nomor Surat</label>
            <div>
                <input type="text" value="" name="nomor_surat" class="form-control" required="">
            </div>
        </div>
        <p><b>Pilih matakuliah yang akan dimasukkan dalam SK</b></p>
        <table class="table table-bordered table-hover">
            <tr>
                <th></th>
                <th>Mata kuliah</th>
                <th>Jam</th>
                <th>Kelas</th>
                <th>Semester</th>
            </tr>

        <?php $no = 1 ?>
        <?php foreach( $jadwal as $j ) {
            $no++;
            $jam_masuk = substr($j->jam_masuk,0,5);
            $jam_keluar = substr($j->jam_keluar,0,5); ?>
            <tr>
                <td align="center"><input type="checkbox" name="mk[]" id="mk-<?= $no ?>" value="<?= $j->id ?>"></td>
                <td><?= trim(ucwords(strtolower($j->nm_mk))) ?></td>
                <td align="center"><?= $jam_masuk ?> - <?= $jam_keluar ?></td>
                <td align="center"><?= $j->kode_kls ?></td>
                <td align="center"><?= $j->smt ?></td>
            </tr>
        <?php } ?>

        </table>
        <hr>
        <center>
            <button type="submit" class="btn btn-primary btn-sm">CETAK SK</button>
        </center>
        <?php 
    }

    public function dosenMengajarSk2(Request $r)
    {
        $data['dsn'] = \App\Dosen::find($r->id_dosen);

        $data['jadwal'] = DB::table('jadwal_kuliah as jdk')
                    ->leftJoin('dosen_mengajar as dm', 'jdk.id', 'dm.id_jdk')
                    ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                    ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                    ->leftJoin('prodi as pr', 'jdk.id_prodi','=', 'pr.id_prodi')
                    ->leftJoin('ruangan as r', 'jdk.ruangan','=','r.id')
                    ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                    ->leftJoin('semester as smt','jdk.id_smt','=','smt.id_smt')
                    ->select('jdk.*','mk.nm_mk','mk.sks_mk',
                            'pr.jenjang','pr.nm_prodi','r.nm_ruangan','jk.jam_masuk',
                            'jk.jam_keluar','smt.nm_smt','mkur.smt','jdk.tgl')
                    ->whereIn('jdk.id', $r->mk)
                    ->where('dm.id_dosen', $r->id_dosen)
                    ->orderBy('mkur.smt')
                    ->get();

        $data['jenis'] = $r->jenis;
        $data['ta'] = $r->ta;
        $data['tgl_cetak'] = Carbon::parse($r->tgl_cetak)->format('Y-m-d');
        $data['nomor_surat'] = $r->nomor_surat;
        

        return view('dosen.sk-mengajar-s2', $data); 
    }
}