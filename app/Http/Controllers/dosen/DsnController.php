<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Sia, Rmt, Response, Session, Auth, Image;
use App\Aktivitas;
use App\Dosen;
use App\InformasiModels\Gbpp;

class DsnController extends Controller
{
    use NilaiControl;
    use AbsenControl;
    use LmsController;
    use PersetujuanSeminar;
    use SkControl;

    public function jadwal(Request $r)
    {
        if ( empty(Session::get('jdm.ta')) ) {
            Session::put('jdm.ta', Sia::sessionPeriode());
        }

        $data['jadwal'] = Sia::jadwalMengajar()
                    ->where('dm.id_dosen', Sia::sessionDsn())
                    ->where('jdk.id_smt', Session::get('jdm.ta'))
                    // ->where('jdk.id_prodi','<>', 61101)
                    ->get();
                    
        $data['semester'] = $this->getSemester();

        return view('dsn.jadwal.index', $data);
    }

    public function getSemester()
    {
        $smt = DB::table('dosen_mengajar as dm')
                    ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'dm.id_jdk')
                    ->selectRaw('min('.Sia::prefix().'jdk.id_smt) as smt_1')
                    ->where('dm.id_dosen', Sia::sessionDsn())
                    ->first();

        if ( !empty($smt) ) {
        $data = DB::table('semester')
                    ->whereBetween('id_smt', [$smt->smt_1, Sia::sessionPeriode()])
                    ->orderBy('id_smt','desc')
                    ->get();
        } else {
            $data = [];
        }

        return $data;
    }

    public function jadwalFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('jdm.'.$r->modul);
            } else {
                Session::put('jdm.'.$r->modul,$r->val);
            }
        }
        
        return redirect(route('dsn_jadwal'));
    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('jdm.cari',$r->cari);
        } else {
            Session::pull('jdm.cari');
        }

        return redirect(route('dsn_jadwal'));
    }

    public function jadwalCetak(Request $r)
    {
        $data['ta'] = DB::table('semester')
                        ->select('nm_smt')
                        ->where('id_smt', Session::get('jdm.ta'))
                        ->first();

        $data['jadwal'] = Sia::jadwalMengajar()
                    ->where('dm.id_dosen', Sia::sessionDsn())
                    ->where('jdk.id_smt', Session::get('jdm.ta'))
                    ->get();

        $qr = 'Jadwal Mengajar : '.Sia::sessionDsn('nama').','.$data['ta']->nm_smt.' [STIE NOBEL INDONESIA]';
        
        \QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionDsn().'.svg');

        return view('dsn.jadwal.cetak', $data);
    }

    public function profil(Request $r)
    {
        $data['dsn'] = DB::table('dosen as d')
                        ->leftJoin('users as u', 'd.id_user', '=', 'u.id')
                        ->select('d.*','u.username','u.email')
                        ->where('d.id', Sia::sessionDsn())->first();

        return view('dsn.profil', $data);
    }

    public function updateFoto(Request $r)
    {
        $this->validate($r, [
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500',
        ]);

        try {
            $whitelist = array('jpg', 'jpeg', 'png', 'gif', 'svg');

            if ( $r->hasFile('foto') ) {
                $extension = $r->foto->getClientOriginalExtension();
                $imageName = time().substr(str_slug(Sia::sessionDsn('nama')),0,20) .'.'.$extension;
                
                if (!in_array($extension, $whitelist)) {
                    $error = 'Type file salah. Pastikan anda mengupload file '.implode(',', $whitelist);
                    return Response::json(['error'  => 1,'msg' => $error]);
                } else {
                    $path = config('app.foto-dosen');
                    $r->foto->move($path, $imageName);

                    $dsn = Dosen::find(Sia::SessionDsn());

                    if ( !empty($dsn->foto) ) {
                        Rmt::unlink(storage_path('foto-dosen').'/'.$dsn->foto);
                    }

                    $dsn->foto = $imageName;
                    $dsn->save();

                }

            }

        } catch (\Exception $e) {
            return Response::json([$e->getMessage], 422);
        }

    }

    public function ttd(Request $r)
    {
        try {
            
            $path = storage_path('ttd-dosen');
            $ttd = base64_decode($r->img_data);
            $file_name = time().'-'.Sia::sessionDsn().'.png';
            $fileName = $path.'/'.$file_name;

            file_put_contents($fileName, $ttd);

            $dsn = \App\Dosen::find(Sia::sessionDsn());

            if ( !empty($dsn->ttd) ) {
                if ( file_exists($path.'/'.$dsn->ttd) ) {
                    unlink($path.'/'.$dsn->ttd);
                }
            }

            $dsn->ttd = $file_name;
            $dsn->save();

            Rmt::success('Berhasil menyimpan tanda tangan');
            return Response::json(['file' => $file_name]);

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }

    }

    public function updateProfil(Request $r)
    {
        $this->validate($r, [
            'nama' => 'required',
            'nidn' => 'unique:dosen,id,'.$r->id,
            'username' => 'required'
        ]);

        try{
            DB::transaction(function()use($r){

                $user = \App\User::find(Auth::user()->id);
                $user->nama = trim($r->nama);
                $user->username = trim($r->username);
                $user->email = $r->email;
                if ( !empty($r->password) ) {
                    $user->password = bcrypt($r->password);
                }
                $user->save();
            

                $dsn = \App\Dosen::find(Sia::sessionDsn());
                $dsn->nm_dosen = trim($r->nama);
                $dsn->gelar_depan = trim($r->gelar_depan);
                $dsn->gelar_belakang = trim($r->gelar_belakang);
                $dsn->pendidikan_tertinggi = $r->pendidikan_tertinggi;
                $dsn->aktivitas = $r->aktivitas;
                $dsn->jabatan_fungsional = $r->jabatan_fungsional;
                $dsn->golongan = $r->golongan;
                $dsn->nip = $r->nip;
                $dsn->nidn = $r->nidn;
                $dsn->tempat_lahir = $r->tempat_lahir;
                $dsn->tgl_lahir = empty($r->tgl_lahir) ? NULL : Rmt::formatTgl($r->tgl_lahir,'Y-m-d');
                $dsn->jenkel = $r->jenis_kelamin;
                $dsn->id_agama = empty($r->agama) ? NULL : $r->agama;
                $dsn->alamat = $r->alamat;
                $dsn->hp = $r->hp;
                $user->email = $r->email;
                $dsn->save();
            });
        } catch( \Exception $e) {
            return Response::json(['error' => 1,'msg' => $e->getMessage()]);
        }

        Rmt::success('Berhasil menyimpan data');
        return Response::json(['error' => 0, 'msg' => 'sukses']);
    }

    public function rps(Request $r)
    {
        if ( empty($r->prodi) ) {
            return redirect()->route('dsn_rps',['prodi' => 61101]);
        }
        
        $data['rps'] = Gbpp::where('prodi', $r->prodi)->orderBy('judul','asc')->get();

        return view('dsn.rps.index', $data);
    }


    public function detailKuesioner(Request $r)
    {
        if ( !$r->ajax() ) {
            exit;    
        }

        if ( empty($r->id_jdk) ) {
            exit;
        }

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
        $total_nilai = 0; 


        if ( count($komponen) > 0 ) { ?>

            <br>

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
                                ->where('kh.approve_komen', 1)
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
    
        <?php } else {
            echo '<br>';
            echo '<b>Belum ada mahasiswa yang mengisi kuesioner</b>';
        } ?>
            
            <div>
                <br>
                <button type="button" data-dismiss="modal" class="btn btn-submit btn-sm pull-right">Keluar</button>
            </div>

        <?php
    }
}
