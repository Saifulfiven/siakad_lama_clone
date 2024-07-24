<?php

namespace App\Http\Controllers;

use App\KrsStatus;
use App\Mahasiswa;
use App\Mahasiswareg;
use App\Nilai;
use App\User;
use App\NilaiMbkm;
use App\MatakuliahKurikulum;
use App\Matakuliah;
use Auth;
use Carbon;
use Config;
use DB;
use Excel;
use Feeder;
use Illuminate\Http\Request;
use Image;
use Mail;
use Ramsey\Uuid\Uuid;
use Response;
use Rmt;
use Session;
use Sia;
use Illuminate\Support\Facades\Log;
use PDF;
use Dompdf\Options;
class MahasiswaController extends Controller
{
    use MahasiswaDokumen;

    public function simpanNilai($idMahasiswa){
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
            ->select(
                'm.nm_mhs',
                'm2.nim',
                'm2.semester_mulai',
                'm.jenkel',
                'p.nm_prodi',
                'p.jenjang',
                'smt.nm_smt',
                'smt.id_smt',
                'k.nm_konsentrasi'
            )
            ->where('m2.id', Session::get('id_regpd_in_nilai'))
            ->first();
        // nilai, jadwal_kuliah,mk_kurikulum,matakuliah
        $data['krs'] = Sia::krsMhs(Session::get('id_regpd_in_nilai'), Session::get('smt_in_nilai'), Session::get('jeniskrs_in_nilai'))
            ->select('n.*', 'jdk.kode_kls', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk')
            ->get();

        // nilai, jadwal_kuliah, matakuliah
        $data['ipk'] = Sia::ipkKhs(Session::get('id_regpd_in_nilai'), $data['mhs']->semester_mulai, Session::get('smt_in_nilai'));

        $pdf = PDF::setOptions(['isHtml5ParserEnable' => true, 'isRemoteEnabled' => true])
        ->setPaper('a4','potrait')
        ->loadView('mahasiswa.simpan-khs',$data);
        // nama-nim-jurusan

        $namaFile = $data['mhs']->nm_mhs."-".$data['mhs']->nim."-".$data['mhs']->nm_prodi.".pdf";
        return $pdf->download($namaFile);
    }

    /* Index */
    public function index(Request $r)
    {
        $query = Sia::mahasiswa()
            ->select(
                'm1.id',
                'm1.id_user',
                'm1.nm_mhs',
                'm1.gelar_depan',
                'm1.gelar_belakang',
                'm1.jenkel',
                'a.nm_agama',
                'm1.tgl_lahir',
                'm2.id_prodi',
                'p.nm_prodi',
                'p.jenjang',
                'm2.jurnal_file',
                'm2.jurnal_approved',
                'm2.jurnal_published',
                'm2.bebas_pembayaran',
                'm2.id as id_mhs_reg',
                'm2.jam_kuliah',
                'm2.semester_mulai',
                'm2.nim',
                'jk.ket_keluar',
                'm2.id_jenis_keluar',
                'm2.bebas_pustaka',
                'm2.bebas_skripsi'
            )
            ->orderBy('m2.nim', 'desc');

        // Filter
        Sia::mahasiswaFilter($query);

        if ($r->jurnal == 'unapproved') {
            $query->where(function ($q) {
                $q->where('m2.jurnal_file', '<>', '')
                    ->whereNotNull('jurnal_file')
                    ->where('jurnal_file', '<>', '0');
            })->where('jurnal_approved', '0');
        }

        if ($r->jurnal == 'approved') {
            $query->where('jurnal_approved', '1');
        }

        $data['mahasiswa'] = $query->paginate(10);
        return view('mahasiswa.index', $data);
    }

    public function filter(Request $r)
    {
        if ($r->ajax()) {
            Sia::filter(trim($r->value), 'mhs_' . $r->modul);
        } else {
            Session::pull('mhs_ta');
            Session::pull('mhs_angkatan');
            Session::pull('mhs_prodi');
            Session::pull('mhs_status');
            Session::pull('mhs_jns_daftar');
            Session::pull('mhs_jenkel');
            Session::pull('mhs_agama');
            Session::pull('mhs_search');
            Session::pull('mhs_waktu_kuliah');
        }

        return redirect(route('mahasiswa'));
    }

    public function cari(Request $r)
    {
        if (!empty($r->q)) {
            Session::put('mhs_search', $r->q);
        } else {
            Session::pull('mhs_search');
        }

        return redirect(route('mahasiswa'));
    }

    public function eksporPrint(Request $r)
    {
        $query = Sia::mahasiswa()
            ->select('m1.id', 'm1.tempat_lahir', 'm1.nm_mhs', 'm1.jenkel', 'a.nm_agama', 'm1.tgl_lahir', 'p.nm_prodi', 'p.jenjang', 'm2.nim', 'm2.kode_kelas', 'jk.ket_keluar', 'm1.alamat')
            ->orderBy('m2.id_prodi')->orderBy('m2.nim');

        // Filter
        Sia::mahasiswaFilter($query);

        $data['mahasiswa'] = $query->get();

        return view('mahasiswa.print', $data);
    }

    public function eksporExcel(Request $r)
    {
        $query = Sia::mahasiswa()
            ->leftJoin('pekerjaan as pk_ibu', 'm1.id_pekerjaan_ibu', '=', 'pk_ibu.id_pekerjaan')
            ->leftJoin('info_nobel as info', 'm1.id_info_nobel', '=', 'info.id_info_nobel')
            ->leftJoin('pekerjaan as pk_ayah', 'm1.id_pekerjaan_ayah', '=', 'pk_ayah.id_pekerjaan')
            ->leftJoin('wilayah as w', 'm1.id_wil', '=', 'w.id_wil')
            ->select(
                'm1.nik',
                'm1.id',
                'm1.alamat',
                'm1.hp',
                'm1.hp_ayah',
                'm1.hp_ibu',
                'm1.tempat_lahir',
                'm1.nm_mhs',
                'm1.alamat_ortu',
                'm1.dusun',
                'm1.rt',
                'm1.rw',
                'w.nm_wil',
                'm1.id_wil',
                'm1.des_kel',
                'm1.des_kel',
                'm1.nm_sekolah',
                'm2.bebas_pembayaran',
                'm2.kode_kelas',
                'm1.jenkel',
                'a.nm_agama',
                'm1.tgl_lahir',
                'p.nm_prodi',
                'p.jenjang',
                'm2.id as id_mhs_reg',
                'm2.nim',
                'jk.ket_keluar',
                'm1.nm_ayah',
                'm1.nm_ibu',
                'pk_ibu.nm_pekerjaan as pkj_ibu',
                'm1.email',
                'm2.nm_pt_asal',
                'm2.nm_prodi_asal',
                'm2.created_at',
                'pk_ayah.nm_pekerjaan as pkj_ayah',
                'info.nm_info'
            )
            ->orderBy('m2.id_prodi')
            ->orderBy('m2.nim');

        // Filter
        Sia::mahasiswaFilter($query);

        $data['mahasiswa'] = $query->get();
        // return view('mahasiswa.excel', $data);
        try {
            Excel::create('Mahasiswa', function ($excel) use ($data) {

                $excel->sheet('New sheet', function ($sheet) use ($data) {

                    $sheet->loadView('mahasiswa.excel', $data);
                });
            })->download('xlsx');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function skKuliah(Request $r, $id)
    {
        $data['mhs'] = Sia::mahasiswa()
            ->leftJoin('prodi as pr', 'pr.id_prodi', 'm2.id_prodi')
            ->where('m2.id', $id)
            ->select('m1.nm_mhs', 'm1.tempat_lahir', 'm1.tgl_lahir', 'm2.nim', 'm2.id_prodi', 'pr.ketua_prodi', 'pr.nip_ketua_prodi', 'pr.nm_prodi', 'pr.jenjang')
            ->first();

        return view('mahasiswa.sk-kuliah', $data);
    }

    public function detail($id)
    {
        $data['mhs'] = DB::table('mahasiswa as m1')
            ->leftJoin('agama as a', 'm1.id_agama', '=', 'a.id_agama')
            ->leftJoin('jenis_tinggal as jt', 'm1.jenis_tinggal', '=', 'jt.id_jns_tinggal')
            ->leftJoin('alat_transpor as at', 'm1.alat_transpor', '=', 'at.id_alat_transpor')
            ->leftJoin('info_nobel as info', 'm1.id_info_nobel', '=', 'info.id_info_nobel')
            ->leftJoin('pendidikan as p_ibu', 'm1.id_pdk_ibu', '=', 'p_ibu.id_pdk')
            ->leftJoin('pendidikan as p_ayah', 'm1.id_pdk_ayah', '=', 'p_ayah.id_pdk')
            ->leftJoin('pendidikan as p_wali', 'm1.id_pdk_wali', '=', 'p_wali.id_pdk')
            ->leftJoin('pekerjaan as pk_ibu', 'm1.id_pekerjaan_ibu', '=', 'pk_ibu.id_pekerjaan')
            ->leftJoin('pekerjaan as pk_ayah', 'm1.id_pekerjaan_ayah', '=', 'pk_ayah.id_pekerjaan')
            ->leftJoin('pekerjaan as pk_wali', 'm1.id_pekerjaan_wali', '=', 'pk_wali.id_pekerjaan')
            ->leftJoin('penghasilan as ph_ibu', 'm1.id_penghasilan_ibu', '=', 'ph_ibu.id_penghasilan')
            ->leftJoin('penghasilan as ph_ayah', 'm1.id_penghasilan_ayah', '=', 'ph_ayah.id_penghasilan')
            ->leftJoin('penghasilan as ph_wali', 'm1.id_penghasilan_wali', '=', 'ph_wali.id_penghasilan')
            ->leftJoin('users as us', 'm1.id_user', 'us.id')
            ->select(
                'm1.*',
                'a.nm_agama',
                'jt.nm_jns_tinggal',
                'at.nm_alat_transpor',
                'info.nm_info',
                'p_ibu.nm_pdk as pdk_ibu',
                'p_ayah.nm_pdk as pdk_ayah',
                'p_wali.nm_pdk as pdk_wali',
                'pk_ibu.nm_pekerjaan as pkj_ibu',
                'pk_ayah.nm_pekerjaan as pkj_ayah',
                'pk_wali.nm_pekerjaan as pkj_wali',
                'ph_ibu.nm_penghasilan as phs_ibu',
                'ph_ayah.nm_penghasilan as phs_ayah',
                'ph_wali.nm_penghasilan as phs_wali',
                'us.id as id_user',
                'us.username'
            )
            ->where('m1.id', $id)
            ->first();
        $data['id_mahasiswa'] = $id;

        $nim = DB::table('mahasiswa_reg')
            ->where('id_mhs', $id)
            ->orderBy('semester_mulai', 'desc')->first();
        $data['nim'] = $nim->nim;

        return view('mahasiswa.detail', compact('data'));
    }

    public function updateAkun(Request $r)
    {

        $this->validate($r, [
            'username' => 'required|unique:users,id,' . $r->id,
            'password' => 'min:6',
        ]);

        try {
            DB::transaction(function () use ($r) {

                $data = User::find($r->id);

                if (!empty($data)) {

                    $data->username = $r->username;

                    if (!empty($r->password)) {
                        $data->password = bcrypt($r->password);
                    }

                    $data->save();
                } else {

                    if (empty($r->email)) {
                        $email = $r->nim . '@stienobel-indonesia.ac.id';
                    } else {
                        $email = $r->email;
                    }

                    if (empty($r->password)) {
                        Rmt::error('Pengguna ini belum mempunyai password, isikan pada kolom password');
                        return redirect()->back();
                    }

                    $id = Rmt::uuid();

                    $mhs = new User;
                    $mhs->id = $id;
                    $mhs->username = $r->username;
                    $mhs->nama = $r->nama;
                    $mhs->email = $email;
                    $mhs->level = 'mahasiswa';
                    $mhs->password = bcrypt($r->password);
                    $mhs->save();

                    DB::table('mahasiswa')->where('id', $r->id_mhs)
                        ->update(['id_user' => $id]);
                }
            });
        } catch (\Exception $e) {
            Rmt::error($e->getMessage());
            return redirect()->back()->withInput();
        }

        Rmt::success('Berhasil menyimpan data');
        return redirect()->back();
    }

    public function cariNim(Request $r)
    {
        if ($r->ajax()) {
            $data = Sia::mahasiswa()
                ->where('m2.nim', $r->nim)
                ->where('m2.id_jenis_keluar', '<>', 0)
                ->select('m1.id', 'm1.nm_mhs', 'p.nm_prodi', 'p.jenjang', 'm2.nim', 'jk.ket_keluar')
                ->first();

            if (!empty($data)) {?>
        <hr>
        <table class="table table-bordered table-striped table-hover">
          <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
          <tbody align="center">
            <tr>
              <td><?=$data->nim?></td>
              <td><?=$data->nm_mhs?></td>
              <td><?=$data->ket_keluar?></td>
              <td><a href="<?=route('mahasiswa_regpd', ['id' => $data->id])?>" class="btn btn-xs btn-primary">Tambah Pendidikan</a></td>
            </tr>
          </tbody>
        </table>

    <?php } else {
                echo '<p>Tidak menemukan mahasiswa atau mahasiswa belum lulus</p>';
            }
        }
    }

    /*end index */

    /* Begin Reg pd */
    public function regPd($id)
    {
        $data['jnsPendaftaran'] = DB::table('jenis_pendaftaran')->orderBy('id_jns_pendaftaran')->get();
        $data['jalurMasuk'] = DB::table('jalur_masuk')->get();

        if (Sia::admin()) {
            $data['prodi'] = DB::table('prodi')->orderBy('jenjang')->get();
        } else {
            $data['prodi'] = DB::table('prodi')->whereIn('id_prodi', Sia::getProdiUser())->orderBy('jenjang')->get();
        }
        $data['regpd'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('jenis_pendaftaran as jp', 'm2.jenis_daftar', '=', 'jp.id_jns_pendaftaran')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->leftJoin('prodi as pr', 'm2.id_prodi', '=', 'pr.id_prodi')
            ->leftJoin('konsentrasi as ko', 'm2.id_konsentrasi', '=', 'ko.id_konsentrasi')
            ->leftJoin('dosen as d', 'm2.dosen_pa', 'd.id')
            ->leftJoin('kurikulum as kur', 'm2.id_kurikulum', 'kur.id')
            ->select('m2.jam_kuliah', 'm2.kode_kelas', 'm2.semester_mulai', 'm2.id as id_regpd', 'm2.nim', 'm2.tgl_daftar', 'smt.nm_smt as periode', 'jp.nm_jns_pendaftaran', 'pr.nm_prodi', 'pr.jenjang', 'ko.nm_konsentrasi', 'd.nm_dosen as pa', 'kur.mulai_berlaku')
            ->where('m2.id_mhs', $id)
            ->get();
        $data['mhs'] = DB::table('mahasiswa as m')
            ->leftJoin('agama as a', 'm.id_agama', '=', 'a.id_agama')
            ->select('m.nm_mhs', 'm.tempat_lahir', 'm.tgl_lahir', 'm.jenkel', 'a.nm_agama')
            ->where('m.id', $id)->first();
        $data['id_mahasiswa'] = $id;
        // dd($data);
        return view('mahasiswa.regpd', $data);
    }

    public function storeRegPd(Request $r)
    {
        $this->validate($r, [
            'tgl_masuk' => 'required|date',
            'jns_pendaftaran' => 'required',
            'prodi' => 'required',
            'waktu_kuliah' => 'required',
            'dosen_pa' => 'required',
            'kode_kelas' => 'required',
            'kurikulum' => 'required',
            'biaya_masuk' => 'required',
        ]);

        $can_aksi = Sia::canAction(Sia::sessionPeriode('berjalan'));

        if (!$can_aksi) {
            $response = ['error' => 1, 'msg' => 'Anda tidak bisa menambah mahasiswa pada periode ini.'];
            return Response::json($response);
        }

        if (in_array($r->jns_pendaftaran, [2, 11]) && empty($r->id_perguruan_tinggi)) {
            $response = ['error' => 1, 'msg' => 'Perguruan tinggi asal harus diisi.'];
            return Response::json($response);
        }

        if (in_array($r->jns_pendaftaran, [2, 11]) && empty($r->id_prodi_asal)) {
            $response = ['error' => 1, 'msg' => 'Program studi asal harus diisi.'];
            return Response::json($response);
        }

        // reg pd hanya boleh ditambah apabila mahasiswa sdh keluar/lulus
        $rule = Mahasiswareg::where('id_jenis_keluar', '=', 0)->where('id_mhs', $r->id)->count();
        if ($rule > 0) {
            $response = ['error' => 1, 'msg' => 'Histori pendidikan tidak bisa ditambah karena mahasiswa ini mempunyai histori pendidikan yang masih aktif'];
            return Response::json($response, 200);
        }

        $data = [
            'id_mhsreg' => Rmt::uuid(),
            'id_mhs' => $r->id,
            'nim' => Sia::generateNim($r->prodi),
        ];

        // validasi tgl daftar
        $tahun_daftar = Carbon::parse($r->tgl_masuk)->format('Y');
        $tahun_now = date('Y');

        if ($tahun_daftar != $tahun_now) {
            $response = ['error' => 1, 'msg' => 'Tahun pada tanggal daftar salah'];
            return Response::json($response, 200);
        }

        try {

            $id_mhs_reg = '';

            DB::transaction(function () use ($r, $data, &$id_mhs_reg) {
                $mhs2 = new Mahasiswareg;
                $mhs2->id = $data['id_mhsreg'];
                $mhs2->id_prodi = $r->prodi;
                $mhs2->id_konsentrasi = empty($r->konsentrasi) ? null : $r->konsentrasi;
                $mhs2->id_mhs = $data['id_mhs'];
                $mhs2->jenis_daftar = $r->jns_pendaftaran;
                $mhs2->jalur_masuk = empty($r->jalur_pendaftaran) ? null : $r->jalur_pendaftaran;
                $mhs2->nm_pt_asal = empty($r->asal_pt) ? null : $r->asal_pt;
                $mhs2->nm_prodi_asal = empty($r->asal_prodi) ? null : $r->asal_prodi;
                $mhs2->nim = $data['nim'];
                $mhs2->semester_mulai = Sia::sessionPeriode();
                $mhs2->tgl_daftar = Carbon::parse($r->tgl_masuk)->format('Y-m-d');
                $mhs2->jam_kuliah = $r->waktu_kuliah;
                $mhs2->dosen_pa = $r->dosen_pa;
                $mhs2->kode_kelas = $r->kode_kelas;
                $mhs2->id_kurikulum = $r->kurikulum;

                $mhs2->id_jenis_pembiayaan = 1;
                $mhs2->biaya_masuk = $r->biaya_masuk;

                if (in_array($r->jns_pendaftaran, [2, 11])) {
                    $mhs2->id_pt_asal = $r->id_perguruan_tinggi;
                    $mhs2->id_prodi_asal = $r->id_prodi_asal;
                }
                $mhs2->save();
                $id_mhs_reg = $mhs2->last_id;

                // $user = DB::table('users')->where('id_mhs', $data['id_mhs'])->first();
                // if ( $user ) {
                //     DB::table('users')->where('id_mhs', $data['id_mhs'])
                //         ->update(['username' => $data['nim']]);
                // }

            });
        } catch (\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
        }

        // $this->feederStoreRegpd($id_mhs_reg);

        Rmt::success('Berhasil menyimpan data');
        $response = ['error' => 0, 'msg' => 'sukses'];
        return Response::json($response, 200);
    }

    public function editRegPd($id)
    {
        $jnsPendaftaran = DB::table('jenis_pendaftaran')->orderBy('id_jns_pendaftaran')->get();

        $jalurMasuk = DB::table('jalur_masuk')->get();

        $mhs = DB::table('mahasiswa_reg as m')
            ->leftJoin('prodi as pr', 'm.id_prodi', '=', 'pr.id_prodi')
            ->leftJoin('semester as smt', 'm.semester_mulai', '=', 'smt.id_smt')
            ->leftJoin('dosen as d', 'd.id', 'm.dosen_pa')
            ->leftJoin('fdr_all_pt as pt', 'm.id_pt_asal', 'pt.id_perguruan_tinggi')
            ->leftJoin('fdr_all_prodi as fp', 'm.id_prodi_asal', 'fp.id_prodi')
            ->select('m.*', 'smt.nm_smt', 'pr.jenjang', 'pr.nm_prodi', 'pt.nama_perguruan_tinggi', 'pt.kode_perguruan_tinggi', 'fp.nama_prodi', 'd.id as id_pa', 'd.nm_dosen as pa')
            ->where('m.id', $id)->first();

        $kurikulum = DB::table('kurikulum')
            ->where('id_prodi', $mhs->id_prodi)->orderBy('mulai_berlaku', 'desc')->get();?>

    <form action="<?=route('mahasiswa_regpdupdate')?>" id="form-update-regpd" method="post">
      <?=csrf_field()?>
      <input type="hidden" name="id" value="<?=$mhs->id?>">
      <div class="table-responsive">
        <table border="0" class="table-hover table-form">
          <tr>
            <td width="160px">Periode</td>
            <td>
              <?=$mhs->nm_smt?>
            </td>
          </tr>
          <tr>
            <td width="160px">Tgl masuk <span>*</span></td>
            <td>
              <input type="date" class="form-control mw-2" name="tgl_masuk" value="<?=Rmt::formatTgl($mhs->tgl_daftar, 'Y-m-d')?>">
            </td>
          </tr>
          <tr>
            <td>Jenis pendaftaran <span>*</span></td>
            <td>
              <select class="form-control select-jenis-daftar" id="jns_pendaftaran" name="jns_pendaftaran">
                <option value="">-- Pilih jenis pendaftaran --</option>
                <?php foreach ($jnsPendaftaran as $jp) {?>
                  <option value="<?=$jp->id_jns_pendaftaran?>" <?=$jp->id_jns_pendaftaran == $mhs->jenis_daftar ? 'selected' : ''?>><?=$jp->nm_jns_pendaftaran?></option>
                <?php }?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Jalur pendaftaran</td>
            <td>
              <select class="form-control" name="jalur_pendaftaran">
                <option value="">-- Pilih jalur pendaftaran --</option>
                <option value="3" <?=$mhs->jalur_masuk == '3' ? 'selected' : ''?>>Penelusuran Minat dan Kemampuan (PMDK)</option>
                <option value="4" <?=$mhs->jalur_masuk == '4' ? 'selected' : ''?>>Prestasi</option>
                <option value="9" <?=$mhs->jalur_masuk == '9' ? 'selected' : ''?>>Program Internasional</option>
                <option value="11" <?=$mhs->jalur_masuk == '11' ? 'selected' : ''?>>Program Kerjasama Perusahaan/Institusi/Pemerintah</option>
                <option value="12" <?=$mhs->jalur_masuk == '12' ? 'selected' : ''?>>Seleksi Mandiri</option>
                <option value="13" <?=$mhs->jalur_masuk == '13' ? 'selected' : ''?>>Ujian Masuk Bersama Lainnya</option>
                <option value="14" <?=$mhs->jalur_masuk == '14' ? 'selected' : ''?>>Seleksi Nasional Berdasarkan Tes (SNBT)</option>
                <option value="15" <?=$mhs->jalur_masuk == '15' ? 'selected' : ''?>>Seleksi Nasional Berdasarkan Prestasi (SNBP)</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Program studi <span>*</span></td>
            <td>
              <?=$mhs->jenjang?> <?=$mhs->nm_prodi?>
            </td>
          </tr>
          <tr>
            <td>Konsentrasi</td>
            <td>
              <span class="konsentrasi">
                <select class="form-control" name="konsentrasi">
                  <option value="">-- Pilih konsentrasi --</option>
                  <?php foreach (Sia::listKonsentrasi($mhs->id_prodi) as $r) {?>
                    <option value="<?=$r->id_konsentrasi?>" <?=$r->id_konsentrasi == $mhs->id_konsentrasi ? 'selected' : ''?>><?=$r->nm_konsentrasi?></option>
                  <?php }?>
                </select>
              </span>
            </td>
          </tr>
          <tr>
            <td>Kurikulum <span>*</span></td>
            <td>
              <select name="kurikulum" class="form-control">
                <option value="">--Pilih Kurikulum--</option>
                <?php foreach ($kurikulum as $kur) {?>
                  <option value="<?=$kur->id?>" <?=$kur->id == $mhs->id_kurikulum ? 'selected' : ''?>><?=$kur->nm_kurikulum?></option>
                <?php }?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Waktu Kuliah <span>*</span></td>
            <td>
              <select class="form-control" name="waktu_kuliah">
                <option value="">-- Pilih waktu kuliah --</option>
                <?php foreach (Sia::waktuKuliah() as $val) {?>
                  <option value="<?=$val?>" <?=$val == $mhs->jam_kuliah ? 'selected' : ''?>><?=$val?></option>
                <?php }?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Dosen PA <span>*</span></td>
            <td>
              <div style="position: relative">
                <div class="input-icon right">
                  <span id="spinner-autocomplete-pa2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                  <input type="text" id="autocomplete-ajax-pa2" class="form-control" value="<?=$mhs->pa?>">
                </div>
                <input type="hidden" name="dosen_pa" id="dosen-pa2" value="<?=$mhs->id_pa?>">
              </div>
            </td>
          </tr>
          <tr>
            <td>Kode Kelas <span>*</span></td>
            <td>
              <div style="position: relative">
                <div class="input-icon right">
                  <input type="text" name="kode_kelas" value="<?=$mhs->kode_kelas?>" data-always-show="true" class="form-control mw-1" maxlength="5" required>
                </div>
              </div>
            </td>
          </tr>
          <tr class="pindahan" <?=$mhs->jenis_daftar == 1 ? 'style="display: none;"' : ''?>>
            <td>Asal perguruan tinggi</td>
            <td>
              <input type="text" name="asal_pt" value="<?=$mhs->kode_perguruan_tinggi?> - <?=$mhs->nama_perguruan_tinggi?>" class="form-control" disabled>
            </td>
          </tr>
          <tr class="pindahan" <?=$mhs->jenis_daftar == 1 ? 'style="display: none;"' : ''?>>
            <td>Prodi asal</td>
            <td>
              <input type="text" name="asal_prodi" value="<?=$mhs->nama_prodi?>" class="form-control" disabled>
            </td>
          </tr>
        </table>
      </div>
      <hr>
      <button type="submit" id="btn-update" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
    </form>

    <script type="text/javascript" src="<?=url('resources')?>/assets/js/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="<?=url('resources')?>/assets/js/jquery.mockjax.js"></script>

    <script>
      $(function() {
        $('#autocomplete-ajax-pa2').autocomplete({
          serviceUrl: '<?=route('jdk_dosen')?>',
          lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
            return re.test(suggestion.value);
          },
          onSearchStart: function(data) {
            $('#spinner-autocomplete-pa2').show();
          },
          onSearchComplete: function(data) {
            $('#spinner-autocomplete-pa2').hide();
          },
          onSelect: function(suggestion) {
            $('#dosen-pa2').val(suggestion.data);
          },
          onInvalidateSelection: function() {}
        });
      });
    </script>

    <?php
}

    public function updateRegPd(Request $r)
    {
        $this->validate($r, [
            'tgl_masuk' => 'required|date',
            'jns_pendaftaran' => 'required',
            'waktu_kuliah' => 'required',
            'dosen_pa' => 'required',
            'kode_kelas' => 'required',
            'kurikulum' => 'required',
        ]);

        try {

            DB::transaction(function () use ($r) {
                $mhs2 = Mahasiswareg::find($r->id);
                $mhs2->id_konsentrasi = empty($r->konsentrasi) ? null : $r->konsentrasi;
                $mhs2->jenis_daftar = $r->jns_pendaftaran;
                $mhs2->jalur_masuk = empty($r->jalur_pendaftaran) ? null : $r->jalur_pendaftaran;
                $mhs2->nm_pt_asal = empty($r->asal_pt) ? null : $r->asal_pt;
                $mhs2->nm_prodi_asal = empty($r->asal_prodi) ? null : $r->asal_prodi;
                $mhs2->tgl_daftar = Carbon::parse($r->tgl_masuk)->format('Y-m-d');
                $mhs2->jam_kuliah = $r->waktu_kuliah;
                $mhs2->dosen_pa = $r->dosen_pa;
                $mhs2->kode_kelas = $r->kode_kelas;
                $mhs2->id_kurikulum = $r->kurikulum;
                $mhs2->save();
            });
        } catch (\Exception $e) {
            Rmt::error($e->getMessage());
            return redirect()->back();
        }

        // Update di feeder
        // $this->feederUpdateRegpd($r->id);

        Rmt::success('Berhasil mengubah data');
        return redirect()->back();
    }

    public function deleteRegPd($id)
    {
        $rule_1 = KrsStatus::where('id_mhs_reg', $id)->count();
        $rule_2 = DB::table('nilai_transfer')->where('id_mhs_reg', $id)->count();

        if ($rule_1 > 0) {
            Rmt::error('Data tidak bisa dihapus. Hapus dahulu History pembayaran/data pembayaran dengan mengeset status bayar menjadi Belum Bayar pada modul pembayaran. Hubungi Bagian Keuangan');
            return redirect()->back();
        }

        if ($rule_2 > 0) {
            Rmt::error('Data tidak bisa dihapus. Hapus dahulu nilai konversinya.');
            return redirect()->back();
        }

        Mahasiswareg::where('id', $id)->delete();
        DB::table('nilai')->where('id_mhs_reg', $id)->delete();

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }
    /* end reg pd */

    public function krs(Request $r, $id)
    {

        if ($r->ubah_nim) {
            $regpd = DB::table('mahasiswa_reg as m2')
                ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
                ->select('m2.id', 'm2.nim')
                ->where('m2.id', $r->ubah_nim)
                ->first();
            Session::put('krs_id_mhs', $regpd->id);
        } else {
            Session::put('krs_id_mhs', Sia::getRegpdPertama($id));
        }

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.id', 'm.nm_mhs', 'm2.id as id_reg_pd', 'm2.nim', 'm2.id_prodi', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', Session::get('krs_id_mhs'))->first();

        $data['nim'] = DB::table('mahasiswa_reg')
            ->where('id_mhs', $id)
            ->select('id', 'nim')
            ->get();

        if (!empty($r->ubah_jenis)) {
            Session::put('krs_jeniskrs', $r->ubah_jenis);
        } else {
            Session::put('krs_jeniskrs', 1);
        }

        // $data['krs'] = Sia::krsMhs(Session::get('krs_id_mhs'),Sia::sessionPeriode(), Session::get('krs_jeniskrs'))->get();

        // for right menu
        $data['id_mahasiswa'] = $id;

        $jenis_krs_str = Session::get('krs_jeniskrs') == 1 ? 'KULIAH' : 'SP';

        // Aktifkan apabila telah selesai KRSan Pendek
        // $data['status_bayar'] = DB::table('krs_status')
        //     ->where('id_mhs_reg', Session::get('krs_id_mhs'))
        //     ->where('id_smt', Sia::sessionPeriode())
        //     ->where('jenis', $jenis_krs_str)
        //     ->count();

        $data['status_bayar'] = 1;

        $data['jdw_akademik'] = DB::table('jadwal_akademik')
            ->where('id_fakultas', Sia::getFakultasUser($data['mhs']->id_prodi))
            ->first();

        return view('mahasiswa.krs', $data);
    }

    public function getJadwal(Request $r)
    {
        $data = Sia::jadwalKuliahMahasiswa(Session::get('krs_id_mhs'), Session::get('krs_jeniskrs'))
            ->where('jdk.id_smt', Sia::sessionPeriode())->get();
        $total_sks = 0;
        $no = 1;

        foreach ($data as $r) {?>
      <tr>
        <td><?=$no++?></td>
        <td align="center">
          <?=empty($r->hari) ? '-' : Rmt::hari($r->hari)?><br>
          <?=substr($r->jam_masuk, 0, 5)?> - <?=substr($r->jam_keluar, 0, 5)?>
        </td>
        <td align="left">
          <?=$r->kode_mk?> - <?=$r->nm_mk?>
        </td>
        <td><?=$r->sks_mk?></td>
        <td><?=$r->kode_kls?><br><?=$r->nm_ruangan?></td>
        <td align="left"><?=$r->dosen?></td>
        <td>
          <?php if (Sia::canAction($r->id_smt)) {?>
            <a href="<?=route('mahasiswa_krs_delete', ['id' => $r->id_krs])?>" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
          <?php }?>
        </td>
      </tr>
      <?php $total_sks += $r->sks_mk;?>
    <?php }?>
    <tr>
      <td colspan="3" align="center"><b>TOTAL SKS</b></td>
      <td><b><?=$total_sks?></b></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  <?php
}

    public function getKrs(Request $r)
    {
        $param = $r->input('query');
        if (!empty($r->query)) {
            $krs = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'mkur.id', '=', 'jdk.id_mkur')
                ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                ->leftJoin('prodi as pr', 'mk.id_prodi', '=', 'pr.id_prodi')
                ->leftJoin('ruangan as r', 'jdk.ruangan', '=', 'r.id')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                ->leftJoin('semester as smt', 'jdk.id_smt', '=', 'smt.id_smt')
                ->select('jdk.*', 'kur.nm_kurikulum', 'mkur.smt', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk', 'pr.nm_prodi', 'mkur.smt')
                ->where('jdk.id_smt', Sia::sessionPeriode())
                ->where('jdk.id_prodi', $r->prodi)
                ->where('jdk.jenis', $r->jeniskrs)
                ->where(function ($q) use ($param) {
                    $q->where('mk.kode_mk', 'like', '%' . $param . '%')
                        ->orWhere('mk.nm_mk', 'like', '%' . $param . '%')
                        ->orWhere('jdk.kode_kls', 'like', '%' . $param . '%');
                })->orderBy('mk.nm_mk', 'asc')->take(30)->get();
        } else {
            $krs = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'mkur.id', '=', 'jdk.id_mkur')
                ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                ->leftJoin('prodi as pr', 'mk.id_prodi', '=', 'pr.id_prodi')
                ->leftJoin('ruangan as r', 'jdk.ruangan', '=', 'r.id')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', '=', 'jk.id')
                ->leftJoin('semester as smt', 'jdk.id_smt', '=', 'smt.id_smt')
                ->select('jdk.*', 'kur.nm_kurikulum', 'mkur.smt', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk', 'pr.nm_prodi', 'mkur.smt')
                ->where('jdk.id_smt', Sia::sessionPeriode())
                ->where('jdk.id_prodi', $r->prodi)
                ->where('jdk.jenis', $r->jeniskrs)
                ->orderBy('mk.nm_mk', 'asc')->take(30)->get();
        }
        $data = [];
        foreach ($krs as $r) {
            $data[] = [
                'data' => $r->id,
                'value' => $r->kode_mk . ' - ' . $r->nm_mk . ' (' . $r->sks_mk . ' sks) - ' . $r->kode_kls . ' - ' . $r->nm_kurikulum . ' - smstr ' . $r->smt,
                'sks' => $r->sks_mk,
                'semester_mk' => $r->smt,
                'id_mk' => $r->id_mk,
                'jam' => $r->id_jam,
                'hari' => $r->hari,
            ];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response, 200);
    }

    public function storeKrs(Request $r)
    {
        $this->validate($r, [
            'jadwal' => 'required',
        ]);
        $jenis_krs_str = $r->jenis_jadwal == 1 ? 'KULIAH' : 'SP';

        // Matakuliah Sudah ada
        $rule_2 = Sia::krsMhs(Session::get('krs_id_mhs'), '', $r->jenis_jadwal)->where('mkur.id_mk', $r->id_mk)->count();
        if ($rule_2 > 0) {
            return Response::json(['error' => 1, 'msg' => 'Matakuliah ini telah diambil'], 200);
        }

        if ($r->jenis_jadwal == 1) {
            // Jadwal perkuliahan

            // Pastikan mahasiswa sudah membayar
            $krs_stat = Sia::krsStatus($r->id_mhs_reg, $jenis_krs_str);

            // Sudah bayar
            if (!$krs_stat) {
                return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini belum membayar'], 200);
            }

            // SKS semester > n jika perkuliahan
            $rule_3 = Sia::sksSemester($r->id_mhs_reg);
            if (($rule_3 + $r->sks) > Sia::maxTakeSks()) {
                return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini telah melebihi batas SKS Total persemester. Total krs yang bisa diprogram tidak boleh lebih dari ' . Sia::maxTakeSks() . ' SKS'], 200);
            }
        } else {

            // SKS semester > n
            $rule_3 = Sia::sksSemester($r->id_mhs_reg, 2);

            if (($rule_3 + $r->sks) > Sia::maxTakeSks('sp')) {
                return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini telah melebihi batas SKS Total semester pendek. Total krs yang bisa diprogram tidak boleh lebih dari ' . Sia::maxTakeSks('sp') . ' SKS'], 200);
            }

            // Matakuliah belum diprogramkan?
            // $rule_4 = DB::table('nilai as n')
            //             ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
            //             ->leftJoin('mk_kurikulum as mkur', 'mkur.id', 'jdk.id_mkur')
            //             ->where('n.id_mhs_reg', $r->id_mhs_reg)
            //             ->where('mkur.id_mk', $r->id_mk)
            //             ->count();

            // if ( $rule_4 == 0 ) {

            //     return Response::json(['error' => 1, 'msg' => 'Matakuliah ini belum pernah diprogram oleh mahasiswa ini'],200);
            // }
        }

        // Jam kuliah mhs tidak bentrok
        if ($r->hari != 0) {

            $rule_5 = Sia::validasiBentrokMK($r->id_mhs_reg, $r->hari, $r->jam);

            if (!empty($rule_5)) {
                return Response::json(['error' => 1, 'msg' => 'Terdapat tabrakan jam kuliah dengan matakuliah: ' . $rule_5->kode_mk . ' - ' . $rule_5->nm_mk], 200);
            }
        }

        try {

            DB::transaction(function () use ($r, $jenis_krs_str) {

                $nilai = new Nilai;
                $nilai->id = Rmt::uuid();
                $nilai->id_mhs_reg = $r->id_mhs_reg;
                $nilai->id_jdk = $r->jadwal;
                $nilai->semester_mk = $r->semester_mk;
                $nilai->save();

                DB::table('krs_status')
                    ->where('id_mhs_reg', $r->id_mhs_reg)
                    ->where('id_smt', Sia::sessionPeriode())
                    ->where('jenis', $jenis_krs_str)
                    ->update(['status_krs' => '1']);
            });
        } catch (\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
        }

        Rmt::success('Berhasil menyimpan data');
        return Response::json(['error' => 0, 'msg' => 1], 200);
    }

    public function deleteKrs($id)
    {
        DB::table('nilai')
            ->where('id', $id)->delete();

        Rmt::success('Berhasil Menghapus data');

        return redirect()->back();
    }

    public function nilai(Request $r, $id)
    {
        if (!empty($r->ubah_nim)) {
            $regpd = DB::table('mahasiswa_reg as m2')
                ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
                ->select('m2.id', 'm2.nim')
                ->where('m2.id', $r->ubah_nim)
                ->first();
            Session::put('id_regpd_in_nilai', $regpd->id);
        }

        if (!empty($r->smt)) {
            Session::put('smt_in_nilai', $r->smt);
        }

        // Jika membuka mahasiswa yang lain unset session sebelumnya
        if ($id != Session::get('id_mhs_in_nilai')) {
            Session::put('id_mhs_in_nilai', $id);
            Session::put('id_regpd_in_nilai', Sia::getRegpdPertama($id));
            Session::put('smt_in_nilai', Sia::sessionPeriode());
        }

        if (!Session::has('smt_in_nilai')) {
            Session::put('smt_in_nilai', Sia::sessionPeriode());
        }

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.id', 'm.nm_mhs', 'm2.id as id_reg_pd', 'm2.nim', 'm2.semester_mulai', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', Session::get('id_regpd_in_nilai'))->first();

        $data['nim'] = DB::table('mahasiswa_reg')
            ->where('id_mhs', $id)
            ->select('id', 'nim')
            ->get();

        $data['semester'] = DB::table('semester')
            ->whereBetween('id_smt', [$data['mhs']->semester_mulai, Sia::semesterBerjalan()['id']])
            ->orderBy('id_smt', 'desc')->get();

        if (!empty($r->ubah_jenis)) {
            Session::put('jeniskrs_in_nilai', $r->ubah_jenis);
        } else {
            if (!Session::has('jeniskrs_in_nilai')) {
                Session::put('jeniskrs_in_nilai', 1);
            }
        }

        $data['krs'] = Sia::krsMhs(Session::get('id_regpd_in_nilai'), Session::get('smt_in_nilai'), Session::get('jeniskrs_in_nilai'))
            ->select('n.*', 'jdk.id_prodi', 'jdk.kode_kls', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk')
            ->get();


        $mhsReg = Session::get('id_regpd_in_nilai');
        $idSmt = Session::get('smt_in_nilai');

        // dd($mhsReg, $idSmt);

        $data['mbkm'] = DB::table('nilai_mbkm as nm')
            ->leftJoin('matakuliah as mk', 'mk.id', '=', 'nm.id_mk')
            ->where('nm.id_mhs_reg', $mhsReg)
            ->where('nm.id_smt', $idSmt)
            ->get();



        $data['ipk'] = Sia::ipkKhs(Session::get('id_regpd_in_nilai'), $data['mhs']->semester_mulai, Session::get('smt_in_nilai'));

        // for right menu
        $data['id_mahasiswa'] = $id;



        return view('mahasiswa.nilai', compact('data'));
    }

    public function nilaiUpdate(Request $r)
    {
        $q_indeks = DB::table('skala_nilai')
            ->select('nilai_indeks')
            ->where('id_prodi', $r->id_prodi)
            ->where('nilai_huruf', $r->nilai)
            ->first();
        if (!empty($q_indeks)) {
            $indeks = $q_indeks->nilai_indeks;
        } else {
            $indeks = 0.00;
        }

        $nil = Nilai::where('id', $r->id_nilai)->first();
        $nil->nilai_huruf = $r->nilai;
        $nil->nilai_indeks = $indeks;
        $nil->save();
    }

    public function nilaiCetak(Request $r)
    {
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
            ->select(
                'm.nm_mhs',
                'm2.nim',
                'm2.semester_mulai',
                'm.jenkel',
                'p.nm_prodi',
                'p.jenjang',
                'smt.nm_smt',
                'smt.id_smt',
                'k.nm_konsentrasi'
            )
            ->where('m2.id', Session::get('id_regpd_in_nilai'))->first();

        $data['krs'] = Sia::krsMhs(Session::get('id_regpd_in_nilai'), Session::get('smt_in_nilai'), Session::get('jeniskrs_in_nilai'))
            ->select('n.*', 'jdk.kode_kls', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk')
            ->get();
        
        $mhsReg = Session::get('id_regpd_in_nilai');
        $idSmt = Session::get('smt_in_nilai');

        $data['ipk'] = Sia::ipkKhs(Session::get('id_regpd_in_nilai'), $data['mhs']->semester_mulai, Session::get('smt_in_nilai'));
        $data['mbkm'] = DB::table('nilai_mbkm as nm')
            ->leftJoin('matakuliah as mk', 'mk.id', '=', 'nm.id_mk')
            ->where('nm.id_mhs_reg', $mhsReg)
            ->where('nm.id_smt', $idSmt)
            ->get();
        return view('mahasiswa.print-khs', $data);
    }

    public function aktivitas($id)
    {
        $data['mhs'] = DB::table('mahasiswa as m')
            ->leftJoin('agama as a', 'm.id_agama', '=', 'a.id_agama')
            ->select('m.nm_mhs', 'm.tempat_lahir', 'm.tgl_lahir', 'm.jenkel', 'a.nm_agama')
            ->where('m.id', $id)->first();

        $mhs_reg = DB::table('mahasiswa_reg')
            ->select('id', 'nim')
            ->where('id_mhs', $id)
            ->orderBy('semester_mulai', 'desc')
            ->get()->toArray();

        // Ganti nim
        if (!empty($r->id_reg_pd)) {
            Session::pull('akm_mhs_reg');
            Session::put('akm_mhs_reg', [$r->id_reg_pd, $r->nim]);
        }

        // Jika membuka mahasiswa yang lain unset session sebelumnya
        if ($id != Session::get('konfersi_idmhs')) {
            Session::put('akm_mhs_reg', [$mhs_reg[0]->id, $mhs_reg[0]->nim]);
            Session::put('konfersi_idmhs', $id);
        }

        $data['aktivitas'] = DB::table('aktivitas_kuliah as akm')
            ->leftJoin('status_mhs as s', 'akm.status_mhs', 's.id_stat_mhs')
            ->leftJoin('semester as smt', 'smt.id_smt', 'akm.id_smt')
            ->select('akm.*', 's.nm_stat_mhs', 'smt.nm_smt')
            ->where('akm.id_mhs_reg', $mhs_reg[0]->id)
            ->orderBy('akm.id_smt')->get();

        $data['id_mahasiswa'] = $id;
        $data['mhs_reg'] = $mhs_reg;

        return view('mahasiswa.aktivitas', $data);
    }

    public function add(Request $r)
    {
        $can_aksi = Sia::canAction(Sia::sessionPeriode('berjalan'));

        if (!$can_aksi) {
            dd('Anda tidak bisa menambah mahasiswa pada periode ini.');
        }

        $data['agama'] = DB::table('agama')->get();
        $data['jenisTinggal'] = DB::table('jenis_tinggal')->get();
        $data['alatTranspor'] = DB::table('alat_transpor')->get();
        $data['alatTranspor'] = DB::table('alat_transpor')->get();
        $data['jnsPendaftaran'] = DB::table('jenis_pendaftaran')->get();
        $data['jalurMasuk'] = DB::table('jalur_masuk')->get();

        if (Sia::admin()) {
            $data['prodi'] = DB::table('prodi')->orderBy('jenjang')->get();
        } else {
            $data['prodi'] = DB::table('prodi')->whereIn('id_prodi', Sia::getProdiUser())->orderBy('jenjang')->get();
        }

        $data['penghasilan'] = DB::table('penghasilan')->get();
        $data['pekerjaan'] = DB::table('pekerjaan')->get();
        $data['pdk'] = DB::table('pendidikan')->get();
        $data['infoNobel'] = DB::table('info_nobel')->get();

        return view('mahasiswa.add', $data);
    }

    public function getKurikulum(Request $r)
    {
        $data = DB::table('kurikulum')
            ->where('id_prodi', $r->prodi)->orderBy('mulai_berlaku', 'desc')->get();?>
    <select name="kurikulum" class="form-control">
      <option value="">--Pilih Kurikulum--</option>
      <?php foreach ($data as $kur) {?>
        <option value="<?=$kur->id?>"><?=$kur->nm_kurikulum?></option>
      <?php }?>
    </select>
  <?php

    }

    public function negara(Request $r)
    {
        $param = $r->input('query');
        if (!empty($r->query)) {
            $negara = DB::table('kewarganegaraan')
                ->where('nm_wil', 'like', '%' . $param . '%')->orderBy('nm_wil')->get();
        } else {
            $negara = DB::table('kewarganegaraan')->orderBy('nm_wil')->get();
        }
        $data = [];
        foreach ($negara as $r) {
            $data[] = ['data' => $r->kewarganegaraan, 'value' => trim($r->nm_wil)];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response, 200);
    }

    public function kecamatan(Request $r)
    {
        $param = $r->input('query');
        if (!empty($r->query)) {
            $wilayah = DB::table('wilayah as w')
                ->join('wilayah as dw', 'w.id_wil', '=', 'dw.id_induk_wilayah')
                ->join('wilayah as dwc', 'dw.id_wil', '=', 'dwc.id_induk_wilayah')
                ->select('dwc.id_wil as id_wil', 'w.nm_wil as provinsi', 'dw.nm_wil as kab', 'dwc.nm_wil as kecamatan')
                ->where('w.id_level_wil', 1)
                ->where('dwc.nm_wil', 'like', '%' . $param . '%')
                ->orWhere('dw.nm_wil', 'like', '%' . $param . '%')
                ->get();
        } else {
            $wilayah = DB::table('wilayah as w')
                ->join('wilayah as dw', 'w.id_wil', '=', 'dw.id_induk_wilayah')
                ->join('wilayah as dwc', 'dw.id_wil', '=', 'dwc.id_induk_wilayah')
                ->select('dwc.id_wil as id_wil', 'w.nm_wil as provinsi', 'dw.nm_wil as kab', 'dwc.nm_wil as kecamatan')
                ->where('w.id_level_wil', 1)
                ->get();
        }
        $data = [];
        foreach ($wilayah as $r) {
            $data[] = ['data' => $r->id_wil, 'value' => trim($r->kecamatan) . " - " . trim($r->kab) . " - " . trim($r->provinsi)];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response, 200);
    }

    public function konsentrasi(Request $r)
    {?>

    <select class="form-control" name="konsentrasi">
      <option value="">-- Pilih konsentrasi --</option>
      <?php foreach (Sia::listKonsentrasi($r->prodi) as $r) {?>
        <option value="<?=$r->id_konsentrasi?>"><?=$r->nm_konsentrasi?></option>
      <?php }?>
    </select>
  <?php
}
    public function store(Request $r)
    {
        $this->validate($r, [
            'nama' => 'required',
            'biaya_masuk' => 'required',
            'waktu_kuliah' => 'required',
            'kurikulum' => 'required',
            'dosen_pa' => 'required',
            'kode_kelas' => 'required',
            'tempat_lahir' => 'required',
            'nama_ibu' => 'required|string|min:3',
            'tgl_lahir' => 'required|date',
            'agama' => 'required',
            'tgl_masuk' => 'required|date',
            'jns_pendaftaran' => 'required',
            'prodi' => 'required',
            'nik' => 'numeric|required|unique:mahasiswa|min:16',
            'kewarganegaraan' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'npwp' => 'numeric|max:15',
            'rt' => 'numeric',
            'rw' => 'numeric',
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500',
        ]);

        $data = [
            'id_user' => Rmt::uuid(),
            'nim' => Sia::generateNim($r->prodi),
            'id_mhs' => Rmt::uuid(),
            'id_mhsreg' => Rmt::uuid(),
        ];

        if (in_array($r->jns_pendaftaran, [2, 11]) && empty($r->id_perguruan_tinggi)) {
            $response = ['error' => 1, 'msg' => 'Perguruan tinggi asal harus diisi.'];
            return Response::json($response);
        }

        if (in_array($r->jns_pendaftaran, [2, 11]) && empty($r->id_prodi_asal)) {
            $response = ['error' => 1, 'msg' => 'Program studi asal harus diisi.'];
            return Response::json($response);
        }

        // validasi tgl daftar
        $tahun_daftar = Carbon::parse($r->tgl_daftar)->format('Y');
        $tahun_now = date('Y');

        if ($tahun_daftar != $tahun_now) {
            $response = ['error' => 1, 'msg' => 'Tahun pada tanggal daftar salah'];
            return Response::json($response, 200);
        }

        if ($r->hasFile('foto')) {
            // Upload original image
            $imageName = $data['nim'] . '.' . $r->foto->getClientOriginalExtension();
            $path = config('app.foto-mhs');
            $r->foto->move($path, $imageName);

            // Generate thumbnail
            $img = Image::make($path . '/' . $imageName);
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path . '/thumb/' . $imageName);
        } else {
            $imageName = '';
        }

        try {

            DB::beginTransaction();

            $id_mhs = '';

            $user = new User;
            $user->id = $data['id_user'];
            $user->nama = $r->nama;
            $user->username = $data['nim'];
            $user->email = empty($r->email) ? $data['nim'] . '@stienobel-indonesia.ac.id' : $r->email;
            $user->password = bcrypt(Carbon::parse($r->tgl_lahir)->format('dmY'));
            $user->level = 'mahasiswa';
            $user->save();

            $mhs = new Mahasiswa;
            $mhs->id = $data['id_mhs'];
            $mhs->id_user = $data['id_user'];
            $mhs->nm_mhs = $r->nama;
            $mhs->gelar_depan = $r->gelar_depan;
            $mhs->gelar_belakang = $r->gelar_belakang;
            $mhs->jenkel = $r->jenis_kelamin;
            $mhs->nik = $r->nik;
            $mhs->nisn = $r->nisn;
            $mhs->npwp = $r->npwp;
            $mhs->tempat_lahir = $r->tempat_lahir;
            $mhs->tgl_lahir = Carbon::parse($r->tgl_lahir)->format('Y-m-d');
            $mhs->id_agama = $r->agama;
            $mhs->alamat = $r->alamat;
            $mhs->dusun = $r->dusun;
            $mhs->des_kel = $r->kelurahan;
            $mhs->rt = $r->rt;
            $mhs->rw = $r->rw;
            $mhs->id_wil = $r->kecamatan;
            $mhs->pos = $r->pos;
            $mhs->hp = $r->hp;
            $mhs->email = empty($r->email) ? $data['nim'] . '@nobel.ac.id' : $r->email;
            $mhs->kewarganegaraan = $r->kewarganegaraan;
            $mhs->nm_sekolah = $r->nm_sekolah;
            $mhs->tahun_lulus_sekolah = $r->thn_lulus_sekolah;
            $mhs->nik_ibu = $r->nik_ibu;
            $mhs->nm_ibu = $r->nama_ibu;
            $mhs->tgl_lahir_ibu = empty($r->tgl_lahir_ibu) ? null : Rmt::formatTgl($r->tgl_lahir_ibu, 'Y-m-d');
            $mhs->id_pdk_ibu = empty($r->pdk_ibu) ? null : $r->pdk_ibu;
            $mhs->id_pekerjaan_ibu = empty($r->pekerjaan_ibu) ? null : $r->pekerjaan_ibu;
            $mhs->id_penghasilan_ibu = empty($r->penghasilan_ibu) ? null : $r->penghasilan_ibu;
            $mhs->hp_ibu = $r->hp_ibu;
            $mhs->nik_ayah = $r->nik_ayah;
            $mhs->nm_ayah = $r->nama_ayah;
            $mhs->tgl_lahir_ayah = empty($r->tgl_lahir_ayah) ? null : Rmt::formatTgl($r->tgl_lahir_ayah, 'Y-m-d');
            $mhs->id_pdk_ayah = empty($r->pdk_ayah) ? null : $r->pdk_ayah;
            $mhs->id_pekerjaan_ayah = empty($r->pekerjaan_ayah) ? null : $r->pekerjaan_ayah;
            $mhs->hp_ayah = $r->hp_ayah;
            $mhs->id_penghasilan_ayah = empty($r->penghasilan_ayah) ? null : $r->penghasilan_ayah;
            $mhs->nm_wali = $r->nama_wali;
            $mhs->tgl_lahir_wali = empty($r->tgl_lahir_wali) ? null : Rmt::formatTgl($r->tgl_lahir_wali, 'Y-m-d');
            $mhs->id_pdk_wali = empty($r->pdk_wali) ? null : $r->pdk_wali;
            $mhs->id_pekerjaan_wali = empty($r->pekerjaan_wali) ? null : $r->pekerjaan_wali;
            $mhs->id_penghasilan_wali = empty($r->penghasilan_wali) ? null : $r->penghasilan_wali;
            $mhs->hp_wali = $r->hp_wali;
            $mhs->jenis_tinggal = empty($r->jenis_tinggal) ? null : $r->jenis_tinggal;
            $mhs->alat_transpor = empty($r->alat_transpor) ? null : $r->alat_transpor;
            $mhs->foto_mahasiswa = $imageName;
            $mhs->id_info_nobel = empty($r->info_nobel) ? null : $r->info_nobel;
            $mhs->save();

            $id_mhs = $mhs->last_id;

            $mhs2 = new Mahasiswareg;
            $mhs2->id = $data['id_mhsreg'];
            $mhs2->id_prodi = $r->prodi;
            $mhs2->id_konsentrasi = empty($r->konsentrasi) ? null : $r->konsentrasi;
            $mhs2->id_mhs = $data['id_mhs'];
            $mhs2->jenis_daftar = $r->jns_pendaftaran;
            $mhs2->jalur_masuk = empty($r->jalur_pendaftaran) ? null : $r->jalur_pendaftaran;
            $mhs2->nm_pt_asal = empty($r->asal_pt) ? null : $r->asal_pt;
            $mhs2->nm_prodi_asal = empty($r->asal_prodi) ? null : $r->asal_prodi;
            $mhs2->nim = $data['nim'];
            $mhs2->semester_mulai = Sia::sessionPeriode();
            $mhs2->tgl_daftar = Carbon::parse($r->tgl_daftar)->format('Y-m-d');
            $mhs2->jam_kuliah = $r->waktu_kuliah;
            $mhs2->dosen_pa = $r->dosen_pa;
            $mhs2->kode_kelas = $r->kode_kelas;
            $mhs2->id_kurikulum = $r->kurikulum;
            $mhs2->id_maba = $r->id_maba;

            $mhs2->id_jenis_pembiayaan = 1;
            $mhs2->biaya_masuk = $r->biaya_masuk;

            if (in_array($r->jns_pendaftaran, [2, 11])) {
                $mhs2->id_pt_asal = $r->id_perguruan_tinggi;
                $mhs2->id_prodi_asal = $r->id_prodi_asal;
            }

            $mhs2->id_maba = $r->id_maba;
            $mhs2->save();

            Rmt::Success('Berhasil menyimpan data');
            $response = ['error' => 0, 'msg' => $data['id_mhs']];

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            $response = ['error' => 1, 'msg' => $e->getMessage()];
            return Response::json($response, 200);
        }

        // Import to feeder
        // $this->feederStore($id_mhs);

        return Response::json($response, 200);
    }

    // Insert
    public function impor2(Request $request)
    {
        if ($request->hasFile('file')) {

            if (!is_dir(storage_path() . '/tmp')) {
                mkdir(storage_path() . '/tmp');
            }

            $nama_file = $request->file('file')->getClientOriginalName();
            $request->file('file')->move(storage_path() . '/tmp', $nama_file);
            $file_impor = storage_path() . '/tmp/' . $nama_file;

            try {

                DB::transaction(function () use ($file_impor, $nama_file) {

                    Excel::filter('chunk')->load($file_impor)->chunk(1000, function ($results) use ($nama_file, &$no) {
                        foreach ($results as $r) {

                            $id_mhs = Rmt::uuid();
                            $id_user = Rmt::uuid();

                            $users = [
                                'id' => $id_user,
                                'nama' => $r->nm_mhs,
                                'username' => $r->nim,
                                'email' => empty($r->email) ? $r->nim . '@stienobel-indonesia.ac.id' : $r->email,
                                'password' => bcrypt(Carbon::parse($r->tgl_lahir)->format('dmY')),
                                'level' => 'mahasiswa',
                            ];

                            $mhs = [
                                'id' => $id_mhs,
                                'id_user' => $id_user,
                                'nm_mhs' => trim($r->nm_mhs),
                                'jenkel' => trim($r->jenkel),
                                'nik' => trim($r->nik),
                                'nisn' => trim($r->nisn),
                                'npwp' => trim($r->npwp),
                                'tempat_lahir' => trim($r->tempat_lahir),
                                'tgl_lahir' => empty($r->tgl_lahir) || $r->tgl_lahir == 'NULL' ? null : $r->tgl_lahir,
                                'id_agama' => trim($r->id_agama),
                                'alamat' => trim($r->alamat),
                                'dusun' => trim($r->dusun),
                                'des_kel' => trim($r->des_kel),
                                'rt' => trim($r->rt),
                                'rw' => trim($r->rw),
                                'id_wil' => trim($r->id_wil),
                                'pos' => trim($r->pos),
                                'hp' => trim($r->hp),
                                'email' => empty($r->email) ? $r->nim . '@stienobel-indonesia.ac.id' : $r->email,
                                'kewarganegaraan' => trim($r->kewarganegaraan),
                                'nm_sekolah' => trim($r->nm_sekolah),
                                'tahun_lulus_sekolah' => trim($r->tahun_lulus_sekolah),
                                'nik_ibu' => trim($r->nik_ibu),
                                'nm_ibu' => trim($r->nm_ibu),
                                'tgl_lahir_ibu' => empty($r->tgl_lahir_ibu) || $r->tgl_lahir_ibu == 'NULL' ? null : $r->tgl_lahir_ibu,
                                'id_pdk_ibu' => empty($r->id_pdk_ibu) || $r->id_pdk_ibu == 'NULL' ? null : $r->id_pdk_ibu,
                                'id_pekerjaan_ibu' => empty($r->id_pekerjaan_ibu) || $r->id_pekerjaan_ibu == 'NULL' ? null : $r->id_pekerjaan_ibu,
                                'id_penghasilan_ibu' => empty($r->id_penghasilan_ibu) || $r->id_penghasilan_ibu == 'NULL' ? null : $r->id_penghasilan_ibu,
                                'hp_ibu' => trim($r->hp_ibu),
                                'nik_ayah' => trim($r->nik_ayah),
                                'nm_ayah' => trim($r->nama_ayah),
                                'tgl_lahir_ayah' => empty($r->tgl_lahir_ayah) || $r->tgl_lahir_ayah == 'NULL' ? null : $r->tgl_lahir_ayah,
                                'id_pdk_ayah' => empty($r->id_pdk_ayah) || $r->id_pdk_ayah == 'NULL' ? null : $r->id_pdk_ayah,
                                'id_pekerjaan_ayah' => empty($r->id_pekerjaan_ayah) || $r->id_pekerjaan_ayah == 'NULL' ? null : $r->id_pekerjaan_ayah,
                                'hp_ayah' => trim($r->hp_ayah),
                                'id_penghasilan_ayah' => empty($r->id_penghasilan_ayah) || $r->id_penghasilan_ayah == 'NULL' ? null : $r->id_penghasilan_ayah,
                                'nm_wali' => trim($r->nama_wali),
                                'tgl_lahir_wali' => empty($r->tgl_lahir_wali) || $r->tgl_lahir_wali == 'NULL' ? null : $r->tgl_lahir_wali,
                                'id_pdk_wali' => empty($r->id_pdk_wali) || $r->id_pdk_wali == 'NULL' ? null : $r->id_pdk_wali,
                                'id_pekerjaan_wali' => empty($r->id_pekerjaan_wali) || $r->id_pekerjaan_wali == 'NULL' ? null : $r->id_pekerjaan_wali,
                                'id_penghasilan_wali' => empty($r->id_penghasilan_wali) || $r->id_penghasilan_wali == 'NULL' ? null : $r->id_penghasilan_wali,
                                'hp_wali' => trim($r->hp_wali),
                                'jenis_tinggal' => empty($r->jenis_tinggal) || $r->jenis_tinggal == 'NULL' ? null : $r->jenis_tinggal,
                                'alat_transpor' => empty($r->alat_transpor) || $r->alat_transpor == 'NULL' ? null : $r->alat_transpor,
                                'id_info_nobel' => empty($r->id_info_nobel) || $r->id_info_nobel == 'NULL' ? null : $r->id_info_nobel,
                                'created_at' => Carbon::now()->format('Y-m-d'),
                            ];

                            $mhs_reg = [
                                'id' => Rmt::uuid(),
                                'id_prodi' => $r->id_prodi,
                                'id_konsentrasi' => empty($r->id_konsentrasi) || $r->id_konsentrasi == 'NULL' ? null : $r->id_konsentrasi,
                                'id_mhs' => $id_mhs,
                                'jenis_daftar' => $r->jenis_daftar,
                                'jam_kuliah' => $r->jam_kuliah,
                                'jalur_masuk' => $r->jalur_masuk,
                                'nim' => $r->nim,
                                'tgl_daftar' => empty($r->tgl_daftar) || $r->tgl_daftar == 'NULL' ? null : $r->tgl_daftar,
                                'dosen_pa' => $r->dosen_pa,
                                'id_kurikulum' => $r->id_kurikulum,
                                'id_jenis_keluar' => $r->id_jenis_keluar,
                                'tgl_keluar' => empty($r->tgl_keluar) || $r->tgl_keluar == 'NULL' ? null : $r->tgl_keluar,
                                // 'semester_mulai' => $r->semester_mulai,
                                'semester_mulai' => 20181,
                                'semester_keluar' => $r->semester_keluar,
                                'jalur_skripsi' => $r->jalur_skripsi,
                                'judul_skripsi' => $r->judul_skripsi,
                                'awal_bimbingan' => empty($r->awal_bimbingan) || $r->awal_bimbingan == 'NULL' ? null : $r->awal_bimbingan,
                                'akhir_bimbingan' => empty($r->akhir_bimbingan) || $r->akhir_bimbingan == 'NULL' ? null : $r->akhir_bimbingan,
                                'sk_yudisium' => $r->sk_yudisium,
                                'tgl_sk_yudisium' => empty($r->tgl_sk_yudisium) || $r->tgl_sk_yudisium == 'NULL' ? null : $r->tgl_sk_yudisium,
                                'seri_ijazah' => $r->seri_ijazah,
                                'nm_pt_asal' => $r->nm_pt_asal,
                                'nm_prodi_asal' => $r->nm_prodi_asal,
                                'created_at' => Carbon::now()->format('Y-m-d'),
                            ];

                            DB::table('users')->insert($users);
                            DB::table('mahasiswa')->insert($mhs);
                            DB::table('mahasiswa_reg')->insert($mhs_reg);
                        }

                        if (file_exists(storage_path() . '/tmp/' . $nama_file)) {
                            unlink(storage_path() . '/tmp/' . $nama_file);
                        }
                    });
                });

                $response = ['error' => 0, 'msg' => 'Sukses memasukkan data'];
            } catch (\Exception $e) {
                $response = ['error' => 1, 'msg' => $e->getMessage()];
            }
        } else {
            $response = ['error' => 1, 'msg' => 'Tidak file dipilih'];
        }

        return Response::json($response, 200);
    }

    // Update
    public function impor(Request $request)
    {
        $error = [];
        if ($request->hasFile('file')) {

            if (!is_dir(storage_path() . '/tmp')) {
                mkdir(storage_path() . '/tmp');
            }

            $nama_file = $request->file('file')->getClientOriginalName();
            $request->file('file')->move(storage_path() . '/tmp', $nama_file);
            $file_impor = storage_path() . '/tmp/' . $nama_file;

            try {

                DB::transaction(function () use ($file_impor, $nama_file, &$error) {

                    Excel::filter('chunk')->load($file_impor)->chunk(1000, function ($results) use ($nama_file, &$error) {

                        foreach ($results as $r) {
                            $mhs_reg = Mahasiswareg::where('nim', $r->nim)->first();
                            if (empty($mhs_reg)) {
                                $error[] = $r->nim;
                                continue;
                            }

                            $mhs = Mahasiswa::find($mhs_reg->id_mhs);
                            if (empty($mhs)) {
                                $error[] = $mhs_reg->id_mhs;
                                continue;
                            }

                            $mhs->nm_mhs = trim($r->nm_mhs);
                            $mhs->jenkel = trim($r->jenkel);
                            $mhs->nik = trim($r->nik);
                            $mhs->tempat_lahir = trim($r->tempat_lahir);
                            $mhs->tgl_lahir = empty($r->tgl_lahir) || $r->tgl_lahir == 'NULL' ? null : $r->tgl_lahir;
                            $mhs->id_agama = trim($r->id_agama);
                            $mhs->alamat = trim($r->alamat);
                            $mhs->dusun = trim($r->dusun);
                            $mhs->des_kel = trim($r->des_kel);
                            $mhs->id_wil = trim($r->id_wil);
                            $mhs->hp = trim($r->hp);
                            $mhs->nm_ibu = trim($r->nm_ibu);
                            $mhs->nm_ayah = trim($r->nm_ayah);
                            $mhs->save();
                        }

                        if (file_exists(storage_path() . '/tmp/' . $nama_file)) {
                            unlink(storage_path() . '/tmp/' . $nama_file);
                        }
                    });
                });

                $response = ['error' => 0, 'msg' => 'Sukses memasukkan data'];
            } catch (\Exception $e) {
                $response = ['error' => 1, 'msg' => $e->getMessage()];
            }
        } else {
            $response = ['error' => 1, 'msg' => 'Tidak file dipilih'];
        }

        Session::set('data_error', $error);
        return Response::json($response, 200);
    }

    public function edit($id)
    {
        $data['agama'] = DB::table('agama')->get();
        $data['jenisTinggal'] = DB::table('jenis_tinggal')->get();
        $data['alatTranspor'] = DB::table('alat_transpor')->get();
        $data['alatTranspor'] = DB::table('alat_transpor')->get();
        $data['jnsPendaftaran'] = DB::table('jenis_pendaftaran')->get();
        $data['jalurMasuk'] = DB::table('jalur_masuk')->get();
        $data['prodi'] = DB::table('prodi')->orderBy('jenjang')->get();
        $data['penghasilan'] = DB::table('penghasilan')->get();
        $data['pekerjaan'] = DB::table('pekerjaan')->get();
        $data['pdk'] = DB::table('pendidikan')->get();
        $data['infoNobel'] = DB::table('info_nobel')->get();
        $data['mhs'] = Mahasiswa::where('id', $id)->first();

        if (empty($data['mhs'])) {
            return redirect(route('mahasiswa'));
        }

        $data['id_mahasiswa'] = $id;

        return view('mahasiswa.edit', $data);
    }

    public function update(Request $r)
    {

        $this->validate($r, [
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'nama_ibu' => 'required|string|min:3',
            'tgl_lahir' => 'required|date',
            'agama' => 'required',
            'nik' => 'required|unique:mahasiswa,id,' . $r->id . '|max:16',
            'kewarganegaraan' => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'hp' => 'numeric',
            'rt' => 'numeric',
            'rw' => 'numeric',
            'npwp' => 'numeric',
            'nisn' => 'numeric',
            'pos' => 'numeric',
            'hp_ibu' => 'numeric',
            'hp_ayah' => 'numeric',
            'hp_wali' => 'numeric',
        ]);

        try {
            DB::transaction(function () use ($r, &$response) {
                $mhs = Mahasiswa::find($r->id);
                $mhs->nm_mhs = $r->nama;
                $mhs->gelar_depan = $r->gelar_depan;
                $mhs->gelar_belakang = $r->gelar_belakang;
                $mhs->jenkel = $r->jenis_kelamin;
                $mhs->nik = $r->nik;
                $mhs->nisn = $r->nisn;
                $mhs->npwp = $r->npwp;
                $mhs->tempat_lahir = $r->tempat_lahir;
                $mhs->tgl_lahir = Carbon::parse($r->tgl_lahir)->format('Y-m-d');
                $mhs->id_agama = $r->agama;
                $mhs->alamat = $r->alamat;
                $mhs->dusun = $r->dusun;
                $mhs->des_kel = $r->kelurahan;
                $mhs->rt = $r->rt;
                $mhs->rw = $r->rw;
                $mhs->id_wil = $r->kecamatan;
                $mhs->pos = $r->pos;
                $mhs->hp = $r->hp;
                $mhs->email = $r->email;
                $mhs->kewarganegaraan = $r->kewarganegaraan;
                $mhs->nm_sekolah = $r->nm_sekolah;
                $mhs->tahun_lulus_sekolah = $r->thn_lulus_sekolah;
                $mhs->nik_ibu = $r->nik_ibu;
                $mhs->nm_ibu = $r->nama_ibu;
                $mhs->tgl_lahir_ibu = empty($r->tgl_lahir_ibu) ? null : Rmt::formatTgl($r->tgl_lahir_ibu, 'Y-m-d');
                $mhs->id_pdk_ibu = empty($r->pdk_ibu) ? null : $r->pdk_ibu;
                $mhs->id_pekerjaan_ibu = empty($r->pekerjaan_ibu) ? null : $r->pekerjaan_ibu;
                $mhs->id_penghasilan_ibu = empty($r->penghasilan_ibu) ? null : $r->penghasilan_ibu;
                $mhs->hp_ibu = $r->hp_ibu;
                $mhs->nik_ayah = $r->nik_ayah;
                $mhs->nm_ayah = $r->nama_ayah;
                $mhs->tgl_lahir_ayah = empty($r->tgl_lahir_ayah) ? null : Rmt::formatTgl($r->tgl_lahir_ayah, 'Y-m-d');
                $mhs->id_pdk_ayah = empty($r->pdk_ayah) ? null : $r->pdk_ayah;
                $mhs->id_pekerjaan_ayah = empty($r->pekerjaan_ayah) ? null : $r->pekerjaan_ayah;
                $mhs->hp_ayah = $r->hp_ayah;
                $mhs->id_penghasilan_ayah = empty($r->penghasilan_ayah) ? null : $r->penghasilan_ayah;
                $mhs->nm_wali = $r->nama_wali;
                $mhs->tgl_lahir_wali = empty($r->tgl_lahir_wali) ? null : Rmt::formatTgl($r->tgl_lahir_wali, 'Y-m-d');
                $mhs->id_pdk_wali = empty($r->pdk_wali) ? null : $r->pdk_wali;
                $mhs->id_pekerjaan_wali = empty($r->pekerjaan_wali) ? null : $r->pekerjaan_wali;
                $mhs->id_penghasilan_wali = empty($r->penghasilan_wali) ? null : $r->penghasilan_wali;
                $mhs->hp_wali = $r->hp_wali;
                $mhs->jenis_tinggal = empty($r->jenis_tinggal) ? null : $r->jenis_tinggal;
                $mhs->alat_transpor = empty($r->alat_transpor) ? null : $r->alat_transpor;
                $mhs->id_info_nobel = $r->info_nobel;
                $mhs->jns_beasiswa = empty($r->jnsMhsKip) ? 1 : $r->jnsMhsKip;
                $mhs->save();

                $user = User::find($mhs->id_user);
                $user->nama = $r->nama;
                $user->email = $r->email;
                $user->save();

                Rmt::Success('Berhasil menyimpan data');
                $response = ['error' => 0, 'msg' => 'sukses'];
            });
        } catch (\Exception $e) {
            $response = ['error' => 1, 'msg' => $e->getMessage()];
            return Response::json($response, 200);
        }

        // update di feeder
        // $this->feederUpdateMhs($r->id);

        return Response::json($response, 200);
    }

    public function updatefoto(Request $r)
    {
        $this->validate($r, [
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500',
        ]);

        if ($r->hasFile('foto')) {
            // Upload original image
            $imageName = $r->nim . '.' . $r->foto->getClientOriginalExtension();
            $r->foto->move(storage_path() . '/foto-mahasiswa', $imageName);

            // Generate thumbnail
            $img = Image::make(storage_path() . '/foto-mahasiswa/' . $imageName);
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path() . '/foto-mahasiswa/thumb/' . $imageName);

            $mhs = Mahasiswa::find($r->id);
            $mhs->foto_mahasiswa = $imageName;
            $mhs->save();

            Rmt::success('Berhasil menyimpan foto');
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }

    public function delete(Request $r, $id)
    {
        try {

            $mhsreg = Mahasiswareg::where('id', $r->id_mhs_reg)
                ->where('id_jenis_keluar', 0)->first();

            if (empty($mhsreg->id)) {
                Rmt::error('Gagal menghapus, mahasiswa tidak ditemukan atau mahasiswa
							telah Lulus/Dikeluarkan');
                return redirect()->back();
            }

            $rule_1 = KrsStatus::where('id_mhs_reg', $mhsreg->id)->count();

            if ($rule_1 > 0) {
                Rmt::error('Data tidak bisa dihapus. Hapus dahulu History pembayaran/data pembayaran dengan mengeset status bayar menjadi Belum Bayar pada modul pembayaran. Hubungi Bagian Keuangan');
                return redirect()->back();
            }

            $mhs = Mahasiswa::find($id);

            DB::transaction(function () use ($mhsreg, $id, $mhs) {
                DB::table('nilai_transfer')->where('id_mhs_reg', $mhsreg->id)->delete();
                DB::table('nilai')->where('id_mhs_reg', $mhsreg->id)->delete();
                DB::table('aktivitas_kuliah')->where('id_mhs_reg', $mhsreg->id)->delete();
                DB::table('potongan_biaya_kuliah')->where('id_mhs_reg', $mhsreg->id)->delete();
                DB::table('mahasiswa_reg')->where('id', $mhsreg->id)->delete();
                DB::table('users')->where('username', $mhsreg->nim)->delete();

                $count_mhs_reg = Mahasiswareg::where('id_mhs', $id)->count();
                if ($count_mhs_reg == 0) {
                    Mahasiswa::where('id', $id)->delete();
                    if (!empty($mhs->foto_mahasiswa)) {
                        $foto = storage_path() . '/foto-mahasiswa/' . $mhs->foto_mahasiswa;
                        $foto_thumb = storage_path() . '/foto-mahasiswa/thumb/' . $mhs->foto_mahasiswa;
                        if (file_exists($foto)) {
                            unlink($foto);
                        }
                        if (file_exists($foto_thumb)) {
                            unlink($foto_thumb);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            Rmt::error('Gagal menghapus, coba ulangi lagi: ' . $e->getMessage());
            return redirect()->back();
        }

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }

    /* Begin nilai tranfer */
    public function nilaiKonfersi(Request $r, $id)
    {
        $data['id_mahasiswa'] = $id;

        $mhs_reg = DB::table('mahasiswa_reg')
            ->select('id', 'nim')
            ->where('id_mhs', $id)
            ->where('jenis_daftar', 2)->get()->toArray();

        if (!empty($r->id_reg_pd)) {
            Session::pull('konfersi_data');
            Session::put('konfersi_data', [$r->id_reg_pd, $r->nim]);
        }

        // Jika membuka mahasiswa yang lain unset session sebelumnya
        if ($id != Session::get('konfersi_idmhs')) {
            Session::put('konfersi_data', [$mhs_reg[0]->id, $mhs_reg[0]->nim]);
            Session::put('konfersi_idmhs', $id);
        }

        $data['mhs'] = DB::table('mahasiswa_reg as m1')
            ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
            ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
            ->select(
                'm2.nm_mhs',
                'm1.nim',
                'm1.semester_mulai',
                'pr.jenjang',
                'pr.id_prodi',
                'pr.nm_prodi'
            )
            ->where('m1.id', Session::get('konfersi_data')[0])
            ->first();

        $data['mhs_reg'] = $mhs_reg;

        $data['nilai'] = Sia::nilaiTransfer()
            ->where('nt.id_mhs_reg', Session::get('konfersi_data')[0])
            ->orderBy('mk.nm_mk')->get();

        return view('mahasiswa.konfersi', $data);
    }

    public function cetakKonfersi()
    {

        $data['mhs'] = DB::table('mahasiswa_reg as m1')
            ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
            ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
            ->select(
                'm2.nm_mhs',
                'm1.nim',
                'm1.semester_mulai',
                'pr.jenjang',
                'pr.id_prodi',
                'pr.nm_prodi'
            )
            ->where('m1.id', Session::get('konfersi_data')[0])
            ->first();

        $data['nilai'] = Sia::nilaiTransfer()
            ->where('nt.id_mhs_reg', Session::get('konfersi_data')[0])
            ->orderBy('mk.nm_mk')->get();

        return view('mahasiswa.print-konfersi', $data);
    }

    public function getMk(Request $r)
    {
        $param = $r->input('query');
        if (!empty($param)) {
            $matakuliah = DB::table('mk_kurikulum as mkur')
                ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                ->select('mk.id', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk')
                ->where('kur.id_prodi', $r->id_prodi)
                ->where(function ($q) use ($param) {
                    $q->where('mk.nm_mk', 'like', '%' . $param . '%')
                        ->orWhere('mk.kode_mk', 'like', '%' . $param . '%');
                })->orderBy('mk.nm_mk', 'asc')->take(10)->get();
        } else {
            $matakuliah = DB::table('mk_kurikulum as mkur')
                ->leftJoin('kurikulum as kur', 'mkur.id_kurikulum', '=', 'kur.id')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
                ->select('mk.id', 'mk.kode_mk', 'mk.nm_mk', 'mk.sks_mk')
                ->where('kur.id_prodi', $r->id_prodi)
                ->take(10)->get();
        }
        $data = [];
        foreach ($matakuliah as $res) {
            $data[] = ['data' => $res->id, 'sks' => $res->sks_mk, 'value' => $res->kode_mk . ' - ' . trim($res->nm_mk) . ' (' . $res->sks_mk . ' sks)'];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response, 200);
    }

    public function storeKonfersi(Request $r)
    {
        $this->validate($r, [
            'kodemtk_t' => 'required',
            'namamtk_t' => 'required',
            'sks_t' => 'required',
            'huruf_t' => 'required',
            'matakuliah' => 'required',
            'huruf' => 'required',
        ]);

        $nil = DB::table('skala_nilai')
            ->where('nilai_huruf', trim($r->huruf))
            ->where('id_prodi', $r->id_prodi)->first();

        if (empty($nil)) {
            return Response::json(['error' => 1, 'msg' => 'Nilai ' . $r->huruf . ' untuk prodi ini tidak ditemukan di skala nilai. Mohon lengkapi skala nilai di modul master']);
        }

        // Cek matakuliah dobel di konfersi
        // 1. Ambil Kode MK yang baru akan dimasukkan
        $mk = DB::table('matakuliah')->where('id', $r->matakuliah)->first();

        $cek = DB::table('nilai_transfer as tr')
            ->join('matakuliah as mk', 'tr.id_mk', 'mk.id')
            ->where('mk.kode_mk', $mk->kode_mk)
            ->where('id_mhs_reg', $r->id_mhs_reg)
            ->count();

        if ($cek > 0) {
            return Response::json(['error' => 1, 'msg' => 'Matakuliah "' . $mk->kode_mk . ' - ' . $mk->nm_mk . '" Telah ada.']);
        }

        try {
            $id = Rmt::uuid();

            DB::transaction(function () use ($r, $id, $nil) {

                $data = [
                    'id' => $id,
                    'id_mhs_reg' => $r->id_mhs_reg,
                    'id_mk' => $r->matakuliah,
                    'kode_mk_asal' => $r->kodemtk_t,
                    'nm_mk_asal' => $r->namamtk_t,
                    'sks_asal' => $r->sks_t,
                    'nilai_huruf_asal' => $r->huruf_t,
                    'nilai_huruf_diakui' => $r->huruf,
                    'nilai_indeks' => $nil->nilai_indeks,
                ];
                DB::table('nilai_transfer')->insert($data);
            });
        } catch (\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
        }

        // Feeder store
        // $this->feederStoreKonfersi($id);

        Rmt::success('Berhasil menyimpan data');
    }

    public function deleteNilaiTransfer($id)
    {

        // delete in feeder
        $konfersi = DB::table('nilai_transfer as tr')
            ->leftJoin('mahasiswa_reg as m1', 'tr.id_mhs_reg', 'm1.id')
            ->leftJoin('matakuliah as mk', 'mk.id', 'tr.id_mk')
            ->where('tr.id', $id)
            ->select('tr.*', 'mk.kode_mk', 'm1.nim')
            ->first();

        DB::table('nilai_transfer')->where('id', $id)->delete();

        // $this->feederDeleteKonfersi($konfersi);

        Rmt::success('Berhasil menghapus data');
        return redirect()->back();
    }
    /* End nilai transfer */

    /* Transkrip & Ijazah */
    public function transkrip(Request $r, $id)
    {
//        $mKur = MataKuliahKurikulum::all()->pluck('id_mk');
//        $mataKuliahDeleted = Matakuliah::whereNotIn('id',$mKur)->get()->pluck('kode_mk');
//        return $mataKuliahDeleted;
//        return $mataKuliahDeleted;
//         $nilaiMbkm = NilaiMbkm::with('mkKurikulum','mataKuliah')->where('id_mhs_reg',Session::get('id_regpd_in_transkrip'))
//         ->get();

         

        if (!empty($r->ubah_nim)) {
            $regpd = DB::table('mahasiswa_reg as m2')
                ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
                ->select('m2.id', 'm2.nim')
                ->where('m2.id', $r->ubah_nim)
                ->first();
            Session::put('id_regpd_in_transkrip', $regpd->id);
        }

        // Jika membuka mahasiswa yang lain unset session sebelumnya
        if ($id != Session::get('id_mhs_in_transkrip')) {
            Session::put('id_mhs_in_transkrip', $id);
            Session::put('id_regpd_in_transkrip', Sia::getRegpdPertama($id));
        }
        // echo Session::get('id_regpd_in_transkrip');exit;
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.id', 'm.nm_mhs', 'm2.id as id_reg_pd', 'm2.nim', 'm2.semester_mulai', 'm2.tgl_ijazah', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', Session::get('id_regpd_in_transkrip'))->first();

        $data['nim'] = DB::table('mahasiswa_reg')
            ->where('id_mhs', $id)
            ->select('id', 'nim')
            ->get();

        $data['krs'] = Sia::transkrip(Session::get('id_regpd_in_transkrip'));

        // for right menu
        $data['id_mahasiswa'] = $id;

//        return $data;
        return view('mahasiswa.transkrip', $data);
    }

    public function transkripCetak(Request $r, $id)
    {
        $idReg = Session::get('id_regpd_in_transkrip');

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select(
                'm.nm_mhs',
                'm.tempat_lahir',
                'm.tgl_lahir',
                'm2.*',
                'k.nm_konsentrasi',
                'p.nm_prodi',
                'p.jenjang',
                'smt.nm_smt',
                'smt.id_smt'
            )
            ->where('m2.id', $idReg)->first();
    

        if ($data['mhs']->id_prodi == 61101) {
            $data['krs'] = Sia::transkripS2($idReg);
        } else {
            $data['krs'] = Sia::transkrip($idReg);
        }
        


        // $data['nilai_mbkm'] = DB::table('nilai_mbkm')
        // ->leftJoin('matakuliah','matakuliah.id','nilai_mbkm.id_mk')
        // ->leftJoin('mk_kurikulum','mk_kurikulum.id_mk','nilai_mbkm.id_mk')
        // ->where('id_mhs_reg',$idReg)
        // ->get();

        $data['nilai'] = DB::table('nilai')->where('id_mhs_reg',$idReg)->get();
        
        // for right menu
        $data['id_mahasiswa'] = $id;
        
        
        if ($data['mhs']->id_prodi == 61101) {
            return view('mahasiswa.print-transkrip-s2', $data);
        } else {
            return view('mahasiswa.print-transkrip', $data);
        }
    }

    public function transkripSementaraCetak(Request $r, $id)
    {

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.nm_mhs', 'm.jenkel', 'm.tempat_lahir', 'm.tgl_lahir', 'm2.*', 'k.nm_konsentrasi', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', Session::get('id_regpd_in_transkrip'))->first();

        $data['krs'] = Sia::transkrip(Session::get('id_regpd_in_transkrip'));

        // for right menu
        $data['id_mahasiswa'] = $id;

        return view('mahasiswa.print-transkrip-sementara', $data);
    }

    public function ijazahCetak(Request $r, $id)
    {

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.nm_mhs', 'm.tempat_lahir', 'm.tgl_lahir', 'm.nik', 'm2.*', 'k.nm_konsentrasi', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', Session::get('id_regpd_in_transkrip'))->first();
        if ($data['mhs']->id_jenis_keluar != 1) {
            echo '<center>Mahasiswa ini belum lulus</center>';
            exit;
        }

        if ($data['mhs']->id_prodi == 61101) {

            if ($data['mhs']->bebas_pembayaran == 0) {
                echo 'Pembayaran belum selesai';
                exit;
            }
        } else {
            // dd($data['mhs']->tgl_keluar);
            // validasi cetak ijazah
            // if (  $data['mhs']->tgl_keluar > '2021-10-19' ) {

            //     $this->validasiCetakIjazah($data['mhs']->bebas_pembayaran, $data['mhs']->bebas_pustaka, $data['mhs']->bebas_skripsi);
            // }
        }

        // for right menu
        $data['id_mahasiswa'] = $id;

        DB::table('mahasiswa_reg')->where('id', $data['mhs']->id)
            ->update(['tgl_ijazah' => Carbon::parse($r->tgl_ijazah)->format('Y-m-d')]);

        if ($data['mhs']->id_prodi == 61101) {
            return view('mahasiswa.print-ijazah-s2', $data);
        } else {
            return view('mahasiswa.print-ijazah', $data);
        }
    }

    private function validasiCetakIjazah($pembayaran, $pustaka, $skripsi)
    {
        $validasi = [];
        if ($pembayaran == 0) {
            $validasi[] = 'Belum bebas pembayaran';
        }

        if ($pustaka == 0) {
            $validasi[] = 'Belum bebas pustaka';
        }

        if ($pustaka == 0) {
            $validasi[] = 'Belum bebas skripsi';
        }

        if (count($validasi) > 0) {
            echo '<center><h4>Ijazah belum bisa dicetak</h4>';
            foreach ($validasi as $key => $val) {
                echo $val . '<br>';
            }
            echo '<center>';

            exit;
        }
    }

    /* End Transkrip & Ijazah */

    /* Jadwal Kuliah */
    public function jadwalKuliah(Request $r, $id)
    {
        $id_reg_pd = Sia::getRegpdPertama($id);

        if (!empty($r->ubah_jenis)) {
            Session::put('jeniskrs_in_jdk', $r->ubah_jenis);
        } else {
            Session::put('jeniskrs_in_jdk', 1);
        }

        $query = Sia::jadwalKuliahMahasiswa($id_reg_pd, Session::get('jeniskrs_in_jdk'));
        $data['jadwal'] = $query->where('jdk.id_smt', Sia::sessionPeriode())
            ->get();

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.id', 'm.nm_mhs', 'm2.id as id_reg_pd', 'm2.nim', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', $id_reg_pd)->first();

        $data['id_mahasiswa'] = $id;
        
        return view('mahasiswa.jadwal-kuliah', $data);
    }
    /* End jadwal kuliah */

    /* Kartu mahasiswa */
    public function kartuMhsCrop(Request $r)
    {
        try {

            // if ( $r->id_prodi == '61101' ) {
            //     $targ_w = 236;
            //     $targ_h = 307;
            // } else {
            //     $targ_w = 76*3;
            //     $targ_h = 133*3;
            // }

            $targ_w = $r->w;
            $targ_h = $r->h;

            $nim = $r->nim;
            $rand = rand(00000, 99999);
            $nm_foto = $nim . '-' . $rand . '.jpg';

            $jpeg_quality = 100;
            $destination = storage_path('foto-mahasiswa/') . $nim . '-' . $rand . '.jpg';
            $destination2 = storage_path('foto-mahasiswa/thumb/') . $nim . '-' . $rand . '.jpg';

            $src = $r->pimg;

            $img_r = imagecreatefromjpeg($src);
            $dst_r = imagecreatetruecolor($targ_w, $targ_h);

            imagecopyresampled($dst_r, $img_r, 0, 0, $r->x, $r->y, $targ_w, $targ_h, $r->w, $r->h);

            header('Content-type: image/jpeg');
            imagejpeg($dst_r, $destination, $jpeg_quality);
            imagejpeg($dst_r, $destination2, $jpeg_quality);

            $mhs = Mahasiswareg::where('nim', $r->nim)->first();
            DB::table('mahasiswa')->where('id', $mhs->id_mhs)->update(['foto_mahasiswa' => $nm_foto]);
        } catch (\Exception $e) {

            return Response::json([$e->getMessage()], 422);
        }

        return Response::json(['error' => 0, 'nim' => $r->nim]);
    }

    public function kartuMhsPrev(Request $r)
    {

        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $lenNama = strlen(trim($mhs->mhs->nm_mhs));

        ?>
    <style>
      .kartu {
        position: relative;
        border-radius: 5px;
      }

      .kartu .bg {
        height: 55mm;
        width: 87.755mm;
      }

      .foto {
        position: absolute;
        left: 21px;
        top: 62px;
        border: 1px solid red;
        background-color: #eee;
        width: 20mm;
        height: 26mm;
      }

      .foto img {
        width: 19.5mm;
        height: 25.5mm;
      }

      .foto-s1 {
        position: absolute;
        left: 15px;
        bottom: 0px;
        width: 20mm;
        height: 40mm;
      }

      .foto-s1 img {
        width: 76px;
        height: 133px;
      }

      .konten {
        position: absolute;
        left: 118px;
        top: 59px;
        color: #000;
      }

      .konten-s1 {
        position: absolute;
        left: 130px;
        top: 50px;
        color: #000 !important;
      }

      <?php if ($lenNama > 22) {?>.konten .nama {
        font-size: 7px !important;
      }

      <?php }?>.logo {
        position: absolute;
        top: 9px;
        left: 90px;
        margin-right: 10px;
        width: 33px;
        float: left;
      }


      .footer {
        font-size: 9px;
        font-weight: bold;
      }

      <?php if ($mhs->jenjang == 'S1') {?>.kop {
        position: absolute;
        top: 7px;
        left: 140px;
        color: #000 !important;
        line-height: 1em;
      }

      .kop .inst {
        font-size: 10px;
      }

      .company {
        font-size: 16.8px;
        font-family: 'Calligraphic';
      }

      .jabatan {
        position: absolute;
        left: 210px;
        bottom: 60px;
      }

      .pimpinan {
        position: absolute;
        left: 165px;
        bottom: 17px;
      }

      <?php } else {?>.kop {
        position: absolute;
        top: 1px;
        left: 140px;
        color: #000 !important;
        line-height: 0.95em;
      }

      .kop .inst {
        font-size: 10px;
      }

      .company {
        font-size: 16.8px;
        font-family: 'Calligraphic';
      }

      .prodi {
        font-size: 12.5px;
        font-family: 'November';
      }

      .jabatan {
        position: absolute;
        left: 230px;
        bottom: 60px;
      }

      .pimpinan {
        position: absolute;
        left: 208px;
        bottom: 20px;
      }

      <?php }?>
    </style>

    <div class="kartu">


<!--      --><?php //if ($mhs->id_prodi != '61101') {?>
<!--        <img class="bg" src="--><?php //=url('storage')?><!--/kartu-mhs/ktm-s1-2.jpg">-->
<!--      --><?php //} else {?>
        <img class="bg" src="<?=url('resources')?>/assets/img/ktm/new-ktm-s2.jpg">
<!--      --><?php //}?>

<!--      --><?php //if (!empty($mhs->mhs->foto_mahasiswa)) {?>
<!--        --><?php //if ($mhs->id_prodi != '61101') {?>
          <div class="foto-s1">
            <?php if($mhs->mhs->foto_mahasiswa){ ?>
                <img src="<?=url('storage')?>/foto-mahasiswa/<?=$mhs->mhs->foto_mahasiswa?>">
             <?php } ?>
          </div>
<!--        --><?php //} else {?>
<!--          <div class="foto">-->
<!--            <img src="--><?php //=url('storage')?><!--/foto-mahasiswa/--><?php //=$mhs->mhs->foto_mahasiswa?><!--">-->
<!--          </div>-->
<!--        --><?php //}?>
<!--      --><?php //}?>

        <div class="konten">
          <table border="0" style="font-size: 9px !important;margin-top:20px;margin-left: 10px">
            <tr>
              <td>NIM</td>
              <td width="10" align="center"> : </td>
              <td><?=$mhs->nim?></td>
            </tr>
            <tr>
              <td>Nama</td>
              <td align="center"> : </td>
              <td class="nama"><?=$mhs->mhs->nm_mhs?></td>
            </tr>
            <tr>
              <td>Program Studi</td>
              <td align="center"> : </td>
              <td><?=$mhs->id_prodi == '61101' ? 'Magister Manajemen' : $mhs->prodi->jenjang . ' ' . $mhs->prodi->nm_prodi?></td>
            </tr>
            <tr>
              <td>Tempat Lahir</td>
              <td align="center"> : </td>
              <td><?=$mhs->mhs->tempat_lahir?></td>
            </tr>
            <tr>
              <td>Tanggal Lahir</td>
              <td align="center"> : </td>
              <td><?=Rmt::tgl_indo($mhs->mhs->tgl_lahir)?></td>
            </tr>
          </table>
          </div>
        </div>
        <hr>
      <?php

    }

    public function kartuMhsCetak(Request $r)
    {
        $data['mhs'] = Mahasiswareg::where('nim', $r->nim)->first();

        $data['lenNama'] = strlen(trim($data['mhs']->mhs->nm_mhs));

        return view('mahasiswa.kartu-mhs-cetak', $data);
    }

    public function kartuMhsCetakSisiDepan(Request $r)
    {
        return view('mahasiswa.kartu-mhs-cetak-depan');
    }

    /* end kartu mahasiswa */

    public function getPmb(Request $req)
    {
        try {
            $periode = Sia::sessionPeriode();
            $prodi = Sia::getProdiUser();
            $tahun = substr($periode, 0, 4) + 1;
            $sudah_masuk = DB::table('mahasiswa_reg')
                ->where('semester_mulai', $periode)
                ->whereIn('id_prodi', $prodi)
                ->where('id_maba', '<>', '')
                ->whereNotNull('id_maba')
                ->pluck('id_maba');

            $maba = DB::connection('pmb')
                ->table('nbl_pendaftar')
                ->where('ta', $tahun)
                ->whereNotIn('id', $sudah_masuk)
                ->whereNotNull('username')
                ->orderBy('username')
                ->get();
        } catch (\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 400);
        }?>

        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
          <thead class="custom">
            <tr>
              <th width="20px">No.</th>
              <th>No Registrasi</th>
              <th>Nama</th>
              <th>Tahun Akademik</th>
              <th>Prodi</th>
              <th width="80">Aksi</th>
            </tr>
          </thead>
          <tbody align="center">
            <?php $no = 1?>
            <?php foreach ($maba as $r) {?>
              <?php $prodi = Sia::prodiFirst($r->prodi)?>
              <?php
$agama = DB::table('agama')
                ->where('nm_agama', 'like', '%' . $r->agama . '%')
                ->first();
            if (!empty($agama)) {
                $id_agama = $agama->id_agama;
            } else {
                $id_agama = 99;
            }
            ?>
              <tr>
                <td><?=$no++?></td>
                <td width="100"><?=$r->username?></td>
                <td align="left"><?=$r->nama?></td>
                <td><?=$periode?></td>
                <td><?=$prodi->nm_prodi . ' - ' . $prodi->jenjang?></td>
                <td>
                  <a href="javascript:;" class="btn btn-primary btn-xs ambil" data-id="<?=$r->id?>" data-nama="<?=ucfirst(strtolower($r->nama))?>" data-prodi="<?=$r->prodi?>" data-ktp="<?=$r->ktp?>" data-alamat="<?=$r->alamat?>" data-provinsi="<?=$r->provinsi?>" data-kota="<?=$r->kota?>" data-kecamatan="<?=$r->kecamatan?>" data-kelurahan="<?=$r->kelurahan?>" data-hp="<?=$r->hp?>" data-tempat_lahir="<?=$r->tempat_lahir?>" data-tgl_lahir="<?=Carbon::parse($r->tgl_lahir)->format('d-m-Y')?>" data-jenkel="<?=trim($r->jenkel) == 'Perempuan' ? 'P' : 'L'?>" data-agama="<?=$id_agama?>" data-jenis_tinggal="<?=$r->jenis_tinggal == 6 ? 99 : $r->jenis_tinggal?>" data-referensi_by="<?=$r->referensi_by?>" data-prodi="<?=$r->prodi?>" data-konsentrasi="<?=$r->konsentrasi?>" data-slta="<?=$r->slta?>" data-alamat_slta="<?=$r->alamat_slta?>" data-jurusan_slta="<?=$r->jurusan_slta?>" data-tahun_lulus="<?=$r->tahun_lulus?>" data-nem="<?=$r->nem?>" data-sttb="<?=$r->sttb?>" data-ayah="<?=$r->ayah?>" data-hp_ayah="<?=$r->hp_ayah?>" data-pekerjaan_ayah="<?=$r->pekerjaan_ayah?>" data-nik_ibu="<?=$r->nik_ibu?>" data-ibu="<?=$r->ibu?>" data-hp_ibu="<?=$r->hp_ibu?>" data-pekerjaan_ibu="<?=$r->pekerjaan_ibu?>" data-penghasilan_ortu="<?=$r->penghasilan_ortu?>" data-nik_ayah="<?=$r->nik_ayah?>" data-info_nobel="<?=$r->info_nobel?>" data-tgl_daftar="<?=Carbon::now()->format('d-m-Y')?>">
                    Ambil
                  </a>
                </td>
              </tr>
            <?php }?>
          </tbody>
        </table>
        <script type="text/javascript" src="<?=url('resources')?>/assets/plugins/datable/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?=url('resources')?>/assets/plugins/datable/dataTables.bootstrap.js"></script>
        <script>
          $(function() {
            $('table[data-provide="data-table"]').dataTable();
          });
        </script>
    <?php
}

    public function pasangPin(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
        ]);

        if (!is_dir(storage_path() . '/tmp')) {
            mkdir(storage_path() . '/tmp');
        }

        $nama_file = $request->file('file')->getClientOriginalName();
        $request->file('file')->move(storage_path() . '/tmp', $nama_file);

        try {

            Excel::load(storage_path() . '/tmp/' . $nama_file, function ($reader) use (&$errors, &$data, &$sukses) {
                $results = $reader->get();

                $errors = [];
                $sukses = 0;

                DB::transaction(function () use ($results, &$errors, &$sukses) {

                    foreach ($results as $r) {
                        $mhs = Mahasiswareg::where('nim', trim($r->nim))->first();

                        if (empty($mhs)) {

                            $errors[] = $r->nim . ' tidak ditemukan di siakad';
                        } else {

                            $mhs = Mahasiswareg::find($mhs->id);
                            $mhs->pin = trim($r->pin);
                            $mhs->kode_batch_pin = trim($r->kode_batch);
                            $mhs->save();

                            $sukses++;
                        }
                    }
                });
            });
        } catch (\Exception $e) {
            $response = ['error' => 1, 'msg' => $e->getMessage()];
        }

        if (file_exists(storage_path() . '/tmp/' . $nama_file)) {
            unlink(storage_path() . '/tmp/' . $nama_file);
        }

        $response = ['error' => 0, 'msg' => ''];

        if (count($errors) > 0) {
            Session::flash('errors_impor', $errors);
        }

        Rmt::success($sukses == 0 ? 'Tidak ada data dimasukkan' : $sukses . ' data PIN mahasiswa dimasukkan');
        return Response::json($response, 200);
    }

    /* Jurnal mahasiswa */
    public function jurnal(Request $r, $id)
    {

        Session::put('jurnal_id_mhs', Sia::getRegpdPertama($id));

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m', 'm.id', '=', 'm2.id_mhs')
            ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
            ->leftJoin('semester as smt', 'm2.semester_mulai', '=', 'smt.id_smt')
            ->select('m.id', 'm.nm_mhs', 'm2.jurnal_file', 'm2.jurnal_approved', 'm2.pesan_revisi', 'm2.updated_jurnal', 'm2.jurnal_published', 'm2.id as id_reg_pd', 'm2.nim', 'm2.id_prodi', 'p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
            ->where('m2.id', Session::get('jurnal_id_mhs'))->first();

        $data['nim'] = DB::table('mahasiswa_reg')
            ->where('id_mhs', $id)
            ->select('id', 'nim')
            ->get();

        if (!empty($r->ubah_jenis)) {
            Session::put('krs_jeniskrs', $r->ubah_jenis);
        } else {
            Session::put('krs_jeniskrs', 1);
        }

        // for right menu
        $data['id_mahasiswa'] = $id;

        return view('mahasiswa.jurnal', $data);
    }

    public function jurnalDownload(Request $r)
    {
        $path = config('app.jurnaldir');
        $pathToFile = $path . '/' . $r->file;

        if (file_exists($pathToFile)) {
            return Response::file($pathToFile);
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }
    }

    public function jurnalStore(Request $r)
    {

        if ($r->hasFile('file')) {

            try {

                $ekstArr = ['docx', 'doc'];
                $ekstensi = $r->file->getClientOriginalExtension();
                $path = config('app.jurnaldir');

                if (!in_array($ekstensi, $ekstArr)) {
                    return Response::json(['error' => 1, 'msg' => 'Jenis file yang diperbolehkan adalah ' . implode(',', $ekstArr)]);
                }

                $fileName = 'jurnal-' . $r->nim . '.' . strtolower($ekstensi);
                $upload = $r->file->move($path, $fileName);

                $mhs = Mahasiswareg::find($r->id_mhs_reg);
                $mhs->jurnal_file = $fileName;
                $mhs->updated_jurnal = Carbon::now()->format('Y-m-d H:i:s');
                $mhs->save();

                $this->sendMailUploadJurnal($r->nm_mhs, $r->nim, $r->id_mhs);

                Rmt::success('Berhasil menyimpan jurnal');
            } catch (\Exception $e) {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
            }
        } else {
            return Response::json(['error' => 1, 'msg' => 'Belum ada file']);
        }
    }

    public function jurnalFileDelete(Request $r)
    {

        try {

            $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);

            $path = config('app.jurnaldir');
            $file = $path . '/' . $mhs->jurnal_file;
            if (file_exists($file)) {
                unlink($file);
            }

            $mhs->jurnal_file = '';
            $mhs->save();

            Rmt::success('Berhasil menghapus jurnal');
        } catch (\Exception $e) {
            Rmt::error($e->getMessage());
            return redirect()->back();
        }

        return redirect()->back();
    }

    public function jurnalPublish(Request $r)
    {
        try {

            if ($r->jenis == 'approval') {

                $mhs = Mahasiswareg::find($r->id_mhs_reg);
                $mhs->pesan_revisi = null;
                $mhs->jurnal_approved = $r->approve;
                $mhs->save();

                $mhs = Mahasiswareg::find($r->id_mhs_reg);

                $this->sendMailApprovalJurnal($mhs);
            } else {

                if (empty($mhs->jurnal_approved)) {
                    return Response::json('Jurnal belum disetujui, silahkan lakukan Approval dahulu pada jurnalnya', 422);
                }

                $mhs->jurnal_published = $r->publish;
                $mhs->save();
            }
        } catch (\Exception $e) {
            return Response::json($e->getMessage() . '. Coba muat ulang halaman dan ulangi lagi', 422);
        }
    }

    public function updateBebasPembayaran(Request $r)
    {
        $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);
        $mhs->bebas_pembayaran = $r->value;
        $mhs->save();

        Session::flash('bebas', 'Berhasil mengupdate data');
        return redirect()->back();
    }

    public function updateBebasPustaka(Request $r)
    {
        $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);
        $mhs->bebas_pustaka = $r->value;
        $mhs->save();

        Session::flash('bebas', 'Berhasil mengupdate data');
        return redirect()->back();
    }

    public function updateBebasSkripsi(Request $r)
    {
        $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);
        $mhs->bebas_skripsi = $r->value;
        $mhs->save();

        Session::flash('bebas', 'Berhasil mengupdate data');
        return redirect()->back();
    }

    private function sendMailUploadJurnal($nm_mhs, $nim, $id_mhs)
    {
        try {

            $data['link'] = 'http://siakad.stienobel-indonesia.ac.id/mahasiswa/jurnal/' . $id_mhs;
            $mail['subjek'] = 'Jurnal baru telah diupload';
            $mail['email'] = 'fitri.nobel89@gmail.com';

            $data['header'] = $nm_mhs . ' - ' . $nim . ' Baru saja mengupload jurnal.';

            Mail::send('email.pemberitahuan-jurnal', $data, function ($message) use ($mail) {
                $message->from('nobel@stienobel-indonesia.ac.id', 'STIE Nobel Indonesia');
                $message->to($mail['email']);
                $message->subject($mail['subjek']);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function storeRevisiJurnal(Request $r)
    {
        try {
            // $data = Mahasiswareg::findOrFail($r->id_mhs_reg);

            DB::table('mahasiswa_reg')->where('id', $r->id_mhs_reg)
                ->update(['pesan_revisi' => $r->pesan]);

            return Response::json(['error' => 0], 200);
        } catch (\Exception $e) {
            return Response::json(['Gagal menyimpan: ' . $e->getMessage()], 422);
        }
    }

    public function sendMailRevisi(Request $r)
    {
        try {
            $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);

            $mail['subjek'] = 'Pemberitahuan Revisi Jurnal';
            $mail['email'] = $mhs->mhs->email;

            $data['header'] = 'Hai ' . $r->nm_mhs . ',';
            $data['msg'] = $mhs->pesan_revisi;

            Mail::send('email.pemberitahuan-revisi-jurnal', $data, function ($message) use ($mail) {
                $message->from('nobel@stienobel-indonesia.ac.id', 'STIE Nobel Indonesia');
                $message->to($mail['email']);
                $message->subject($mail['subjek']);
            });

            return Response::json(['error' => 0], 200);
        } catch (\Exception $e) {
            return Response::json(['Gagal mengirim email: ' . $e->getMessage()], 422);
        }
    }

    private function sendMailApprovalJurnal($mhs)
    {
        try {

            $mail['subjek'] = 'Jurnal telah disetujui';
            $mail['email'] = $mhs->mhs->email;

            $data['header'] = 'Hai ' . $mhs->mhs->nm_mhs . ',';

            Mail::send('email.pemberitahuan-approval-jurnal', $data, function ($message) use ($mail) {
                $message->from('nobel@stienobel-indonesia.ac.id', 'STIE Nobel Indonesia');
                $message->to($mail['email']);
                $message->subject($mail['subjek']);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /* FEEDER */
    public function feederStore($id_mhs)
    {
        $mhs = Mahasiswa::findOrFail($id_mhs);
        $riwayat = Mahasiswareg::where('id_mhs', $id_mhs)->firstOrFail();

        // Cek mahasiswa
        $filter = "nama_mahasiswa = '" . $mhs->nm_mhs . "' and tanggal_lahir = '" . $mhs->tgl_lahir->format('Y-m-d') . "' and nama_ibu = '" . $mhs->nm_ibu . "'";
        $dt_cek_mhs = [
            'act' => 'GetBiodataMahasiswa',
            'filter' => $filter,
        ];

        $res_cek_mhs = Feeder::runWs($dt_cek_mhs);

        if (empty($res_cek_mhs->error_desc) and !empty($res_cek_mhs->data)) {

            Mahasiswareg::where('id', $riwayat->id)->update(['feeder_status' => 1, 'feeder_ket' => '']);
            // dd('Mahasiswa ini telah ada');

        } elseif (empty($res_cek_mhs->error_desc) and empty($res_cek_mhs->data)) {
            // Mahasiswa tidak ditemukan

            $data = [
                'act' => 'InsertBiodataMahasiswa',
                'record' => [
                    'nama_mahasiswa' => $mhs->nm_mhs,
                    'jenis_kelamin' => $mhs->jenkel,
                    'tempat_lahir' => $mhs->tempat_lahir,
                    'tanggal_lahir' => $mhs->tgl_lahir->format('Y-m-d'),
                    'id_agama' => $mhs->id_agama,
                    'nik' => $mhs->nik,
                    'kewarganegaraan' => $mhs->kewarganegaraan,
                    'jalan' => $mhs->alamat,
                    'rt' => $mhs->rt,
                    'rw' => $mhs->rw,
                    'dusun' => $mhs->dusun,
                    'kelurahan' => $mhs->des_kel,
                    'id_wilayah' => $mhs->id_wil,
                    'kode_pos' => $mhs->kode_pos,
                    'nisn' => $mhs->nisn,
                    'nama_ayah' => $mhs->nm_ayah,
                    'tanggal_lahir_ayah' => !empty($mhs->tgl_lahir_ayah) ? $mhs->tgl_lahir_ayah->format('Y-m-d') : '',
                    'nik_ayah' => $mhs->nik_ayah,
                    // 'id_jenjang_pendidikan_ayah' => $mhs->id_pdk_ayah,
                    'id_pekerjaan_ayah' => $mhs->id_pekerjaan_ayah,
                    'id_penghasilan_ayah' => $mhs->id_penghasilan_ayah,
                    'id_kebutuhan_khusus_ayah' => 0,
                    'nama_ibu_kandung' => $mhs->nm_ibu,
                    'tanggal_lahir_ibu' => !empty($mhs->tgl_lahir_ibu) ? $mhs->tgl_lahir_ibu->format('Y-m-d') : '',
                    'nik_ibu' => $mhs->nik_ibu,
                    // 'id_jenjang_pendidikan_ibu' => $mhs->id_pdk_ibu,
                    'id_pekerjaan_ibu' => $mhs->id_pekerjaan_ibu,
                    'id_penghasilan_ibu' => $mhs->id_penghasilan_ibu,
                    'id_kebutuhan_khusus_ibu' => 0,
                    'id_kebutuhan_khusus_mahasiswa' => 0,
                    'nama_wali' => $mhs->nm_wali,
                    'tanggal_lahir_wali' => !empty($mhs->tgl_lahir_wali) ? $mhs->tgl_lahir_wali->format('Y-m-d') : '',
                    // 'id_jenjang_pendidikan_wali' => $mhs->id_pdk_wali,
                    'id_pekerjaan_wali' => $mhs->id_pekerjaan_wali,
                    'id_penghasilan_wali' => $mhs->id_penghasilan_wali,
                    'handphone' => $mhs->hp,
                    'email' => $mhs->email,
                    'penerima_kps' => 0,
                    'npwp' => $mhs->npwp,
                    'id_jenis_tinggal' => $mhs->id_jenis_tinggal,
                    'id_alat_transportasi' => $mhs->id_alat_transpor,
                ],
            ];

            $res_insert_mhs = Feeder::runWs($data);

            if (!empty($res_insert_mhs) && $res_insert_mhs->error_code == 0) {

                $sks_diakui = 0;

                if (in_array($riwayat->jenis_daftar, [2, 11])) {
                    $sks_diakui = DB::table('nilai_transfer as tr')
                        ->join('matakuliah as mk', 'tr.id_mk', 'mk.id')
                        ->where('id_mhs_reg', $riwayat->id)
                        ->sum('mk.sks_mk');
                }

                // Insert riwayat pendidikan
                $id_mhs = $res_insert_mhs->data->id_mahasiswa;
                $data2 = [
                    'act' => 'InsertRiwayatPendidikanMahasiswa',
                    'record' => [
                        'id_mahasiswa' => $id_mhs,
                        'nim' => $riwayat->nim,
                        'id_jenis_daftar' => $riwayat->jenis_daftar,
                        'id_jalur_daftar' => $riwayat->jalur_masuk,
                        'id_periode_masuk' => $riwayat->semester_mulai,
                        'tanggal_daftar' => $riwayat->tgl_daftar,
                        'id_perguruan_tinggi' => Config::get('app.feeder_id_pt'),
                        'id_prodi' => Feeder::idProdi($riwayat->id_prodi),
                        'sks_diakui' => $sks_diakui,
                        'id_perguruan_tinggi_asal' => $riwayat->id_pt_asal,
                        'id_prodi_asal' => $riwayat->id_prodi_asal,
                        // 'id_pembiayaan' => $riwayat->id_jenis_pembiayaan
                        'id_pembiayaan' => '1',
                        'biaya_masuk' => empty($riwayat->biaya_masuk) ? 450000 : $riwayat->biaya_masuk,
                    ],
                ];

                $insert_pdk = Feeder::runWs($data2);

                if (!empty($insert_pdk) && $insert_pdk->error_code == 0) {

                    Mahasiswareg::where('id', $riwayat->id)->update(['feeder_status' => 1, 'feeder_ket' => '']);
                    // dd('Berhasil mengupload data');

                } else {

                    // hapus biodata mahasiswa
                    $data_delete = [
                        'act' => 'DeleteBiodataMahasiswa',
                        'key' => [
                            'id_mahasiswa' => $id_mhs,
                        ],
                    ];
                    Feeder::runWs($data_delete);

                    if (!empty($insert_pdk)) {
                        Mahasiswareg::where('id', $riwayat->id)->update(['feeder_status' => 2, 'feeder_ket' => $insert_pdk->error_desc]);
                        // return false;
                    }
                }
            } else {

                if (!empty($res_insert_mhs)) {
                    Mahasiswareg::where('id', $riwayat->id)->update(['feeder_status' => 2, 'feeder_ket' => 'Error in biodata. ' . $res_insert_mhs->error_desc]);
                    // return false;
                }
            }
        } else {
            // Error
            // return false;
            // dd($res_cek_mhs->error_desc);
        }
    }

    public function feederStoreRegpd($id_mhs_reg)
    {
        $riwayat = Mahasiswareg::findOrFail($id_mhs_reg);
        $mhs = Mahasiswa::findOrFail($riwayat->id_mhs);

        // Cek mahasiswa
        $filter = "nama_mahasiswa = '" . $mhs->nm_mhs . "' and tanggal_lahir = '" . $mhs->tgl_lahir->format('Y-m-d') . "' and nama_ibu = '" . $mhs->nm_ibu . "'";
        $dt_cek_mhs = [
            'act' => 'GetBiodataMahasiswa',
            'filter' => $filter,
        ];

        $res_cek_mhs = Feeder::runWs($dt_cek_mhs);

        if (empty($res_cek_mhs->error_desc) and !empty($res_cek_mhs->data)) {
            $id_mhs_fdr = $res_cek_mhs->data[0]->id_mahasiswa;

            $data = [
                'act' => 'InsertRiwayatPendidikanMahasiswa',
                'record' => [
                    'id_mahasiswa' => $id_mhs_fdr,
                    'nim' => $riwayat->nim,
                    'id_jenis_daftar' => $riwayat->jenis_daftar,
                    'id_jalur_daftar' => $riwayat->jalur_masuk,
                    'id_periode_masuk' => $riwayat->semester_mulai,
                    'tanggal_daftar' => $riwayat->tgl_daftar,
                    'id_perguruan_tinggi' => Config::get('app.feeder_id_pt'),
                    'id_prodi' => Feeder::idProdi($riwayat->id_prodi),
                    'sks_diakui' => $sks_diakui,
                    'id_perguruan_tinggi_asal' => $riwayat->id_pt_asal,
                    'id_prodi_asal' => $riwayat->id_prodi_asal,
                    'id_pembiayaan' => '1',
                    'biaya_masuk' => empty($riwayat->biaya_masuk) ? 450000 : $riwayat->biaya_masuk,
                ],
            ];

            $insert_pdk = Feeder::runWs($data2);

            if (!empty($insert_pdk) && $insert_pdk->error_code == 0) {

                Mahasiswareg::where('id', $riwayat->id)->update(['feeder_status' => 1, 'feeder_ket' => '']);
                // dd('Berhasil mengupload data');
                // return true;

            }
        }
    }

    public function feederUpdateMhs($id_mhs)
    {
        $mhs = Mahasiswa::findOrFail($id_mhs);

        $data = [
            'act' => 'UpdateBiodataMahasiswa',
            'key' => [
                'id_mahasiswa' => $mhs->id,
            ],
            'record' => [
                'nama_mahasiswa' => $mhs->nm_mhs,
                'jenis_kelamin' => $mhs->jenkel,
                'tempat_lahir' => $mhs->tempat_lahir,
                'tanggal_lahir' => $mhs->tgl_lahir->format('Y-m-d'),
                'id_agama' => $mhs->id_agama,
                'nik' => $mhs->nik,
                'kewarganegaraan' => $mhs->kewarganegaraan,
                'jalan' => $mhs->alamat,
                'rt' => $mhs->rt,
                'rw' => $mhs->rw,
                'dusun' => $mhs->dusun,
                'kelurahan' => $mhs->des_kel,
                'id_wilayah' => $mhs->id_wil,
                'kode_pos' => $mhs->kode_pos,
                'nisn' => $mhs->nisn,
                'nama_ayah' => $mhs->nm_ayah,
                'tanggal_lahir_ayah' => !empty($mhs->tgl_lahir_ayah) ? $mhs->tgl_lahir_ayah->format('Y-m-d') : '',
                'nik_ayah' => $mhs->nik_ayah,
                'id_pekerjaan_ayah' => $mhs->id_pekerjaan_ayah,
                'id_penghasilan_ayah' => $mhs->id_penghasilan_ayah,
                'id_kebutuhan_khusus_ayah' => 0,
                'nama_ibu_kandung' => $mhs->nm_ibu,
                'tanggal_lahir_ibu' => !empty($mhs->tgl_lahir_ibu) ? $mhs->tgl_lahir_ibu->format('Y-m-d') : '',
                'nik_ibu' => $mhs->nik_ibu,
                'id_pekerjaan_ibu' => $mhs->id_pekerjaan_ibu,
                'id_penghasilan_ibu' => $mhs->id_penghasilan_ibu,
                'id_kebutuhan_khusus_ibu' => 0,
                'id_kebutuhan_khusus_mahasiswa' => 0,
                'nama_wali' => $mhs->nm_wali,
                'tanggal_lahir_wali' => !empty($mhs->tgl_lahir_wali) ? $mhs->tgl_lahir_wali->format('Y-m-d') : '',
                'id_pekerjaan_wali' => $mhs->id_pekerjaan_wali,
                'id_penghasilan_wali' => $mhs->id_penghasilan_wali,
                'handphone' => $mhs->hp,
                'email' => $mhs->email,
                'penerima_kps' => 0,
                'npwp' => $mhs->npwp,
                'id_jenis_tinggal' => $mhs->id_jenis_tinggal,
                'id_alat_transportasi' => $mhs->id_alat_transpor,
            ],
        ];

        $res_insert_mhs = Feeder::runWs($data);

        if (!empty($res_insert_mhs) && $res_insert_mhs->error_code == 0) {
            // sukses
        }
    }

    public function feederUpdateRegpd($id_mhs_reg)
    {
        $riwayat = Mahasiswareg::findOrFail($id_mhs_reg);

        $data = [
            'act' => 'GetListRiwayatPendidikanMahasiswa',
            'filter' => "nim='" . $riwayat->nim . "'",
        ];

        $data_cek_mhs = Feeder::runWs($data);

        if (!empty($data_cek_mhs) && $data_cek_mhs->error_code == 0) {
            // eksis
            $id_regpd = $data_cek_mhs->data[0]->id_registrasi_mahasiswa;

            $data2 = [
                'act' => 'UpdateRiwayatPendidikanMahasiswa',
                'record' => [
                    'id_jenis_daftar' => $riwayat->jenis_daftar,
                    'id_jalur_daftar' => $riwayat->jalur_masuk,
                    'id_periode_masuk' => $riwayat->semester_mulai,
                    'tanggal_daftar' => $riwayat->tgl_daftar,
                    'id_perguruan_tinggi' => Config::get('app.feeder_id_pt'),
                    'id_prodi' => Feeder::idProdi($riwayat->id_prodi),
                    'sks_diakui' => $sks_diakui,
                    'id_perguruan_tinggi_asal' => $riwayat->id_pt_asal,
                    'id_prodi_asal' => $riwayat->id_prodi_asal,
                    'id_pembiayaan' => '1',
                    'biaya_masuk' => empty($riwayat->biaya_masuk) ? 450000 : $riwayat->biaya_masuk,
                ],
            ];

            $update_pdk = Feeder::runWs($data2);

            if (!empty($update_pdk) && $update_pdk->error_code == 0) {
                // Sukses
            }
        }
    }

    public function feederStoreKonfersi($id_nilai_transfer)
    {

        $konfersi = DB::table('nilai_transfer as n')
            ->join('matakuliah as mk', 'mk.id', 'n.id_mk')
            ->select('n.*', 'kode_mk', 'nm_mk', 'sks_mk')
            ->where('n.id', $id_nilai_transfer)
            ->first();

        if (!empty($konfersi)) {

            $riwayat = Mahasiswareg::findOrFail($konfersi->id_mhs_reg);

            $data = [
                'act' => 'GetListRiwayatPendidikanMahasiswa',
                'filter' => "nim='" . $riwayat->nim . "'",
            ];

            $data_cek_mhs = Feeder::runWs($data);

            if (!empty($data_cek_mhs) && $data_cek_mhs->error_code == 0) {
                // eksis
                $id_regpd = $data_cek_mhs->data[0]->id_registrasi_mahasiswa;

                // Get Matakuliah
                $data_mk = [
                    'act' => 'GetListMataKuliah',
                    'filter' => "kode_mata_kuliah='" . $konfersi->kode_mk . "'",
                    'limit' => 1,
                ];

                $res_mk = Feeder::runWs($data_mk);

                if (!empty($res_mk) && $res_mk->error_code == 0) {

                    $id_mk = $res_mk->data[0]->id_matkul;

                    $data2 = [
                        'act' => 'InsertNilaiTransferPendidikanMahasiswa',
                        'record' => [
                            "id_registrasi_mahasiswa" => $id_regpd,
                            "id_matkul" => $id_mk,
                            "kode_mata_kuliah_asal" => $konfersi->kode_mk_asal,
                            "nama_mata_kuliah_asal" => $konfersi->nm_mk_asal,
                            "sks_mata_kuliah_asal" => $konfersi->sks_asal,
                            "sks_mata_kuliah_diakui" => $konfersi->sks_mk,
                            "nilai_huruf_asal" => $konfersi->nilai_huruf_asal,
                            "nilai_huruf_diakui" => $konfersi->nilai_huruf_diakui,
                            "nilai_angka_diakui" => $konfersi->nilai_indeks,
                        ],
                    ];

                    $res = Feeder::runWs($data2);
                } else {
                    DB::table('nilai_transfer')->where('id', $id_nilai_transfer)
                        ->update(['feeder_status' => '2', 'feeder_ket' => 'Matakuliah tidak ditemukan']);
                }
            } else {
                DB::table('nilai_transfer')->where('id', $id_nilai_transfer)
                    ->update(['feeder_status' => '2', 'feeder_ket' => 'Mahasiswa tidak ditemukan']);
            }
        }
    }

    public function feederDeleteKonfersi($konfersi)
    {
        // Get id reg pd
        $data_mhs = [
            'act' => 'GetListRiwayatPendidikanMahasiswa',
            'filter' => "nim='" . $konfersi->nim . "'",
        ];

        $data_cek_mhs = Feeder::runWs($data_mhs);

        if (!empty($data_cek_mhs) && $data_cek_mhs->error_code == 0) {
            // eksis
            $id_regpd = $data_cek_mhs->data[0]->id_registrasi_mahasiswa;

            $data_mk = [
                'act' => 'GetListMataKuliah',
                'filter' => "kode_mata_kuliah='" . $konfersi->kode_mk . "'",
                'limit' => 1,
            ];
            // dd($data_mk);
            $res_mk = Feeder::runWs($data_mk);

            if (!empty($res_mk) && $res_mk->error_code == 0) {
                $id_mk = $res_mk->data[0]->id_matkul;

                $data = [
                    'act' => 'DeleteNilaiTransferPendidikanMahasiswa',
                    'key' => [
                        'id_registrasi_mahasiswa' => $id_regpd,
                        'id_matkul' => $id_mk,
                    ],
                ];

                $data_del = Feeder::runWs($data);
            }
        }
    }

    public function reLogin($id_user)
    {
        Session::put('current_admin', Auth::user()->id);
        Session::put('switch_from', 'mahasiswa');
        $user = User::find($id_user);
        Auth::login($user);
        Session::pull('periode_aktif');
        return redirect(url('/beranda'));
    }

    public function mhsKIP()
    {
        $data = DB::table('mahasiswa')
            ->join('mahasiswa_reg', 'mahasiswa_reg.id_mhs', '=', 'mahasiswa.id')
            ->where('mahasiswa.jns_beasiswa', '!=', 1)
            ->where('mahasiswa.jns_beasiswa', '!=', 0)
            ->get();

        return view('mahasiswa-kip.index', compact('data'));
    }

    public function cetakMhsKip()
    {
        $data = DB::table('mahasiswa')
            ->join('mahasiswa_reg', 'mahasiswa_reg.id_mhs', '=', 'mahasiswa.id')
            ->where('mahasiswa.jns_beasiswa', '!=', 1)
            ->where('mahasiswa.jns_beasiswa', '!=', 0)
            ->get();
    }

    public function importWilayah()
    {

        // $req = Http::post('http://192.168.6.10:8100/ws/live2.php', [
        //     "act" => "GetWilayah",
        //     "token" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF9wZW5nZ3VuYSI6ImNjYWE4ZGIxLTQyZDQtNGYzNy04OTJlLTk2M2U2ZDI0MmQ4ZCIsInVzZXJuYW1lIjoiaXFiYWwuMDkyMDIxIiwibm1fcGVuZ2d1bmEiOiJJcWJhbCBBemlzIiwidGVtcGF0X2xhaGlyIjoiUGFyZS1wYXJlIiwidGdsX2xhaGlyIjoiMTk4MC0wNi0yNFQxNjozMDowMC4wMDBaIiwiamVuaXNfa2VsYW1pbiI6IkwiLCJhbGFtYXQiOiJKbC4gTWFjY2luaSBHdXN1bmcgTm8uIDY1IiwieW0iOiJpcWJhbEBub2JlbC5hYy5pZCIsInNreXBlIjoiIiwibm9fdGVsIjoiIiwiYXBwcm92YWxfcGVuZ2d1bmEiOiIxIiwiYV9ha3RpZiI6IjEiLCJ0Z2xfZ2FudGlfcHdkIjpudWxsLCJpZF9zZG1fcGVuZ2d1bmEiOm51bGwsImlkX3BkX3BlbmdndW5hIjpudWxsLCJpZF93aWwiOiIxOTYwMDAgICIsImxhc3RfdXBkYXRlIjoiMjAyMS0wOS0yM1QwMzoxNTowOS43NzdaIiwic29mdF9kZWxldGUiOiIwIiwibGFzdF9zeW5jIjoiMjAyMy0wNS0wOVQxMjowMzo0MS41NTNaIiwiaWRfdXBkYXRlciI6ImNjYWE4ZGIxLTQyZDQtNGYzNy04OTJlLTk2M2U2ZDI0MmQ4ZCIsImNzZiI6IjAiLCJ0b2tlbl9yZWciOm51bGwsImphYmF0YW4iOm51bGwsInRnbF9jcmVhdGUiOiIyMDIxLTA5LTIyVDA2OjAyOjUzLjkyMFoiLCJpZF9wZXJhbiI6Mywibm1fcGVyYW4iOiJBZG1pbiBQVCIsImlkX3NwIjoiMjAyZDhjNzAtYWZjMS00OWY1LWI3MzUtYTMxZTMxM2ZmYjYyIiwiaWF0IjoxNjg1MDA0Mjk5LCJleHAiOjE2ODUwMDYwOTl9.8a7QWn5ziYWHJeTPmLlB1t5mQjSVVbktEiI9E-Byv7c",
        // ]);
//        $url = "http://192.168.6.10:8100/ws/live2.php";
        $url = 'https://siakad.nobel.ac.id/public/wilayah.json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = [
            'act' => "GetWilayah",
            'token' => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZF9wZW5nZ3VuYSI6ImNjYWE4ZGIxLTQyZDQtNGYzNy04OTJlLTk2M2U2ZDI0MmQ4ZCIsInVzZXJuYW1lIjoiaXFiYWwuMDkyMDIxIiwibm1fcGVuZ2d1bmEiOiJJcWJhbCBBemlzIiwidGVtcGF0X2xhaGlyIjoiUGFyZS1wYXJlIiwidGdsX2xhaGlyIjoiMTk4MC0wNi0yNFQxNjozMDowMC4wMDBaIiwiamVuaXNfa2VsYW1pbiI6IkwiLCJhbGFtYXQiOiJKbC4gTWFjY2luaSBHdXN1bmcgTm8uIDY1IiwieW0iOiJpcWJhbEBub2JlbC5hYy5pZCIsInNreXBlIjoiIiwibm9fdGVsIjoiIiwiYXBwcm92YWxfcGVuZ2d1bmEiOiIxIiwiYV9ha3RpZiI6IjEiLCJ0Z2xfZ2FudGlfcHdkIjpudWxsLCJpZF9zZG1fcGVuZ2d1bmEiOm51bGwsImlkX3BkX3BlbmdndW5hIjpudWxsLCJpZF93aWwiOiIxOTYwMDAgICIsImxhc3RfdXBkYXRlIjoiMjAyMS0wOS0yM1QwMzoxNTowOS43NzdaIiwic29mdF9kZWxldGUiOiIwIiwibGFzdF9zeW5jIjoiMjAyMy0wNS0wOVQxMjowMzo0MS41NTNaIiwiaWRfdXBkYXRlciI6ImNjYWE4ZGIxLTQyZDQtNGYzNy04OTJlLTk2M2U2ZDI0MmQ4ZCIsImNzZiI6IjAiLCJ0b2tlbl9yZWciOm51bGwsImphYmF0YW4iOm51bGwsInRnbF9jcmVhdGUiOiIyMDIxLTA5LTIyVDA2OjAyOjUzLjkyMFoiLCJpZF9wZXJhbiI6Mywibm1fcGVyYW4iOiJBZG1pbiBQVCIsImlkX3NwIjoiMjAyZDhjNzAtYWZjMS00OWY1LWI3MzUtYTMxZTMxM2ZmYjYyIiwiaWF0IjoxNjg1MTU0NjMyLCJleHAiOjE2ODUxNTY0MzJ9.R1YuXoFcQ8JbVrGpveyFI8r-5MVgREJrcqP7I-h2F0E",
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json"
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);


        // FILTER NEGARA INDONESIA ONLY
        $negara = collect($response->data)->filter(function($item){ return $item->id_negara === 'ID'; })->values();
        // LOOPING THROUGH NEGARA INDONESIA
        return $negara->map(function($item){
            $kodeWilayah = trim($item->id_wilayah);
            $dataToStore = [];

              // KECAMATAN
              $dataToStore['id_wil'] = $kodeWilayah;
              $dataToStore['nm_wil'] = trim($item->nama_wilayah);
              $dataToStore['status_error'] = 0;
              $dataToStore['id_induk_wilayah'] = substr($kodeWilayah,0,4)."00";
              $dataToStore['id_level_wil'] = 3;

            // JIKA KABUPATEN
            if(substr($kodeWilayah,-2) === "00"){
                $dataToStore['id_induk_wilayah'] = substr($kodeWilayah,0,2)."0000";
                $dataToStore['id_level_wil'] = 2;
            }

            // JIKA PROVINSI
            if(substr($kodeWilayah,-4) === "0000"){
                $dataToStore['id_induk_wilayah'] = "000000";
                $dataToStore['id_level_wil'] = 1;
            }

            // JIKA NEGARA
            if($kodeWilayah === "000000"){
                $dataToStore['id_induk_wilayah'] = null;
                $dataToStore['id_level_wil'] = 0;
            }

            // ENABLE THIS TO UPDATE THE DATABASE
            // DB::table('wilayah')->updateOrCreate(['id_wil' => $dataToStore['id_wil']],$dataToStore);

            // USE THIS IF THE ABOVE METHOD NOT WORKING
            $wilayah = DB::table('wilayah')->where('id_wil',$kodeWilayah)->first();
            if($wilayah){
               DB::table('wilayah')->where('id_wil',$kodeWilayah)->update($dataToStore);
            }else{
               DB::table('wilayah')->insert($dataToStore);
            }
            return $dataToStore;
        });


        return $result;

    }
}
