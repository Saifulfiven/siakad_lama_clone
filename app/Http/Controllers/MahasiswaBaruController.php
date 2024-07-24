<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use App\Mahasiswareg;
use App\User;
use Carbon;
use DB;
use Illuminate\Http\Request;
use Image;
use Ramsey\Uuid\Uuid;
use Response;
use Rmt;
use Session;
use Sia;
use Storage;
use Illuminate\Support\Facades\Log;

class MahasiswaBaruController extends Controller
{

    public function index(Request $r)
    {
        $prodi_user = Sia::getProdiUser();
        if (!in_array('61101', $prodi_user)) {
            echo '<center><h4>Maaf, untuk saat ini Prodi anda belum bisa menggunakan fitur ini</h4></center>';
            echo '<center><a href="/">Kembali ke Dashboard</a></center>';
            exit;
        }

        $semester = !empty($r->smt) ? $r->smt : Sia::sessionPeriode();

        if (!Session::has('maba.smt')) {
            Session::put('maba.smt', $semester);
        }

        $maba = DB::connection('mysql2')
            ->table('pendaftar');

        $this->filter($maba);

        $data['maba'] = $maba->paginate(20);

        return view('maba.index', $data);
    }

    public function cari(Request $r)
    {
        if (!empty($r->cari)) {
            Session::put('maba.cari', $r->cari);
        } else {
            Session::pull('maba.cari');
        }

        return redirect(route('maba'));
    }

    private function filter($query)
    {
        if (Session::has('maba.cari')) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . Session::get('maba.cari') . '%');
            });
        }

        if (Session::has('maba.kelas')) {
            $query->where('jenis_kelas', Session::get('maba.kelas'));
        }

        if (Session::has('maba.smt')) {
            $semester = Session::get('maba.smt');

            $thn = substr($semester, 0, 4);
            $smstr = substr($semester, 4, 1);
            $smstr = $smstr == 1 ? 'GANJIL' : 'GENAP';

            $query->where('ta', $thn)->where('semester', $smstr);
        }

        if (Session::has('maba.status')) {
            if (Session::get('maba.status') == 'SELESAI') {
                $query->whereIn('id',
                    DB::table('mahasiswa_reg')
                        ->where('semester_mulai', Session::get('maba.smt'))
                        ->pluck('id_maba'));
            } else {
                $query->whereNotIn('id',
                    DB::table('mahasiswa_reg')
                        ->where('semester_mulai', Session::get('maba.smt'))
                        ->pluck('id_maba'));
            }
        }

        return $query;
    }

    public function setFilter(Request $r)
    {
        if (!empty($r->modul)) {

            if ($r->val == 'all') {
                Session::pull('maba.' . $r->modul);
            } else {
                Session::put('maba.' . $r->modul, $r->val);
            }
        }

        return redirect(route('maba'));
    }

    public function add(Request $r)
    {

    }

    public function impor(Request $r)
    {
        try {
            $maba = DB::connection('mysql2')
                ->table('pendaftar')
                ->where('id', $r->id)
                ->first();

            if (empty($maba)) {
                return Response::json(['error' => 1, 'msg' => 'Data tidak ditemukan']);
            }

            $cek = Mahasiswareg::where('id_maba', $maba->id)->count();
            if ($cek > 0) {
                return Response::json(['error' => 1, 'msg' => 'Mahasiswa ini telah ada']);
            }
            $cekTest = DB::transaction(function () use ($maba, $r) {
                 $this->storeMaba($maba, $r->smt, 'http://pmbpasca.stienobel-indonesia.ac.id/');
            });

            Log::warning($cekTest);

        } catch (\Exception $e) {
            Log::info($e);
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }

        return Response::json(['error' => 0, 'msg' => '']);

    }

    public function imporMassal(Request $r)
    {
        // Log::error("message");
        // return $r;
        try {
            $count = count($r->nama);

            if ($count == 0) {
                return Response::json(['error' => 1, 'msg' => 'Tidak menemukan data']);
            }

            $errors = [];
            $success = [];

            for ($i = 0; $i < $count; $i++) {

                $maba = DB::connection('mysql2')
                    ->table('pendaftar')
                    ->where('id', $r->id[$i])
                    ->first();

                if (empty($maba)) {
                    $errors[] = ['nama' => $r->nama, 'ket' => 'Tidak ditemukan'];
                    continue;
                }

                $cek = Mahasiswareg::where('id_maba', $maba->id)->count();

                if ($cek > 0) {
                    continue;
                }

                $cekTest = DB::transaction(function () use ($maba, $r) {
                    $this->storeMaba($maba, $r->smt, 'http://pmbpasca.stienobel-indonesia.ac.id/');
                });

                // Log::warning($cekTest);

                $success[] = 1;
            }

        } catch (\Exception $e) {
          Log::error($e);
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }

        if (count($success) > 0 || count($errors) > 0) {
            Session::put('import', ['errors' => $errors, 'success' => $success]);
        }

        return Response::json(['error' => 0, 'msg' => '']);
    }

    private function storeMaba($maba, $smt, $url)
    {
        $nim = Sia::generateNim($maba->prodi);
        $semester = $maba->semester == 'GANJIL' ? 1 : 2;
        $kurikulum = DB::table('kurikulum')
            ->where('id_prodi', $maba->prodi)
            ->orderBy('mulai_berlaku', 'desc')->first();

        Log::warning(json_encode($nim));

        $data = [
            'id_user' => Rmt::uuid(),
            'nim' => $nim,
            'id_mhs' => Rmt::uuid(),
            'id_mhsreg' => Rmt::uuid(),
            'smt' => $smt,
            'kurikulum' => $kurikulum->id,
            'email' => empty($maba->email) ? $nim . '@stienobel-indonesia.ac.id' : $maba->email,
        ];

        $foto = '';

        if (!empty($maba->foto)) {
            // $this->uploadFoto($maba->foto, $nim, $url);
        }

        $user = new User;
        $user->id = $data['id_user'];
        $user->nama = $maba->nama;
        $user->username = $data['nim'];
        $user->email = $data['email'];
        $user->password = bcrypt(Carbon::parse($maba->tgl_lahir)->format('dmY'));
        $user->level = 'mahasiswa';
        $imp1 = $user->save();

        $mhs = new Mahasiswa;
        $mhs->id = $data['id_mhs'];
        $mhs->id_user = $data['id_user'];
        $mhs->nm_mhs = $maba->nama;
        $mhs->jenkel = $maba->jenkel == 'WANITA' ? 'P' : 'L';
        $mhs->nik = $maba->ktp;
        $mhs->tempat_lahir = $maba->tempat_lahir;
        $mhs->tgl_lahir = Carbon::parse($maba->tgl_lahir)->format('Y-m-d');
        $mhs->id_agama = $maba->agama;
        $mhs->alamat = $maba->alamat;
        $mhs->des_kel = $maba->kelurahan;
        $mhs->id_wil = $maba->kecamatan;
        $mhs->hp = $maba->hp;
        $mhs->email = $data['email'];
        $mhs->kewarganegaraan = 'ID';
        $mhs->nm_sekolah = $maba->prodi == '61101' || $maba->prodi == '61112' || $maba->prodi == '61113' ? $maba->nm_pt : $maba->slta;
        $mhs->nm_ibu = $maba->ibu;
        $mhs->nm_ayah = $maba->ayah;
        $mhs->foto_mahasiswa = $foto;
        $mhs->id_info_nobel = empty($maba->info_nobel) ? null : $maba->info_nobel;
        $imp2 = $mhs->save();

        $mhs2 = new Mahasiswareg;
        $mhs2->id = $data['id_mhsreg'];
        $mhs2->id_prodi = $maba->prodi;
        $mhs2->id_konsentrasi = null;
        $mhs2->id_mhs = $data['id_mhs'];
        $mhs2->jenis_daftar = 1;
        $mhs2->nim = $data['nim'];
        $mhs2->semester_mulai = Sia::sessionPeriode();
        $mhs2->tgl_daftar = Carbon::now()->format('Y-m-d');
        $mhs2->id_kurikulum = $data['kurikulum'];
        $mhs2->id_maba = $maba->id;
        $mhs2->kelas = $maba->jenis_kelas;
        $mhs2->id_jenis_pembiayaan = 1;
        $mhs2->biaya_masuk = 4500000;
        $imp3 = $mhs2->save();

        // dd($imp1, $imp2, $imp3);
    }

    private function uploadFoto($foto, $nim, $url)
    {
        $url = $url . "resources/assets/pmb/front/images/foto/'.$foto";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        curl_close($ch);

        $ori = substr($url, strrpos($url, '/') + 1);
        $ekstensi = substr($ori, strrpos($ori, '.'));
        $fileName = $nim . $ekstensi;
        Storage::disk('mhs')->put($fileName, $output);

        $this->generateThumb($fileName);
    }

    private function generateThumb($imageName)
    {
        // Generate thumbnail
        $img = Image::make(storage_path() . '/foto-mahasiswa/' . $imageName);
        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save(storage_path() . '/foto-mahasiswa/thumb/' . $imageName);
    }

    public function delete($id)
    {
        DB::connection('mysql2')
            ->table('pendaftar')
            ->where('id', $id)
            ->delete();

        Rmt::success('Berhasil menghapus data');

        return redirect()->back();
    }
}
