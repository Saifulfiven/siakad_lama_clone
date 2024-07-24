<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Rmt, Sia, Session, Response;
use App\Pkm, App\Dosen;

class PkmController extends Controller
{
    public function index(Request $r)
    {
        if ( !Session::has('pkm') ) {
            Session::put('pkm', []);
        }

        $pkm = DB::table('pkm as p')
                ->join('pkm_peserta as ps', 'p.id', 'ps.id_pkm')
                ->where('ps.id_mhs_reg', Sia::sessionMhs())
                ->select('p.*', 
                    DB::raw('(SELECT m2.nm_mhs from pkm_peserta as ps2
                        JOIN mahasiswa_reg as m1 on ps2.id_mhs_reg = m1.id
                        JOIN mahasiswa as m2 on m1.id_mhs = m2.id 
                        where ps2.id_pkm = p.id and ps2.jabatan=\'ketua\') as ketua'),
                    DB::raw('(SELECT d.nm_dosen from pkm_pembimbing as pb
                        JOIN dosen as d on pb.id_dosen = d.id
                        where pb.id_pkm = p.id
                        and pb.pembimbing_ke=1) as pembimbing')
                );

        $this->filter($pkm);

        $data['pkm'] = $pkm->paginate(10);
    	return view('mahasiswa-member.pkm.index', $data);
    }

    public function filter($query)
    {
        if ( Session::has('pkm.cari') ) {
            $query->where('p.judul', 'like', '%'.Session::get('pkm.cari').'%');
        }

        if ( Session::has('pkm.smt') ) {
            $query->where('p.id_smt', Session::get('pkm.smt'));
        }
    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('pkm.cari',$r->cari);
        } else {
            Session::pull('pkm.cari');
        }

        return redirect(route('m_pkm'));
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('pkm.'.$r->modul);
            } else {
                Session::put('pkm.'.$r->modul,$r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('pkm');
        }

        return redirect(route('m_pkm'));
    }

    public function detail($id)
    {
        $pkm = Pkm::findOrFail($id);
        $peserta = DB::table('pkm_peserta as ps')
                    ->join('mahasiswa_reg as m1', 'm1.id', 'ps.id_mhs_reg')
                    ->join('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m1.nim', 'm2.nm_mhs','ps.*')
                    ->where('id_pkm', $id)->get();
        $pembimbing = DB::table('pkm_pembimbing as pb')
                    ->join('dosen as d', 'd.id', 'pb.id_dosen')
                    ->where('id_pkm', $id)->get(); ?>
        <div class="col-md-12">
            <b><?= $pkm->judul ?></b>
            <hr>
        </div>

        <div class="col-md-12">
            <div class="table-responsive">
                <b>Anggota:</b><br>
                <table class="table table-bordered" style="min-width: 450px;width: 450px">
                    <tr>
                        <th width="40">No</th>
                        <th>Mahasiswa</th>
                        <th>Jabatan</th>
                    </tr>
                    <?php $no = 1 ?>
                    <?php foreach( $peserta as $val ) { ?>
                        <tr>
                            <td align="center"><?= $no++ ?></td>
                            <td><?= $val->nim ?> - <?= $val->nm_mhs ?></td>
                            <td align="center"><?= $val->jabatan ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

        </div>

        <div class="col-md-12">
        <hr>
            <div class="table-responsive">
                <b>Pembimbing:</b><br>
                <table class="table table-bordered" style="min-width: 400px;width: 400px">
                    <tr>
                        <th width="40">No</th>
                        <th>Dosen</th>
                    </tr>
                    <?php $no = 1 ?>
                    <?php foreach( $pembimbing as $val ) { ?>
                        <tr>
                            <td align="center"><?= $no++ ?></td>
                            <td><?= $val->nm_dosen ?></td>
                        </tr>
                    <?php } ?>

                    <?php if ( empty($pembimbing) ) { ?>
                        <tr><td colspan="3">Belum ada data</td></tr>
                    <?php } ?>
                </table>
            </div>

        </div>
    <?php
    }

    public function daftar(Request $r)
    {
        if ( !Session::has('anggota') ) {
            $anggota = [
                ['id' => Sia::sessionMhs(), 'mhs' => Sia::sessionMhs('nim').' - '.Sia::sessionMhs('nama'), 'jabatan' => 'ketua']
            ];

            Session::put('anggota', $anggota);
        }

        if ( !Session::has('pembimbing') ) {
            $data = [];
            Session::put('pembimbing', $data);
        }

        return view('mahasiswa-member.pkm.daftar');
    }

    public function getDosen(Request $r)
    {
            $param = $r->input('query');
            if ( !empty($param) ) {
                $dosen = Dosen::where('aktif',1)
                                ->where(function($q)use($param){
                                    $q->where('nm_dosen','like','%'.$param.'%')
                                    ->orWhere('nidn','like','%'.$param.'%');
                                })->orderBy('nm_dosen','asc')->get();
            } else {
                $dosen = Dosen::where('aktif',1)->orderBy('nm_dosen','asc')->get();
            }
            $data = [];
            foreach( $dosen as $r ) {
                $data[] = ['data' => $r->id, 'value' => Sia::namaDosen($r->gelar_depan,$r->nm_dosen,$r->gelar_belakang)];
            }
            $response = ['query' => 'Unit', 'suggestions' => $data];
            return Response::json($response,200);
    }

    public function getMahasiswa(Request $r)
    {
        $prodi = Sia::sessionMhs('prodi');

        $param = $r->input('query');
        if ( !empty($param) ) {
            $mahasiswa = Sia::mahasiswa()
                            ->where('m2.id_jenis_keluar', '0')
                            ->where('m2.id_prodi', $prodi)
                            ->where(function($q)use($param){
                                $q->where('m2.nim', 'like', '%'.$param.'%')
                                    ->orWhere('m1.nm_mhs', 'like', '%'.$param.'%');
                            })->select('m2.id','m2.nim','m1.nm_mhs')->take(10)->get();
        } else {
            $mahasiswa = Sia::mahasiswa()
                            ->where('m2.id_prodi', $prodi)
                            ->where('m2.id_jenis_keluar', '0')
                            ->select('m2.id','m2.nim','m1.nm_mhs')->take(10)->get();
        }

        $data = [];
        foreach( $mahasiswa as $r ) {
            $data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function anggotaStore(Request $r)
    {
        $this->validate($r, [
            'mahasiswa' => 'required'
        ]);

        if ( $r->id ) {

            $data = [
                'id_pkm' => $r->id,
                'id_mhs_reg' => $r->mahasiswa,
                'jabatan' => 'anggota'
            ];

            DB::table('pkm_peserta')->insert($data);

        } else {

            $mhs = Sia::mahasiswa()->where('m2.id', $r->mahasiswa)
                ->select('m2.id', 'm2.nim', 'm1.nm_mhs')
                ->first();

            $anggota = ['id' => $mhs->id, 'mhs' => $mhs->nim.' - '.$mhs->nm_mhs, 'jabatan' => 'anggota'];

            $val_before = Session::get('anggota');

            Session::push('anggota', $anggota);

            $val_after = Session::get('anggota');

            foreach ($val_after as $current_key => $current_array) {
                foreach ($val_after as $search_key => $search_array) {
                    if ($search_array['id'] == $current_array['id']) {
                        if ($search_key != $current_key) {
                            // Duplicate
                            Session::pull('anggota');
                            Session::put('anggota', $val_before);
                            Rmt::error('Mahasiswa ini sudah ada');
                            return redirect()->back();
                        }
                    }
                }

            }
        }


        return redirect()->back();
    }

    public function anggotaDelete(Request $r)
    {
        if ( $r->id ) {
            
            DB::table('pkm_peserta')
                ->where('id', $r->id)->delete();

        } else {

            $anggota_session = Session::get('anggota');
            unset($anggota_session[$r->index]);
            Session::put('anggota', $anggota_session);
        }
        return redirect()->back();
    }

    public function dosenStore(Request $r)
    {
        $this->validate($r, [
            'dosen' => 'required'
        ]);

        if ( $r->id ) {

            $data = [
                'id_pkm' => $r->id,
                'id_dosen' => $r->dosen,
                'pembimbing_ke' => 1
            ];

            DB::table('pkm_pembimbing')->insert($data);

        } else {

            $dsn = DB::table('dosen')->where('id', $r->dosen)
                    ->first();

            $dosen = ['id' => $dsn->id, 'dosen' => $dsn->nm_dosen];
            
            $val_before = Session::get('pembimbing');

            Session::push('pembimbing', $dosen);

            $val_after = Session::get('pembimbing');

            foreach ($val_after as $current_key => $current_array) {
                foreach ($val_after as $search_key => $search_array) {
                    if ($search_array['id'] == $current_array['id']) {
                        if ($search_key != $current_key) {
                            // Duplicate
                            Session::pull('pembimbing');
                            Session::put('pembimbing', $val_before);
                            Rmt::error('Dosen ini sudah ada');
                            return redirect()->back();
                        }
                    }
                }

            }
        }

        return redirect()->back();
    }

    public function dosenDelete(Request $r)
    {
        if ( $r->id ) {
            
            DB::table('pkm_pembimbing')
                ->where('id', $r->id)->delete();

        } else {
            $dosen = Session::get('pembimbing');
            unset($dosen[$r->index]);
            Session::put('pembimbing', $dosen);
        }
        return redirect()->back();
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required'
        ]);

        if ( empty(Session::get('pembimbing')) ) {
            return Response::json(['Silahkan masukkan pembimbing'], 422);
        }

        if ( count(Session::get('anggota')) <= 2 ) {
            return Response::json(['Setidaknya masukkan 2 anggota'], 422);
        }

        DB::transaction(function($query)use($r){
            $data = new Pkm;
            $data->judul = $r->judul;
            $data->id_smt = Sia::sessionPeriode();
            $data->save();

            $id_pkm = $data->id;

            foreach( Session::get('anggota') as $val ) {
                $data = [
                    'id_pkm' => $id_pkm,
                    'id_mhs_reg' => $val['id'],
                    'jabatan' => $val['jabatan']
                ];

                DB::table('pkm_peserta')->insert($data);
            }

            foreach( Session::get('pembimbing') as $key => $val ) {
                $data = [
                    'id_pkm' => $id_pkm,
                    'id_dosen' => $val['id'],
                    'pembimbing_ke' => $key + 1
                ];

                DB::table('pkm_pembimbing')->insert($data);
            }
        });

        Session::pull('pembimbing');
        Session::pull('anggota');

        Rmt::success('Berhasil menyimpan data');

        return Response::json(['ok']);
    }

    public function edit(Request $r)
    {

        $pkm = Pkm::findOrFail($r->id);
        $data['peserta'] = DB::table('pkm_peserta as ps')
                    ->join('mahasiswa_reg as m1', 'm1.id', 'ps.id_mhs_reg')
                    ->join('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m1.id as id_mhs_reg','m1.nim', 'm2.nm_mhs','ps.*')
                    ->where('id_pkm', $r->id)->get();

        $data['pembimbing'] = DB::table('pkm_pembimbing as pb')
                    ->join('dosen as d', 'd.id', 'pb.id_dosen')
                    ->select('pb.*', 'd.nm_dosen')
                    ->where('id_pkm', $r->id)->get();

        $data['pkm'] = $pkm;

        return view('mahasiswa-member.pkm.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required'
        ]);

        $data = Pkm::findOrFail($r->id);
        $data->judul = $r->judul;
        $data->save();

        Rmt::success('Selesai mengubah data');

        return Response::json(['Berhasil menyimpan data']);
    }
}