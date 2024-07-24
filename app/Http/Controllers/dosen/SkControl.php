<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;

use DB, Sia, Rmt, Response, Session;

trait SkControl
{
    public function skMengajar(Request $r)
    {
        $id = Sia::sessionDsn();

        $data['dsn'] = \App\Dosen::find($id);

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
                    ->where('dm.id_dosen', $id)
                    ->where('jdk.id_smt', Session::get('jdm.ta'));

        if ( Session::has('jdm.jenis') ) {
            $jadwal->where('jdk.jenis', Session::get('jdk.jenis'));
        } else {
            $jadwal->where('jdk.jenis', 1);
        }

        $data['jadwal'] = $jadwal->orderBy('mkur.smt')->get();


        return view('dsn.jadwal.sk-mengajar', $data); 
    }

    public function skBimbinganData(Request $r)
    {
        $id = Sia::sessionDsn();
        $id_smt = Session::get('bim.smt');

        $bimbingan = DB::table('penguji as p')
                        ->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
                        ->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
                        ->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                        ->select('p.id_mhs_reg', 'm1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi')
                        ->where('p.id_dosen', $id)
                        ->where('p.id_smt', $id_smt)
                        ->whereIn('p.jabatan', ['KETUA', 'SEKRETARIS'])
                        ->groupBy('p.id_mhs_reg')
                        ->get();

        ?>

        <div class="form-group">
            <label class="control-label">Tanggal SK/Cetak</label>
            <div>
                <input type="date" value="" name="tgl" class="form-control" required="">
            </div>
        </div>
        <p><b>Pilih mahasiswa yang akan dimasukkan dalam SK</b></p>
        <table class="table table-bordered table-hover">
            <tr>
                <th></th>
                <th>Mahasiswa</th>
                <th>Prodi</th>
            </tr>

        <?php $no = 1 ?>
        <?php foreach( $bimbingan as $j ) {
            $no++; ?>
            <tr>
                <td align="center">
                    <input type="checkbox" name="mhs[]" id="mhs-<?= $no ?>" value="<?= $j->id_mhs_reg ?>">
                </td>
                <td><label for="mhs-<?= $no ?>"><?= $j->nim .' - '. $j->nm_mhs ?></label></td>
                <td align="center"><?= $j->nm_prodi .' '. $j->jenjang ?></td>
            </tr>
        <?php } ?>

        </table>
        <hr>
        <center>
            <button type="submit" class="btn btn-primary btn-sm">CETAK SK</button>
        </center>
        <?php
    }

    public function skBimbingan(Request $r)
    {
        $id = Sia::sessionDsn();
        $id_smt = Session::get('bim.smt');

        $data['bimbingan'] = DB::table('penguji as p')
                        ->leftJoin('ujian_akhir as ua', function($join){
                            $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
                            $join->on('ua.id_smt', '=', 'p.id_smt');
                            $join->on('ua.jenis', '=', 'p.jenis');
                        })
                        ->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
                        ->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
                        ->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                        ->select('p.id_smt', 'p.id_mhs_reg', 'p.jenis','m1.nim','m2.nm_mhs','pr.jenjang','pr.id_prodi', 'pr.nm_prodi', 'ua.judul_tmp','p.jabatan')
                        ->where('p.id_dosen', $id)
                        ->where('p.id_smt', $id_smt)
                        ->whereIn('p.jabatan', ['KETUA', 'SEKRETARIS'])
                        ->whereIn('p.id_mhs_reg', $r->mhs)
                        ->groupBy('p.id_mhs_reg')
                        ->get();


        $view = 'sk-bimbingan-s2';

        foreach( $data['bimbingan'] as $val ) {
            
            if ( $val->id_prodi != '61101' ) {
                $view = 'sk-bimbingan';
                break;
            }

        }

        $data['dsn'] = \App\Dosen::find($id);

        return view('dsn.bimbingan.'.$view, $data);
    }

}