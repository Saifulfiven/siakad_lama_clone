<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Seminar, App\SeminarFile, App\Ujianakhir;
use Sia, DB, Response, Rmt, Carbon;

class SeminarController extends Controller
{
  public function index(Request $r)
  {

    /* 
        ALGORITMA
        
        Telah seminar?
            Y => msg
            N => has_skripsi
                    YA => Cek apakah telah seminar
                        NO => Cek apakah telah mendaftar seminar
                                        NO => Apakah seminar tersedia
                                            Y => Tombol pendaftaran
                                            T => Pesan
                                        YA => View data-pendaftaran
                            Y => Pesan telah selesai
                        Y => Pesan belum bisa mendaftar
        */

    $id_mhs_reg     = Sia::sessionMhs();
    $has_skripsi    = false;
    $telah_seminar  = false;

    // Jenis
    // 1. Proposal
    // 2. Hasil
    // 3. Tesis/Skripsi
    $jenis = $r->jenis ? $r->jenis : 'P';

    // Pengecekan nilai seminar di semua semester
    // Bisa saja dia telah mendapatkan nilai di semester sebelumnya
    $nilai = DB::table('penguji as p')
      ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
      ->where('p.id_mhs_reg', $id_mhs_reg)
      ->where('p.jenis', $jenis)
      ->sum('p.nilai');

    if ($nilai) {

      if ($nilai > 0) {
        // Telah ujian seminar
        $telah_seminar = true;
      } else {
        // Belum ujian seminar
        $telah_seminar = false;
      }
    } else {
      // Belum seminar
      $telah_seminar = false;
    }

    // Cek apakah mhs memprogram skripsi/tesis
    $rule_1 = $this->cekKrsTesis($id_mhs_reg, Sia::sessionPeriode());
    // dd($rule_1);
    if ($rule_1 > 0) {
      $has_skripsi = true;
    }

    $data['has_skripsi']    = $has_skripsi;
    $data['jenis']          = $jenis;
    $data['telah_seminar']  = $telah_seminar;
    $data['seminar_tersedia'] = $this->seminarTersedia($id_mhs_reg);

    $data['seminar'] = Seminar::where('id_mhs_reg', Sia::sessionMhs())
      ->where('jenis', $jenis)->first();

    $data['file_seminar'] = [];

    return view('mahasiswa-member.seminar.index', $data);
  }

  public function seminarTersedia($id_mhs_reg)
  {
    // $jenis
    // Kode: P,H,S 

    $seminar_tersedia = 1;

    // PENGECEKAN SEMINAR YANG BELUM DIJALANI
    foreach (Sia::jenisSeminar() as $key => $val) {

      // Pengecekan nilai seminar
      $nilai = DB::table('penguji as p')
        ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
        ->where('p.id_mhs_reg', $id_mhs_reg)
        ->where('p.jenis', $key)
        ->sum('p.nilai');

      if ($nilai > 0) {
        continue;
      } else {
        $seminar_tersedia = $key;
        break;
      }
    }

    return $seminar_tersedia;
  }

  public function cekKrsTesis($id_mhs_reg, $smt)
  {
    $query = DB::table('nilai as n')
      ->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg', '=', 'm1.id')
      ->leftJoin('mahasiswa as m2', 'm1.id_mhs', '=', 'm2.id')
      ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
      ->leftJoin('mk_kurikulum as mkur', 'mkur.id', '=', 'jdk.id_mkur')
      ->leftJoin('matakuliah as mk', 'mkur.id_mk', '=', 'mk.id')
      ->where('mk.ujian_akhir', 'H')
      ->where('n.id_mhs_reg', $id_mhs_reg)
      ->where('jdk.id_smt', $smt)
      ->count();

    // dd(DB::table('nilai as n')
    // ->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg','=','m1.id')
    // ->leftJoin('mahasiswa as m2', 'm1.id_mhs','=','m2.id')
    // ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
    // ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
    // ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
    // ->where('mk.ujian_akhir', 'H')
    // ->where('n.id_mhs_reg', $id_mhs_reg)
    // ->where('jdk.id_smt', $smt)
    // ->count());

    return $query;
  }

  public function store(Request $r)
  {
    $this->validate($r, [
      'file' => 'required|max:1024',
      'jenis_file' => 'required'
    ]);

    try {

      $id_mhs_reg = Sia::sessionMhs();

      DB::beginTransaction();

      // Cek dahulu apakah file telah ada (hanya untuk jenis pembayaran)
      if ($r->jenis_file == 'pembayaran') {

        $ekstArr = ['png', 'jpg', 'jpeg', 'pdf'];

        $cek = SeminarFile::where('jenis_file', $r->jenis_file)
          ->where('id_seminar', $r->id)
          ->first();

        if (empty($cek)) {

          $data = new SeminarFile;
          $data->id_seminar = $r->id;
          $data->jenis_file = $r->jenis_file;
        } else {
          $data = SeminarFile::find($cek->id);
        }
      } else {

        $ekstArr = ['docx', 'pdf', 'xls', 'xlsx', 'doc'];

        $data = new SeminarFile;
        $data->id_seminar = $r->id;
        $data->jenis_file = $r->jenis_file;
      }

      if ($r->hasFile('file')) {

        $ekstensi = $r->file->getClientOriginalExtension();

        if (!in_array($ekstensi, $ekstArr)) {
          return Response::json(['Jenis file yang diperbolehkan adalah (' . implode(',', $ekstArr) . ')'], 422);
        }

        $nama_nim   = Sia::sessionMhs('nim') . '-' . str_slug(Sia::sessionMhs('nama'));

        $fileName   = $nama_nim . ' - ' . $r->jenis_file . ' - ' . time() . '.' . strtolower($ekstensi);
        $path   = config('app.file-seminar') . '/' . Sia::sessionMhs('nim');
        $upload = $r->file->move($path, $fileName);

        $data->file = $fileName;

        if (empty($r->ket)) {
          $data->ket = pathinfo($r->file->getClientOriginalName(), PATHINFO_FILENAME);
        } else {
          $data->ket = $r->ket;
        }

        $data->save();

        DB::commit();

        Rmt::success('Upload berhasil');

        return Response::json(['OK']);
      } else {

        DB::rollback();
        return Response::json(['File tidak terbaca, mohon ulangi lagi'], 422);
      }
    } catch (\Exception $e) {

      DB::rollback();
      return Response::json([$e->getMessage()], 422);
    }
  }

  public function storeAjuan(Request $r)
  {
    $this->validate($r, [
      'tanggal' => 'required',
      'pukul_1' => 'required',
      'pukul_2' => 'required'
    ]);

    $pukul = Rmt::pukul($r->pukul_1) . ' - ' . Rmt::pukul($r->pukul_2);

    if ($r->pukul_1 > $r->pukul_2) {
      return Response::json(['Jam mulai lebih besar dari jam selesai'], 422);
    }

    $mulai = Carbon::parse($r->tanggal . ' ' . Rmt::pukul($r->pukul_1));
    $end = Carbon::parse($r->tanggal . ' ' . Rmt::pukul($r->pukul_2));

    $totalDuration = $end->diffInMinutes($mulai);

    if ($totalDuration > 60) {
      return Response::json(['Waktu seminar tidak boleh lebih dari 60 Menit'], 422);
    }

    // Cek penguji yg tabrakan jam
    foreach ($r->penguji as $id_dosen) {
      $cek = DB::table('ujian_akhir as ua')
        ->join('penguji as p', function ($join) {
          $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
          $join->on('ua.jenis', '=', 'p.jenis');
        })
        ->where('ua.pukul', $pukul)
        ->where('ua.tgl_ujian', $r->tgl)
        ->where('ua.id_smt', Sia::sessionPeriode())
        ->where('p.id_dosen', $id_dosen)
        ->where('ua.id_mhs_reg', '<>', Sia::sessionMhs())
        ->count();

      if ($cek > 0) {

        $dosen = DB::table('dosen')->where('id', $id_dosen)->first();
        return Response::json([$dosen->nm_dosen . ' menguji pada tanggal & jam tersebut.'], 422);
      }
    }

    $data = Ujianakhir::findOrFail($r->id);
    $data->tgl_ujian = $r->tanggal;
    $data->pukul = $pukul;
    $data->save();

    Rmt::success('Berhasil menyimpan ajuan jadwal, tunggu hingga penguji menyetujui jadwal yang anda ajukan');
  }

  public function deleteFile(Request $r)
  {
    $data = SeminarFile::findOrFail($r->id);

    $file = config('app.file-seminar') . '/' . Sia::sessionMhs('nim') . '/' . $data->file;
    Rmt::unlink($file);

    $data->delete();

    Rmt::success('Berhasil menghapus data');
    return redirect()->back();
  }
}
