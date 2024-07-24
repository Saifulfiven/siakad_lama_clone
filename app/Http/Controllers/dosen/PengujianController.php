<?php

namespace App\Http\Controllers\dosen;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Rmt;
use Session;
use Sia;

class PengujianController extends Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = 'dsn.pengujian';
    }

    private function filterAwal()
    {
        if (!Session::has('pgj.smt')) {
            Session::put('pgj.smt', Sia::sessionPeriode());
        }
    }

    public function index()
    {
        $this->filterAwal();
        $id_smt = Session::get('pgj.smt');

        $penguji = Rmt::pengujianDosen($id_smt, Sia::sessionDsn());

        // if (Session::has('bim.cari')) {

        //     $cari = Session::get('bim.cari');
        //     $penguji->where(function ($q) use ($cari) {
        //         $q->where('m1.nim', 'LIKE', '%' . $cari . '%')
        //             ->orWhere('m2.nm_mhs', 'LIKE', '%' . $cari . '%');
        //     });
        // }

        $data['penguji'] = $penguji->paginate(20);

        // dd($data['penguji']);

        return view($this->view . '.index', $data);
    }

    public function skPengujianData()
    {
        $id = Sia::sessionDsn();
        $id_smt = Session::get('pgj.smt');

        $bimbingan = DB::table('penguji as p')
            ->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
            ->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
            ->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
            ->select('p.id_mhs_reg', 'm1.nim', 'm2.nm_mhs', 'pr.jenjang', 'pr.nm_prodi')
            ->where('p.id_dosen', $id)
            ->where('p.id_smt', $id_smt)
            ->whereIn('p.jabatan', ['ANGGOTA', 'ANGGOTA2'])
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

        <?php $no = 1?>
        <?php foreach ($bimbingan as $j) {
            $no++;?>
            <tr>
                <td align="center">
                    <input type="checkbox" name="mhs[]" id="mhs-<?=$no?>" value="<?=$j->id_mhs_reg?>">
                </td>
                <td><label for="mhs-<?=$no?>"><?=$j->nim . ' - ' . $j->nm_mhs?></label></td>
                <td align="center"><?=$j->nm_prodi . ' ' . $j->jenjang?></td>
            </tr>
        <?php }?>

        </table>
        <hr>
        <center>
            <button type="submit" class="btn btn-primary btn-sm">CETAK SK</button>
        </center>
        <?php
}

    public function skPengujian(Request $r)
    {
        $id = Sia::sessionDsn();
        $id_smt = Session::get('pgj.smt');

        $data['bimbingan'] = DB::table('penguji as p')
            ->leftJoin('ujian_akhir as ua', function ($join) {
                $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
                $join->on('ua.id_smt', '=', 'p.id_smt');
                $join->on('ua.jenis', '=', 'p.jenis');
            })
            ->join('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
            ->join('mahasiswa as m2', 'm2.id', 'm1.id_mhs')
            ->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
            ->select('p.id_smt', 'p.id_mhs_reg', 'p.jenis', 'm1.nim', 'm2.nm_mhs', 'pr.jenjang', 'pr.id_prodi', 'pr.nm_prodi', 'ua.judul_tmp', 'p.jabatan')
            ->where('p.id_dosen', $id)
            ->where('p.id_smt', $id_smt)
            ->whereIn('p.jabatan', ['ANGGOTA', 'ANGGOTA2'])
            ->whereIn('p.id_mhs_reg', $r->mhs)
            ->groupBy('p.id_mhs_reg')
            ->get();

        $view = 'sk-pengujian-s2';

        foreach ($data['bimbingan'] as $val) {

            if ($val->id_prodi != '61101') {
                $view = 'sk-pengujian';
                break;
            }

        }

        $data['dsn'] = \App\Dosen::find($id);

        // dd($data);

        return view('dsn.pengujian.' . $view, $data);
    }

    public function setFilter(Request $r)
    {
        if (!empty($r->modul)) {

            if ($r->val == 'all') {
                Session::pull('pgj.' . $r->modul);
            } else {
                Session::put('pgj.' . $r->modul, $r->val);
            }
        }

        if ($r->remove) {
            Session::pull('pgj');
        }

        if ($r->go) {
            return redirect()->back();
        }

        return redirect(route('dsn_pgj'));

    }
}
